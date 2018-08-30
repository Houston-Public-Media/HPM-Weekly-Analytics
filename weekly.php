<?php
	/**
	 * Defining a couple of terms and some helper functions
	 */
	define( 'BASE', __DIR__ );
	define( 'DS', DIRECTORY_SEPARATOR );

	// Setting up find/replace for article headlines since Excel doesn't always play nice with diacriticals
	$find = [ '…', '’', 'ó', 'é', '–', '“', '”', '‘', 'í', 'Ø', 'ú', 'è', 'á', 'ï', 'ñ', 'ä', 'á', 'ö', 'ë', 'è' ];
	$replace = [ '...', '\'', 'o', 'e', '--', '"', '"', '\'', 'i', 'o', 'u', 'e', 'a', 'i', 'n', 'a', 'a', 'o', 'e', 'e' ];

	// Recursive array search
	function in_array_r( $needle, $haystack, $strict = false ) {
		foreach ( $haystack as $item ) :
			if ( ( $strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && in_array_r( $needle, $item, $strict ) ) ) :
				return true;
			endif;
		endforeach;
		return false;
	}

	// Read keyboard input from terminal during execution
	function read_stdin() {
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
	require BASE . DS . 'colors.php';

	/**
	 * Expose global env() function from oscarotero/env
	 */
	Env::init();

	/**
	 * Use Dotenv to set required environment variables and load .env file in root
	 */
	$dotenv = new Dotenv\Dotenv( BASE );
	if ( file_exists( BASE . DS . '.env' ) ) :
		$dotenv->load();
		$dotenv->required([ 'GA_CLIENT', 'GDRIVE_CREDS', 'GDRIVE_TOKEN', 'YT_ACCESS', 'YT_CLIENT', 'CLIENT_EMAILS', 'CF_DISTRO', 'TIMEZONE', 'FB_PAGE_ID', 'FB_PAGE_ACCESS', 'FB_PAGE_PROOF', 'AWS_KEY', 'AWS_SECRET', 'INSTAGRAM_ACCESS', 'INSTAGRAM_PROOF', 'INSTAGRAM_ID', 'GDRIVE_PARENT', 'WCM_USER', 'WCM_PASSWORD', 'FROM_EMAIL', 'APP_URL', 'S3_BUCKET' ]);
	endif;

	// Map all of the variables from .env to constants and variables
	date_default_timezone_set( env('TIMEZONE') );
	define( 'GA_CLIENT', BASE . DS . env( 'GA_CLIENT' ) );
	define( 'GDRIVE_CREDS', BASE . DS . env( 'GDRIVE_CREDS' ) );
	define( 'GDRIVE_TOKEN', BASE . DS . env( 'GDRIVE_TOKEN' ) );
	define( 'YT_ACCESS', BASE . DS . env( 'YT_ACCESS' ) );
	define( 'YT_CLIENT', BASE . DS . env( 'YT_CLIENT' ) );
	define( 'GA_MAIN', env( 'GA_MAIN' ) );
	define( 'GA_AMP', env( 'GA_AMP' ) );
	define( 'GDRIVE_PARENT', env( 'GDRIVE_PARENT' ) );
	define( 'WCM_USER', env( 'WCM_USER' ) );
	define( 'WCM_PASSWORD', env( 'WCM_PASSWORD' ) );
	define( 'APP_URL', env( 'APP_URL' ) );
	define( 'FROM_EMAIL', env( 'FROM_EMAIL' ) );
	$s3_bucket = env( 'S3_BUCKET' );
	$cf_distro = env( 'CF_DISTRO' );
	$email_arr = explode( ',', env( 'CLIENT_EMAILS' ) );
	$hpm_fb = env( 'FB_PAGE_ID' );
	$page_access = env( 'FB_PAGE_ACCESS' );
	$page_proof = env( 'FB_PAGE_PROOF' );
	$hpm_insta = env( 'INSTAGRAM_ID' );
	$access = env( 'INSTAGRAM_ACCESS' );
	$proof = env( 'INSTAGRAM_PROOF' );
	$aws_key = env( 'AWS_KEY' );
	$aws_secret = env( 'AWS_SECRET' );

	// Initialize PHPSpreadsheet software so we can create XLSX files
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	$spreadsheet = new Spreadsheet();

	// The raw array element that all of our spreadsheets will populate from
	$sheets = [];

	/**
	 * The data structure for the JSON file that will underpin the charts in the included application
	 * The chart names can be changed, but you will need to update the names in the charting application
	 */
	$graphs = [
		'ga-main-articles' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Direct',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Google',
					'backgroundColor' => 'rgba(219,68,55,0.2 )',
					'borderColor' => 'rgba(219,68,55,1 )',
					'data' => []
				],
				2 => [
					'label' => 'Facebook',
					'backgroundColor' => 'rgba(59,89,152,0.2 )',
					'borderColor' => 'rgba(59,89,152,1 )',
					'data' => []
				],
				3 => [
					'label' => 'Twitter',
					'backgroundColor' => 'rgba(29,161,242,0.2 )',
					'borderColor' => 'rgba(29,161,242,1 )',
					'data' => []
				],
				4 => [
					'label' => 'Bing Search',
					'backgroundColor' => 'rgba(255,187,0,0.2 )',
					'borderColor' => 'rgba(255,187,0,1 )',
					'data' => []
				],
				5 => [
					'label' => 'Yahoo Search',
					'backgroundColor' => 'rgba(65,0,147,0.2 )',
					'borderColor' => 'rgba(65,0,147,1 )',
					'data' => []
				]
			]
		],
		'ga-main-hourly' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Users',
					'backgroundColor' => [ 'rgba( 0, 0, 255, 0.2 )' ],
					'borderColor' => [ 'rgba( 0, 0, 255, 1 )' ],
					'data' => []
				]
			]
		],
		'ga-main-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Users',
					'backgroundColor' => [],
					'data' => []
				]
			]
		],
		'ga-amp-articles' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Direct',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Google',
					'backgroundColor' => 'rgba(219,68,55,0.2 )',
					'borderColor' => 'rgba(219,68,55,1 )',
					'data' => []
				],
				2 => [
					'label' => 'Facebook',
					'backgroundColor' => 'rgba(59,89,152,0.2 )',
					'borderColor' => 'rgba(59,89,152,1 )',
					'data' => []
				],
				3 => [
					'label' => 'Twitter',
					'backgroundColor' => 'rgba(29,161,242,0.2 )',
					'borderColor' => 'rgba(29,161,242,1 )',
					'data' => []
				],
				4 => [
					'label' => 'Bing Search',
					'backgroundColor' => 'rgba(255,187,0,0.2 )',
					'borderColor' => 'rgba(255,187,0,1 )',
					'data' => []
				],
				5 => [
					'label' => 'Yahoo Search',
					'backgroundColor' => 'rgba(65,0,147,0.2 )',
					'borderColor' => 'rgba(65,0,147,1 )',
					'data' => []
				]
			]
		],
		'ga-amp-hourly' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Users',
					'backgroundColor' => [ 'rgba( 0, 0, 255, 0.2 )' ],
					'borderColor' => [ 'rgba( 0, 0, 255, 1 )' ],
					'data' => []
				]
			]
		],
		'ga-amp-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Users',
					'backgroundColor' => [],
					'data' => []
				]
			]
		],
		'facebook-impressions' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Total Reach',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Paid Impressions',
					'backgroundColor' => 'rgba(255,0,0,0.2 )',
					'borderColor' => 'rgba(255,0,0,1 )',
					'data' => []
				],
				2 => [
					'label' => 'Organic Impressions',
					'backgroundColor' => 'rgba(0,0,255,0.2 )',
					'borderColor' => 'rgba(0,0,255,1 )',
					'data' => []
				],
				3 => [
					'label' => 'Viral Impressions',
					'backgroundColor' => 'rgba(255,0,255,0.2 )',
					'borderColor' => 'rgba(255,0,255,1 )',
					'data' => []
				]
			]
		],
		'facebook-likes' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Page Likes',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'facebook-reactions' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Like',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Love',
					'backgroundColor' => 'rgba(255,0,0,0.2 )',
					'borderColor' => 'rgba(255,0,0,1 )',
					'data' => []
				],
				2 => [
					'label' => 'Wow',
					'backgroundColor' => 'rgba(0,0,255,0.2 )',
					'borderColor' => 'rgba(0,0,255,1 )',
					'data' => []
				],
				3 => [
					'label' => 'Haha',
					'backgroundColor' => 'rgba(255,0,255,0.2 )',
					'borderColor' => 'rgba(255,0,255,1 )',
					'data' => []
				],
				4 => [
					'label' => 'Sorry',
					'backgroundColor' => 'rgba(255,255,0,0.2 )',
					'borderColor' => 'rgba(255,255,0,1 )',
					'data' => []
				],
				5 => [
					'label' => 'Anger',
					'backgroundColor' => 'rgba(0,255,255,0.2 )',
					'borderColor' => 'rgba(0,255,255,1 )',
					'data' => []
				]
			]
		],
		'instagram-stats' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'New Followers',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Reach',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Profile Views',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				3 => [
					'label' => 'Impressions',
					'backgroundColor' => 'rgba( 255, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'twitter-tweets-by-impression' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Impressions',
					'backgroundColor' => 'rgba( 29,161,242, 0.2 )',
					'borderColor' => 'rgba( 29,161,242, 1 )',
					'data' => []
				]
			]
		],
		'twitter-tweets-by-engagement' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Retweets',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Replies',
					'backgroundColor' => 'rgba(0,0,255,0.2)',
					'borderColor' => 'rgba(0,0,255,1)',
					'data' => []
				],
				2 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba(255,0,0,0.2)',
					'borderColor' => 'rgba(255,0,0,1)',
					'data' => []
				],
				3 => [
					'label' => 'URL Clicks',
					'backgroundColor' => 'rgba(255,0,255,0.2)',
					'borderColor' => 'rgba(255,0,255,1)',
					'data' => []
				],
				4 => [
					'label' => 'Hashtag Clicks',
					'backgroundColor' => 'rgba(0,255,255,0.2)',
					'borderColor' => 'rgba(0,255,255,1)',
					'data' => []
				],
				5 => [
					'label' => 'Detail Expands',
					'backgroundColor' => 'rgba(255,255,0,0.2)',
					'borderColor' => 'rgba(255,255,0,1)',
					'data' => []
				],
				6 => [
					'label' => 'Media Views',
					'backgroundColor' => 'rgba(128,128,128,0.2)',
					'borderColor' => 'rgba(128,128,128,1)',
					'data' => []
				]
			]
		],
		'twitter-account-tweets' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Tweets',
					'backgroundColor' => 'rgba( 29,161,242, 0.2 )',
					'borderColor' => 'rgba( 29,161,242, 1 )',
					'data' => []
				]
			]
		],
		'twitter-account-impressions' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Organic Impressions',
					'backgroundColor' => 'rgba( 29,161,242, 0.2 )',
					'borderColor' => 'rgba( 29,161,242, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Paid Impressions',
					'backgroundColor' => 'rgba( 128,128,128,0.2 )',
					'borderColor' => 'rgba( 128,128,128,1 )',
					'data' => []
				]
			]
		],
		'twitter-account-engagements' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'URL Clicks',
					'backgroundColor' => 'rgba(255,0,255,0.2 )',
					'borderColor' => 'rgba(255,0,255,1 )',
					'data' => []
				],
				1 => [
					'label' => 'Retweets',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Replies',
					'backgroundColor' => 'rgba(0,0,255, 0.2 )',
					'borderColor' => 'rgba(0,0,255, 1 )',
					'data' => []
				],
				3 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba(255,0,0,0.2 )',
					'borderColor' => 'rgba(255,0,0,1 )',
					'data' => []
				]
			]
		],
		'triton-hourly' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'News',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Classical',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Mixtape',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				]
			]
		],
		'triton-news-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'CUME by Device Type',
					'backgroundColor' => [],
					'borderWidth' => 1,
					'data' => []
				]
			]
		],
		'triton-classical-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'CUME by Device Type',
					'backgroundColor' => [],
					'borderWidth' => 1,
					'data' => []
				]
			]
		],
		'triton-mixtape-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'CUME by Device Type',
					'backgroundColor' => [],
					'borderWidth' => 1,
					'data' => []
				]
			]
		],
		'youtube-videos-by-views' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Views',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Estimated Minutes Watched',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'youtube-videos-by-engagement' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Comments',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Subscribed Gained',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				]
			]
		],
		'apple-demo' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Male',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Female',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				]
			]
		],
		'apple-age' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Users',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'apple-reach' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Total Views',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Unique Views',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Reach',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				]
			]
		],
		'apple-engage' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Shares',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Favorites',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				],
				3 => [
					'label' => 'Saved Articles',
					'backgroundColor' => 'rgba( 255, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 255, 1 )',
					'data' => []
				]
			]
		]
	];

	/**
	 * Set up data for our various interactive checks
	 * All of these checks are set up as while loops for input checking
	 * If the user doesn't enter anything into the prompt, or doesn't stick to the pattern, it prints an error and prompts again
	 */
	$date_conf = $gdoc = $gdoc_conf = $emails = $email_conf = false;
	
	/**
	 * Set the end date for your report
	 * Typically, I'm running these on a Monday with the intent to cover the previous Monday through Sunday
	 */
	while ( $date_conf == false ) :
		echo "Enter the end date for your report (YYYY-MM-DD), or hit enter to set it as today: ";
		$date_in = read_stdin();
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date_in ) ) :
			$datex = explode( '-', $date_in );
			if (
				intval( $datex[0] ) <= date( 'Y' ) &&
				( intval( $datex[1] ) > 0 && intval( $datex[1] ) <= 12 ) &&
				( intval( $datex[2] ) > 0 && intval( $datex[2] < 32 ) )
			):
				$run_date = mktime( 12, 0, 0, intval( $datex[1] ), intval( $datex[2] ), intval( $datex[0] ) );
				$date_conf = true;
			else :
				echo $FG_BR_RED . $BG_BLACK . $FS_BOLD . "Invalid input, please try again." . $RESET_ALL . PHP_EOL;
			endif;
		elseif ( empty( $date_in ) ) :
			$date_conf = true;
			$run_date = time();
		else :
			echo $FG_BR_RED . $BG_BLACK . $FS_BOLD . "Invalid input, please try again." . $RESET_ALL . PHP_EOL;
		endif;
	endwhile;

	/**
	 * Choose whether or not to upload to Google Docs. Useful for testing
	 */
	while ( $gdoc_conf == false ) :
		echo "Do you want this data uploaded to Google Drive? (y/n) ";
		$gdoc_in = read_stdin();
		if ( $gdoc_in == 'y' ) :
			$gdoc_conf = true;
			$gdoc = true;
		elseif ( $gdoc_in == 'n' ) :
			$gdoc_conf = true;
		else :
			echo $FG_BR_RED . $BG_BLACK . $FS_BOLD . "Invalid input, please try again." . $RESET_ALL . PHP_EOL;
		endif;
	endwhile;

	/**
	 * Choose whether or not to email the group. Also useful for testing
	 */
	while ( $email_conf == false ) :
		echo "Do you want this report emailed to the group? (y/n) ";
		$email_in = read_stdin();
		if ( $email_in == 'y' ) :
			$email_conf = true;
			$emails = true;
		elseif ( $email_in == 'n' ) :
			$email_conf = true;
		else :
			echo $FG_BR_RED . $BG_BLACK . $FS_BOLD . "Invalid input, please try again." . $RESET_ALL . PHP_EOL;
		endif;
	endwhile;

	/**
	 * Twitter and Apple News don't have publicly accessible analytics API
	 * So, I've been downloading reports from both and saving them in their respective folders
	 * In order to facilitate past reports, all of the CSVs should have the report date in the filename
	 * Example: if you entered '2018-08-27' as the end date for your report, then Apple News data should
	 * 		be in 'channel-2018-08-27.csv'
	 * 
	 * One more note: if you look in the Twitter parser, there is reference to a 'graphs-YYYY-MM-DD.json' file
	 * 		That can be found by using the web inspector in the Twitter Analytics console, looking for the
	 * 		'account_stats.json' request in the Network panel (it will be made when you set the date), and 
	 * 		copy/pasting it into a file. I had a chunk of code that would use cURL requests to mimic a login 
	 * 		to Twitter Analytics and download the relevant files, but after locking the station Twitter account
	 * 		for the 5th time or so, I abandoned it
	 * 		
	 */
	echo "Did you remember to update your Twitter and Apple News reports? (y/n) ";
	$tw_apple = read_stdin();
	if ( $tw_apple != 'y' ) :
		echo PHP_EOL . $FG_BR_RED . $BG_BLACK . $FS_BOLD . "Well, go fix that and come back to me. I'll wait." . PHP_EOL;
		die;
	endif;

	echo $FG_BR_CYAN . $BG_BLACK . $FS_BOLD ."Hold please..." . $RESET_ALL . PHP_EOL;

	/**
	 * Setting the start and end dates in both YYYY-MM-DD format and Unixtime
	 */
	$suw = $run_date - ( 60 * 60 * 24 * 7 );
	$startu = mktime( 0, 0, 0, date( 'm', $suw ), date( 'd', $suw ), date( 'y', $suw ) );
	
	$start = date( 'Y-m-d', $startu );
	$endu = mktime( 0, 0, 0, date( 'm', $run_date ), date( 'd', $run_date ), date( 'Y', $run_date ) );
	$end = date( 'Y-m-d', $endu );
	$num = 20;

	// Facebook Graph API base 
	$fb_base = 'https://graph.facebook.com/v3.1/';

	// Where the magic happens
	if ( file_exists( GA_CLIENT ) ) :
		require BASE . DS . 'google' . DS . 'google.php';
	endif;
	
	if ( !empty( $page_access ) ) :
		require BASE . DS . 'facebook' . DS . 'facebook.php';
	endif;
	
	if ( !empty( $access ) ) :
		require BASE . DS . 'facebook' . DS . 'instagram.php';
	endif;

	require BASE . DS . 'twitter' . DS . 'twitter.php';
	
	if ( !empty( WCM_USER ) ) :
		require BASE . DS . 'triton' . DS . 'triton.php';
	endif;
	
	if ( file_exists( YT_ACCESS ) ) :
		require BASE . DS . 'google' . DS . 'youtube.php';
	endif;
	
	require BASE . DS . 'apple' . DS . 'apple.php';

	// Flipping the sheet array so Google Analytics stats are first
	$rsheets = array_reverse( $sheets );

	// Write sheets from array into XLSX file
	foreach ( $rsheets as $k => $v ) :
		$myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet( $spreadsheet, $k );
		// Setting the highlight color for the worksheet tabs in Excel
		if ( strpos( $k, 'Facebook' ) !== false ) :
			$myWorkSheet->getTabColor()->setRGB('3b5998');
		elseif ( strpos( $k, '(Main)' ) !== false || strpos( $k, '(AMP)' ) !== false ) :
			$myWorkSheet->getTabColor()->setRGB('db4437');
		elseif ( strpos( $k, 'Tweet' ) !== false || strpos( $k, 'Twitter' ) !== false ) :
			$myWorkSheet->getTabColor()->setRGB('1da1f2');
		elseif ( strpos( $k, 'Instagram' ) !== false ) :
			$myWorkSheet->getTabColor()->setRGB('8a3ab9');
		elseif ( strpos( $k, 'Triton' ) !== false ) :
			$myWorkSheet->getTabColor()->setRGB('056ab2');
		elseif ( strpos( $k, 'YouTube' ) !== false ) :
			$myWorkSheet->getTabColor()->setRGB('ff0000');
		elseif ( strpos( $k, 'Apple' ) !== false ) :
			$myWorkSheet->getTabColor()->setRGB('000000');
		endif;
		$spreadsheet->addSheet( $myWorkSheet, 0 );
		$spreadsheet->setActiveSheetIndexByName( $k );
		$spreadsheet->getActiveSheet()->fromArray( $v, NULL, 'A1' );

		// Merging some of the header rows for readability
		if ( $k == 'Triton By Device' ) :
			$spreadsheet->getActiveSheet()->mergeCells( 'A1:E1' );
		endif;
		if ( strpos( $k, 'Top Stories' ) !== false  ) :
			$spreadsheet->getActiveSheet()->mergeCells( 'A1:E1' );
			$spreadsheet->getActiveSheet()->mergeCells( 'F1:G1' );
			$spreadsheet->getActiveSheet()->mergeCells( 'H1:M1' );
			$spreadsheet->getActiveSheet()->mergeCells( 'N1:R1' );
		endif;
	endforeach;

	// Remove the default worksheet that is created
	$sheetIndex = $spreadsheet->getIndex(
		$spreadsheet->getSheetByName('Worksheet')
	);
	$spreadsheet->removeSheetByIndex($sheetIndex);

	// Auto size columns for each worksheet
	foreach ( $spreadsheet->getWorksheetIterator() as $worksheet ) :

		$spreadsheet->setActiveSheetIndex( $spreadsheet->getIndex( $worksheet ) );

		$sheet = $spreadsheet->getActiveSheet();
		$cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells( true );
		foreach ( $cellIterator as $cell ) :
			$sheet->getColumnDimension( $cell->getColumn() )->setAutoSize( true );
		endforeach;
	endforeach;

	// Write XLSX file
	$writer =  new Xlsx( $spreadsheet );
	$writer->save( BASE . DS . 'data' . DS . 'analytics-'.date( 'Y-m-d', $run_date ).'.xlsx' );

	if ( $gdoc && file_exists( GDRIVE_CREDS ) ) :
		// Upload file contents to Google Sheet
		require BASE . DS . 'google'. DS .'gsheet.php';
	endif;
	
	// Delete the local XLSX file
	unlink( BASE . DS . 'data' . DS . 'analytics-'.date( 'Y-m-d', $run_date ).'.xlsx' );

	// Save the graphing data locally as a JSON file
	file_put_contents( BASE . DS . 'data' . DS . date( 'Y-m-d', $startu ) . '.json', json_encode( $graphs ) );

	/**
	 * Update the reports.json file with the name and date of this report
	 * This information will populate the dropdown in the graphing application
	 */
	$text = json_decode( file_get_contents( BASE . DS . 'data' . DS . 'reports.json' ) );
	if ( empty( $text ) ) :
		$text = [
			0 => [
				'text' => 'Week of '.date( 'F jS, Y', $startu ),
				'value' => date( 'Y-m-d', $startu )
			]
		];
	else :
		$new_entry = [
			'text' => 'Week of '.date( 'F jS, Y', $startu ),
			'value' => date( 'Y-m-d', $startu )
		];
		array_unshift( $text, $new_entry );
	endif;
	file_put_contents( BASE . DS . 'data' . DS . 'reports.json', json_encode( $text ) );

	// Upload the file to S3, alert everyone via email, clear the Cloudfront cache
	if ( !empty( $aws_key ) ) :
		require BASE . DS . 'amazon.php';
	endif;

	// All done!
	echo $FG_BR_GREEN . $BG_BLACK . $FS_BOLD . 'Report process completed successfully!' . $RESET_ALL . PHP_EOL;
?>