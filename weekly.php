<?php
	/**
	 * Defining a couple of terms and some helper functions
	 */
	const BASE = __DIR__;
	const DS = DIRECTORY_SEPARATOR;
	$process_start = time();

	// Setting up find/replace for article headlines since Excel doesn't always play nice with diacriticals
	$find = [ '…', '’', 'ó', 'é', '–', '“', '”', '‘', 'í', 'Ø', 'ú', 'è', 'á', 'ï', 'ñ', 'ä', 'á', 'ö', 'ë', 'è' ];
	$replace = [ '...', '\'', 'o', 'e', '--', '"', '"', '\'', 'i', 'o', 'u', 'e', 'a', 'i', 'n', 'a', 'a', 'o', 'e', 'e' ];

	// Recursive array search
	function in_array_r( $needle, $haystack, $strict = false ): bool {
		foreach ( $haystack as $item ) {
			if ( ( $strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && in_array_r( $needle, $item, $strict ) ) ) {
				return true;
			}
		}
		return false;
	}

	function csv_int_check ( $index, $data, $head ): int|string {
		if ( str_contains( $index, 'Demographics' ) ) {
			return ( empty( $data[ $head[ $index ] ] ) ? '0%' : ($data[ $head[ $index ] ] * 100).'%' );
		} else {
			return ( empty( intval( $data[ $head[ $index ] ] ) ) ? 0 : intval( $data[ $head[ $index ] ] ) );
		}
	}

	// Read keyboard input from terminal during execution
	function read_stdin(): string {
		$fr = fopen( "php://stdin", "r" );
		$input = fgets( $fr, 128 );
		$input = rtrim( $input );
		fclose ( $fr );
		return $input;
	}

	/**
	 * Load the composer dependencies and my terminal color helper
	 */
	require BASE . DS . 'vendor' . DS . 'autoload.php';
	include( BASE . DS . 'colors.php' );
	include( BASE . DS . 'notif.php' );

	/**
	 * Expose global env() function from oscarotero/env
	 */
	Env::init();

	/**
	 * Use Dotenv to set required environment variables and load .env file in root
	 */
	$dotenv = new Dotenv\Dotenv( BASE );
	if ( file_exists( BASE . DS . '.env' ) ) {
		$dotenv->load();
		$dotenv->required([ 'GA_CLIENT', 'YT_ACCESS', 'YT_CLIENT', 'YT_CHANNEL_ID', 'CLIENT_EMAILS', 'CF_DISTRO', 'TIMEZONE', 'FB_PAGE_ID', 'FB_PAGE_ACCESS', 'FB_PAGE_SECRET', 'AWS_KEY', 'AWS_SECRET', 'INSTAGRAM_ID', 'WCM_USER', 'WCM_PASSWORD', 'FROM_EMAIL', 'APP_URL', 'S3_BUCKET', 'STORAGE_PATH', 'STORAGE_SHARE' ]);
	}

	// Map all of the variables from .env to constants and variables
	date_default_timezone_set( env('TIMEZONE') );
	define( 'GA_CLIENT', BASE . DS . env( 'GA_CLIENT' ) );
	define( 'YT_ACCESS', BASE . DS . env( 'YT_ACCESS' ) );
	define( 'YT_CLIENT', BASE . DS . env( 'YT_CLIENT' ) );
	define( 'YT_CHANNEL_ID', env( 'YT_CHANNEL_ID' ) );
	define( 'GA_COMBO', env( 'GA_COMBO' ) );
	define( 'GA4_PROPERTY', env( 'GA4_PROPERTY' ) );
	define( 'WCM_USER', env( 'WCM_USER' ) );
	define( 'WCM_PASSWORD', env( 'WCM_PASSWORD' ) );
	define( 'APP_URL', env( 'APP_URL' ) );
	define( 'FROM_EMAIL', env( 'FROM_EMAIL' ) );
	define( 'STORAGE_PATH', env( 'STORAGE_PATH' ) );
	define( 'STORAGE_SHARE', env( 'STORAGE_SHARE' ) );
	$s3_bucket = env( 'S3_BUCKET' );
	$cf_distro = env( 'CF_DISTRO' );
	$email_arr = explode( ',', env( 'CLIENT_EMAILS' ) );
	$hpm_fb = env( 'FB_PAGE_ID' );
	$fb_access = env( 'FB_PAGE_ACCESS' );
	$fb_secret = env( 'FB_PAGE_SECRET' );
	$fb_proof = hash_hmac( 'sha256', $fb_access, $fb_secret );
	$hpm_insta = env( 'INSTAGRAM_ID' );
	$aws_key = env( 'AWS_KEY' );
	$aws_secret = env( 'AWS_SECRET' );

	// Initialize PHPSpreadsheet software so we can create XLSX files
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	$spreadsheet = new Spreadsheet();

	// The raw array element that all of our spreadsheets will populate from
	$sheets = [];

	/**
	 * The data structure for the JSON file that will underpin the charts in the included application
	 * The chart names can be changed, but you will need to update the names in the charting application
	 */
	$graphs = [];
	include( BASE . DS . 'graphs.php' );

	/**
	 * Set up data for our various interactive checks
	 * All of these checks are set up as while loops for input checking
	 * If the user doesn't enter anything into the prompt, or doesn't stick to the pattern, it prints an error and prompts again
	 */

	$date_conf = $emails = $email_conf = $rerun = $rerun_conf = false;
	$startu = $endu = $run_date = 0;
	$start = $end = '';

	/**
	 * Set the end date for your report
	 * Typically, I'm running these on a Monday with the intent to cover the previous Monday through Sunday
	 */
	while ( $date_conf == false ) {
		if ( count( $argv ) === 1 ) {
			echo "Enter the end date for your report (YYYY-MM-DD), or hit enter to set it as today: ";
			$date_in = read_stdin();
		} else {
			$date_in = $argv[1];
		}
		$run_date = time();
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date_in ) ) {
			$datex = explode( '-', $date_in );
			if (
				intval( $datex[0] ) <= date( 'Y' ) &&
				( intval( $datex[1] ) > 0 && intval( $datex[1] ) <= 12 ) &&
				( intval( $datex[2] ) > 0 && intval( $datex[2] < 32 ) )
			) {
				$run_date = mktime( 12, 0, 0, intval( $datex[1] ), intval( $datex[2] ), intval( $datex[0] ) );
				$date_conf = true;
			} else {
				echo FG_BR_RED . BG_BLACK . FS_BOLD . "Invalid date input, please try again." . RESET_ALL . PHP_EOL;
			}
		} elseif ( empty( $date_in ) ) {
			$date_conf = true;
		} else {
			echo FG_BR_RED . BG_BLACK . FS_BOLD . "Invalid date input, please try again." . RESET_ALL . PHP_EOL;
			if ( count( $argv ) === 1 ) {
				die;
			}
		}
		/**
		 * Setting the start and end dates in both YYYY-MM-DD format and Unixtime
		 */
		$suw = $run_date - ( 60 * 60 * 24 * 7 );
		$startu = mktime( 0, 0, 0, date( 'm', $suw ), date( 'd', $suw ), date( 'y', $suw ) );

		$start = date( 'Y-m-d', $startu );
		$endu = mktime( 0, 0, 0, date( 'm', $run_date ), date( 'd', $run_date ), date( 'Y', $run_date ) );
		$end = date( 'Y-m-d', $endu );
	}

	/**
	 * Debug rerun CLAP CLAP CLAPCLAPCLAP
	 * Saying yes to this will assume some things:
	 * 		-- You don't want to email the group
	 * 		-- You don't want to update the report list for the graphing app
	 */
	while ( $rerun_conf == false ) {
		if ( count( $argv ) === 1 ) {
			echo "Are you rerunning this report because something screwed up? (y/n) ";
			$rerun_in = read_stdin();
		} else {
			$rerun_in = $argv[2];
		}
		if ( $rerun_in == 'y' ) {
			$rerun_conf = true;
			$rerun = true;
			$email_conf = true;
		} elseif ( $rerun_in == 'n' ) {
			$rerun_conf = true;
		} else {
			echo FG_BR_RED . BG_BLACK . FS_BOLD . "Invalid input, please try again." . RESET_ALL . PHP_EOL;
			if ( count( $argv ) === 1 ) {
				die;
			}
		}
	}

	/**
	 * Choose whether or not to email the group. Also useful for testing
	 */
	while ( $email_conf == false ) {
		if ( count( $argv ) === 1 ) {
			echo "Do you want this report emailed to the group? (y/n) ";
			$email_in = read_stdin();
		} else {
			$email_in = $argv[3];
		}
		if ( $email_in == 'y' ) {
			$email_conf = true;
			$emails = true;
		} elseif ( $email_in == 'n' ) {
			$email_conf = true;
		} else {
			echo FG_BR_RED . BG_BLACK . FS_BOLD . "Invalid input, please try again." . RESET_ALL . PHP_EOL;
			if ( count( $argv ) === 1 ) {
				die;
			}
		}
	}

	/**
	 * Twitter and Apple News don't have publicly accessible analytics APIs
	 * So, I've been downloading reports from both and saving them in their respective folders
	 * In order to facilitate past reports, all of the CSVs should have the report date in the filename
	 * Example: if you entered '2018-08-27' as the end date for your report, then Apple News data should
	 * 		be in 'channel-2018-08-27.csv'
	 *
	 * One more note: if you look in the Twitter parser, there is reference to a 'graphs-YYYY-MM-DD.json' file
	 * 		That can be found by using the web inspector in the Twitter Analytics console, looking for the
	 * 		'account_stats.json' request in the Network panel (it will be made when you set the date range), and
	 * 		copy/pasting it into a file. I had a chunk of code that would use cURL requests to mimic a login
	 * 		to Twitter Analytics and download the relevant files, but after locking the station Twitter account
	 * 		for the 5th time or so, I abandoned it
	 */
	if ( $run_date < mktime( 0, 0, 0, 6, 16, 2024 ) ) {
		if (
			!file_exists( BASE . DS . "podcasts" . DS . "podcasts-" . $end . ".csv" ) ||
			!file_exists( BASE . DS . "twitter" . DS . "tweets" . DS . "tweets-" . $end . ".csv" ) ||
			!file_exists( BASE . DS . "apple" . DS . "channel-" . $end . ".csv" ) ||
			!file_exists( BASE . DS . "twitter" . DS . "graphs" . DS . "graphs-" . $end . ".json" )
		) {
			echo PHP_EOL . FG_BR_RED . BG_BLACK . FS_BOLD . "You are missing one of your manual reports. Please check the Twitter, Apple, and Podcasts folders." . PHP_EOL;
			die;
		}
	} else {
		if (
			!file_exists( BASE . DS . "podcasts" . DS . "podcasts-" . $end . ".csv" ) ||
			!file_exists( BASE . DS . "apple" . DS . "channel-" . $end . ".csv" ) ||
			!file_exists( BASE . DS . "twitter" . DS . "x-stats" . DS . $end . ".json" )
		) {
			echo PHP_EOL . FG_BR_RED . BG_BLACK . FS_BOLD . "You are missing one of your manual reports. Please check the X, Apple, and Podcasts folders." . PHP_EOL;
			die;
		}
	}

	echo FG_BR_CYAN . BG_BLACK . FS_BOLD ."Hold please..." . RESET_ALL . PHP_EOL;

	$num = 20;

	// Facebook Graph API base
	$fb_base = 'https://graph.facebook.com/v22.0/';

	// Where the magic happens
	if ( file_exists( GA_CLIENT ) ) {
		if ( $endu > mktime( 0, 0, 0, 5, 30, 2023 ) ) {
			require BASE . DS . 'google' . DS . 'google-ga4.php';
		} else {
			require BASE . DS . 'google' . DS . 'google.php';
		}
	}

	if ( !empty( $fb_access ) && !empty( $hpm_fb ) ) {
		require BASE . DS . 'facebook' . DS . 'facebook.php';
	}

	if ( !empty( $fb_access ) && !empty( $hpm_insta ) ) {
		require BASE . DS . 'facebook' . DS . 'instagram.php';
	}

	if ( $run_date < mktime( 0, 0, 0, 6, 16, 2024 ) ) {
		require BASE . DS . 'twitter' . DS . 'twitter.php';
	} else {
		require BASE . DS . 'twitter' . DS . 'x.php';
	}

	if ( !empty( WCM_USER ) ) {
		require BASE . DS . 'triton' . DS . 'triton.php';
	}

	require BASE . DS . 'google' . DS . 'youtube.php';
	require BASE . DS . 'podcasts' . DS . 'podcasts.php';
	require BASE . DS . 'apple' . DS . 'apple.php';

	// Flipping the sheet array so Google Analytics stats are first
	$rsheets = array_reverse( $sheets );

	// Write sheets from array into XLSX file
	foreach ( $rsheets as $k => $v ) {
		$myWorkSheet = new Worksheet( $spreadsheet, $k );
		// Setting the highlight color for the worksheet tabs in Excel
		if ( str_contains( $k, 'Facebook' ) ) {
			$myWorkSheet->getTabColor()->setRGB('3b5998');
		} elseif ( str_contains( $k, '(Combined)' ) || str_contains( $k, 'Top Stories' ) ) {
			$myWorkSheet->getTabColor()->setRGB('db4437');
		} elseif ( str_contains( $k, 'Tweet' ) || str_contains( $k, 'Twitter' ) || str_contains( $k, 'X' ) ) {
			$myWorkSheet->getTabColor()->setRGB('1da1f2');
		} elseif ( str_contains( $k, 'Instagram' ) ) {
			$myWorkSheet->getTabColor()->setRGB('8a3ab9');
		} elseif ( str_contains( $k, 'Triton' ) ) {
			$myWorkSheet->getTabColor()->setRGB('056ab2');
		} elseif ( str_contains( $k, 'YouTube' ) ) {
			$myWorkSheet->getTabColor()->setRGB('ff0000');
		} elseif ( str_contains( $k, 'Podcasts' ) ) {
			$myWorkSheet->getTabColor()->setRGB('808080');
		} elseif ( str_contains( $k, 'Apple' ) ) {
			$myWorkSheet->getTabColor()->setRGB('000000');
		}
		try {
			$spreadsheet->addSheet( $myWorkSheet, 0 );
			$spreadsheet->setActiveSheetIndexByName( $k );
		} catch ( \PhpOffice\PhpSpreadsheet\Exception $e ) {
			echo $e->getMessage() . PHP_EOL;
		}
		$spreadsheet->getActiveSheet()->fromArray( $v );

		// Merging some of the header rows for readability
		if ( $k == 'Triton By Device' ) {
			try {
				$spreadsheet->getActiveSheet()->mergeCells( 'A1:E1' );
			} catch ( \PhpOffice\PhpSpreadsheet\Exception $e ) {
				echo $e->getMessage() . PHP_EOL;
			}
		}
		if ( str_contains( $k, 'Top Stories' ) ) {
			try {
				$spreadsheet->getActiveSheet()->mergeCells( 'A1:E1' );
				$spreadsheet->getActiveSheet()->mergeCells( 'F1:G1' );
				$spreadsheet->getActiveSheet()->mergeCells( 'H1:M1' );
				$spreadsheet->getActiveSheet()->mergeCells( 'N1:R1' );
			} catch ( \PhpOffice\PhpSpreadsheet\Exception $e ) {
				echo $e->getMessage() . PHP_EOL;
			}
		}
	}

	// Remove the default worksheet that is created
	try {
		$sheetIndex = $spreadsheet->getIndex(
			$spreadsheet->getSheetByName( 'Worksheet' )
		);
		$spreadsheet->removeSheetByIndex($sheetIndex);
	} catch ( \PhpOffice\PhpSpreadsheet\Exception $e ) {
		echo $e->getMessage() . PHP_EOL;
	}


	// Auto size columns for each worksheet
	foreach ( $spreadsheet->getWorksheetIterator() as $worksheet ) {
		try {
			$spreadsheet->setActiveSheetIndex( $spreadsheet->getIndex( $worksheet ) );
			$sheet = $spreadsheet->getActiveSheet();
			$cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells( true );
			foreach ( $cellIterator as $cell ) {
				$sheet->getColumnDimension( $cell->getColumn() )->setAutoSize( true );
			}
		} catch ( \PhpOffice\PhpSpreadsheet\Exception $e ) {
			echo $e->getMessage() . PHP_EOL;
		}
	}

	// Write XLSX file
	$writer =  new Xlsx( $spreadsheet );
	try {
		$writer->save( STORAGE_PATH . 'analytics-' . date( 'Y-m-d', $run_date ) . '.xlsx' );
	} catch ( \PhpOffice\PhpSpreadsheet\Writer\Exception $e ) {
		echo $e->getMessage() . PHP_EOL;
	}

	// Save the graphing data locally as a JSON file
	file_put_contents( BASE . DS . 'data' . DS . date( 'Y-m-d', $startu ) . '.json', json_encode( $graphs ) );

	/**
	 * Update the reports.json file with the name and date of this report
	 * This information will populate the dropdown in the graphing application
	 */
	$text = json_decode( file_get_contents( BASE . DS . 'data' . DS . 'reports.json' ) );
	if ( empty( $text ) ) {
		$text = [
			0 => [
				'text' => 'Week of '.date( 'F jS, Y', $startu ),
				'value' => date( 'Y-m-d', $startu )
			]
		];
	} else {
		$new_entry = [
			'text' => 'Week of '.date( 'F jS, Y', $startu ),
			'value' => date( 'Y-m-d', $startu )
		];
		$add = true;
		foreach ( $text as $t ) {
			if ( $t->text === $new_entry['text'] && $t->value === $new_entry['value'] ) {
				$add = false;
			}
		}
		if ( $add ) {
			array_unshift( $text, $new_entry );
		}
	}
	if ( ! $rerun ) {
		file_put_contents( BASE . DS . 'data' . DS . 'reports.json', json_encode( $text ) );
	}

	// Upload the file to S3, alert everyone via email, clear the Cloudfront cache
	if ( !empty( $aws_key ) ) {
		require BASE . DS . 'amazon.php';
	}

	// All done!
	$process_end = time();
	$process_total = ( $process_end - $process_start );
	echo FG_BR_GREEN . BG_BLACK . FS_BOLD . 'Report process completed successfully!' . PHP_EOL . 'Total execution time: ' . $process_total . ' seconds' . RESET_ALL . PHP_EOL;
	notification_toast( 'Total execution time: ' . $process_total, 'Weekly Analytics Report', 'Reporting process completed successfully', 'Crystal' );