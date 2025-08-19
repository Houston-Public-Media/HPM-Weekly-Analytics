<?php
	global $end, $graphs, $sheets;
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
	$apple_head = [];
	/**
	 * Open the CSV report downloaded from Apple News. They have a way to set up weekly reports that they email you
	 * 		which makes downloading the report a lot easier. The report referenced below is the "Channel Summary" report.
	 * 		I then save it in the 'apple' folder with a filename 'channel-YYYY-MM-DD.csv' (same as the report end date)
	 *
	 */
	if ( ( $handle = fopen( BASE . DS . "apple" . DS . "channel-" . $end . ".csv", "r" ) ) !== FALSE ) {
		while ( ( $data = fgetcsv( $handle, 1000 ) ) !== FALSE ) {
			if ( $row === 0 ) {
				$apple_head = array_flip( $data );

				$sheets[ $sheet ][] = [
					// Header row for the spreadsheet
					'Date','Total Views','Unique Views','Reach','Shares','Likes','Favorites','Saved Articles','Male Users','Female Users','Users 18-24','Users 25-34','Users 35-44','Users 45-54','Users 55-64','Users 65+'
				];
			} else {

				/**
				 * Mapping all of the CSV fields into the graphing data structure
				 * Demographics by gender
				 */
				$graphs['apple-demo']['labels'][] = $data[ $apple_head[ 'Date' ] ];
				$graphs['apple-demo']['datasets'][0]['data'][] = $data[ $apple_head[ 'Demographics, Proportion Male' ] ] * 100;
				$graphs['apple-demo']['datasets'][1]['data'][] = $data[ $apple_head[ 'Demographics, Proportion Female' ] ] * 100;

				/**
				 * Total views, unique views and overall reach
				 */
				$graphs['apple-reach']['labels'][] = $data[ $apple_head[ 'Date' ] ];
				$graphs['apple-reach']['datasets'][0]['data'][] = csv_int_check( 'Total Views', $data, $apple_head );
				$graphs['apple-reach']['datasets'][1]['data'][] = csv_int_check( 'Unique Viewers', $data, $apple_head );
				$graphs['apple-reach']['datasets'][2]['data'][] = csv_int_check( 'Reach', $data, $apple_head );

				/**
				 * Shares, likes, favorites, saved articles
				 */
				$graphs['apple-engage']['labels'][] = $data[ $apple_head[ 'Date' ] ];
				$graphs['apple-engage']['datasets'][0]['data'][] = csv_int_check( 'Article Shares', $data, $apple_head );
				$graphs['apple-engage']['datasets'][1]['data'][] = csv_int_check( 'Likes', $data, $apple_head );
				$graphs['apple-engage']['datasets'][2]['data'][] = csv_int_check( 'New Favorites', $data, $apple_head );
				$graphs['apple-engage']['datasets'][3]['data'][] = csv_int_check( 'Saves', $data, $apple_head );

				/**
				 * User breakdown by age range. Currently trending majority 50+
				 * This breakdown is presented by day, so I'm saving it to the side for averaging
				 */
				$apple_ages['18-24'][] = csv_int_check( 'Demographics, Proportion Age 18–24', $data, $apple_head );
				$apple_ages['25-34'][] = csv_int_check( 'Demographics, Proportion Age 25–34', $data, $apple_head );
				$apple_ages['35-44'][] = csv_int_check( 'Demographics, Proportion Age 35–44', $data, $apple_head );
				$apple_ages['45-54'][] = csv_int_check( 'Demographics, Proportion Age 45–54', $data, $apple_head );
				$apple_ages['55-64'][] = csv_int_check( 'Demographics, Proportion Age 55–64', $data, $apple_head );
				$apple_ages['65+'][] = csv_int_check( 'Demographics, Proportion Age 65+', $data, $apple_head );

				// Save all of that info into a row in the spreadsheet
				$sheets[ $sheet ][] = [
					$data[ $apple_head[ 'Date' ] ],
					csv_int_check( 'Total Views', $data, $apple_head ),
					csv_int_check( 'Unique Viewers', $data, $apple_head ),
					csv_int_check( 'Reach', $data, $apple_head ),
					csv_int_check( 'Article Shares', $data, $apple_head ),
					csv_int_check( 'Likes', $data, $apple_head ),
					csv_int_check( 'New Favorites', $data, $apple_head ),
					csv_int_check( 'Saves', $data, $apple_head ),
					csv_int_check( 'Demographics, Proportion Male', $data, $apple_head ),
					csv_int_check( 'Demographics, Proportion Female', $data, $apple_head ),
					csv_int_check( 'Demographics, Proportion Age 18–24', $data, $apple_head ),
					csv_int_check( 'Demographics, Proportion Age 25–34', $data, $apple_head ),
					csv_int_check( 'Demographics, Proportion Age 35–44', $data, $apple_head ),
					csv_int_check( 'Demographics, Proportion Age 45–54', $data, $apple_head ),
					csv_int_check( 'Demographics, Proportion Age 55–64', $data, $apple_head ),
					csv_int_check( 'Demographics, Proportion Age 65+', $data, $apple_head )
				];
				$graphs['overall-totals']['apple-news']['data'] += csv_int_check( 'Reach', $data, $apple_head );
			}
			$row++;
		}
		fclose( $handle );
	}

	/**
	 * Creating weekly averages for the different age ranges, for inclusion in the graphs
	 */
	foreach ( $apple_ages as $k => $v ) {
		$graphs['apple-age']['labels'][] = $k;
		$temp = [];
		foreach ( $v as $vv ) {
			$temp[] = (float)str_replace( '%', '', $vv );
		}
		$avg = round( array_sum( $temp ) / count( $temp ), 1 );
		$graphs['apple-age']['datasets'][0]['data'][] = $avg;
	}