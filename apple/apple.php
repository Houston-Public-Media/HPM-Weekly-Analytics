<?php
	$apple_ages = [
		'18-24' => [],
		'25-34' => [],
		'35-44' => [],
		'45-54' => [],
		'55-64' => [],
		'65+' => [],
	];
	
	$sheet = 'Apple News';
	$row = 0;
	/**
	 * Open the CSV report downloaded from Apple News. They have a way to set up weekly reports that they email you
	 * 		which makes downloading the report a lot easier. The report referenced below is the "Channel Summary" report.
	 * 		I then save it in the 'apple' folder with a filename 'channel-YYYY-MM-DD.csv' (same as the report end date)
	 * 
	 */
	if ( ( $handle = fopen( BASE . DS . "apple" . DS . "channel-" . $end . ".csv", "r" ) ) !== FALSE ) :
		while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) :
			if ( $row === 0 ) :
				$sheets[$sheet][] = [
					// Header row for the spreadsheet
					'Date','Total Views','Unique Views','Reach','Shares','Likes','Favorites','Saved Articles','Male Users','Female Users','Users 18-24','Users 25-34','Users 35-44','Users 45-54','Users 55-64','Users 65+'
				];
			else :
				/**
				 * Mapping all of the CSV fields into the graphing data structure
				 * Demographics by gender
				 */
				$graphs['apple-demo']['labels'][] = $data[0];
				$graphs['apple-demo']['datasets'][0]['data'][] = $data[19] * 100;
				$graphs['apple-demo']['datasets'][1]['data'][] = $data[20] * 100;

				/**
				 * Total views, unique views and overall reach
				 */
				$graphs['apple-reach']['labels'][] = $data[0];
				$graphs['apple-reach']['datasets'][0]['data'][] = ( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) );
				$graphs['apple-reach']['datasets'][1]['data'][] = ( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) );
				$graphs['apple-reach']['datasets'][2]['data'][] = ( empty( intval( $data[18] ) ) ? 0 : intval( $data[18] ) );

				/**
				 * Shares, likes, favorites, saved articles
				 */
				$graphs['apple-engage']['labels'][] = $data[0];
				$graphs['apple-engage']['datasets'][0]['data'][] = ( empty( intval( $data[3] ) ) ? 0 : intval( $data[3] ) );
				$graphs['apple-engage']['datasets'][1]['data'][] = ( empty( intval( $data[9] ) ) ? 0 : intval( $data[9] ) );
				$graphs['apple-engage']['datasets'][2]['data'][] = ( empty( intval( $data[10] ) ) ? 0 : intval( $data[10] ) );
				$graphs['apple-engage']['datasets'][3]['data'][] = ( empty( intval( $data[13] ) ) ? 0 : intval( $data[13] ) );

				/**
				 * User breakdown by age range. Currently trending majority 50+
				 * This breakdown is presented by day, so I'm saving it to the side for averaging
				 */
				$apple_ages['18-24'][] = $data[21] * 100;
				$apple_ages['25-34'][] = $data[22] * 100;
				$apple_ages['35-44'][] = $data[23] * 100;
				$apple_ages['45-54'][] = $data[24] * 100;
				$apple_ages['55-64'][] = $data[25] * 100;
				$apple_ages['65+'][] = $data[26] * 100;

				// Save all of that info into a row in the spreadsheet
				$sheets[$sheet][] = [
					$data[0],
					( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) ),
					( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) ),
					( empty( intval( $data[18] ) ) ? 0 : intval( $data[18] ) ),
					( empty( intval( $data[3] ) ) ? 0 : intval( $data[3] ) ),
					( empty( intval( $data[9] ) ) ? 0 : intval( $data[9] ) ),
					( empty( intval( $data[10] ) ) ? 0 : intval( $data[10] ) ),
					( empty( intval( $data[13] ) ) ? 0 : intval( $data[13] ) ),
					($data[19] * 100).'%',
					($data[20] * 100).'%',
					($data[21] * 100).'%',
					($data[22] * 100).'%',
					($data[23] * 100).'%',
					($data[24] * 100).'%',
					($data[25] * 100).'%',
					($data[26] * 100).'%'
				];
				$graphs['overall-totals']['apple-news']['data'] += ( empty( intval( $data[18] ) ) ? 0 : intval( $data[18] ) );
			endif;
			$row++;
		endwhile;
		fclose($handle);
	endif;

	/**
	 * Creating weekly averages for the different age ranges, for inclusion in the graphs
	 */
	foreach ( $apple_ages as $k => $v ) :
		$graphs['apple-age']['labels'][] = $k;
		$avg = round( array_sum( $v ) / count( $v ), 1 );
		$graphs['apple-age']['datasets'][0]['data'][] = $avg;
	endforeach;
?>