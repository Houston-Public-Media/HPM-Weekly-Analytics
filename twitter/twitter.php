<?php
	global $sheets, $graphs, $end;
	/**
	 * Twitter does not have a publicly-accessible analytics API that isn't solely dedicated to advertisers
	 * 		(API problems with Twitter? Shocked! Shocked I say!)
	 *
	 * That said, the data for this section comes in 2 parts: the "Export Data" function in Twitter's analytics
	 * 		dashboard, which spits out a CSV file; and a JSON file called "account_stats.json" that you have to
	 * 		grab from the Network tab of your browser's developer tools. Open the dev tools, change the date range
	 * 		on the page, and then search for the request named "account_stats.json". Copy the response and save it
	 *		into a file.
	 *
	 * I know that last part seems a bit arduous, but it's better than the previous method, which was to simulate
	 * 		a login and download the data using cURL request, which works up to maybe 5 times before Twitter starts
	 * 		locking your account for "exhibiting automated behavior that violates their terms of service."
	 *
	 * One last note: I always save the files in the following format: "tweets-YYYY-MM-DD.csv" and "graphs-YYYY-MM-DD.json"
	 * 		with the dates matching the end date of the report
	 */
	$tweets = $tw_eng = $tw_imp = [];
	$row = 0;
	$tw_head = [];

	// Open the CSV file
	if ( ( $handle = fopen( BASE . DS . "twitter" . DS . "tweets" . DS . "tweets-" . $end . ".csv", "r" ) ) !== FALSE ) {
		while ( ( $data = fgetcsv( $handle, 1000 ) ) !== FALSE ) {
			if ( $row == 0 ) {
				$tw_head = array_flip( $data );
			} else {
				// Insert each tweet as a row using the tweets ID as the array key
				$tweets[ $data[ $tw_head['Tweet id'] ] ] = [
					'Tweet Text' => preg_replace( [ '/ https:\/\/t\.co\/[a-zA-Z0-9]+/', '/\n/' ], [ '', ' ' ], $data[ $tw_head['Tweet text'] ] ),
					'Tweet Link' => $data[ $tw_head['Tweet permalink'] ],
					'Date' => $data[ $tw_head['time'] ],
					'Impressions' => csv_int_check( 'impressions', $data, $tw_head ),
					'Engagements' => csv_int_check( 'engagements', $data, $tw_head ),
					'Engagement Rate' => round( $data[ $tw_head[ 'engagement rate' ] ] * 100, 1 )."%",
					'Retweets' => csv_int_check( 'retweets', $data, $tw_head ),
					'Replies' => csv_int_check( 'replies', $data, $tw_head ),
					'Likes' => csv_int_check( 'likes', $data, $tw_head ),
					'URL Clicks' => csv_int_check( 'url clicks', $data, $tw_head ),
					'Hashtag Clicks' => csv_int_check( 'hashtag clicks', $data, $tw_head ),
					'Detail Expands' => csv_int_check( 'detail expands', $data, $tw_head ),
					'Media Views' => csv_int_check( 'media views', $data, $tw_head ),
					'Media Engagements' => csv_int_check( 'media engagements', $data, $tw_head )
				];
				// Save the number of engagements for each tweet into an array for sorting
				$tw_eng[ $data[ $tw_head[ 'Tweet id' ] ] ] = csv_int_check( 'engagements', $data, $tw_head );

				// Save the number of impressions for each tweet into an array for sorting
				$tw_imp[ $data[ $tw_head[ 'Tweet id' ] ] ] = csv_int_check( 'impressions', $data, $tw_head );
			}
			$row++;
		}
		fclose( $handle );
	}

	// Create a worksheet in the spreadsheet that lists all of the tweets and their relevant stats
	$t = 0;
	$sheet = 'Tweets';
	foreach ( $tweets as $tweet ) {
		if ( $t == 0 ) {
			$sheets[ $sheet ][] = array_keys( $tweet );
		}
		$sheets[ $sheet ][] = array_values( $tweet );
		$t++;
	}

	// Create a worksheet with breakdowns
	$sheet = 'Tweet Analytics';
	arsort( $tw_eng );
	arsort( $tw_imp );

	// Sort the impression and engagement arrays, and return the top 10 of each
	$tw_most_eng = array_slice( $tw_eng, 0, 10, true );
	$tw_most_imp = array_slice( $tw_imp, 0, 10, true );
	$t = 0;

	// Enter the top 10 tweets by engagement into the spreadsheet
	$sheets[ $sheet ][] = [ 'Top 10 Tweets by Engagements' ];
	foreach ( $tw_most_eng as $k => $v ) {
		if ( $t == 0 ) {
			$sheets[ $sheet ][] = array_keys( $tweets[ $k ] );
		}
		$sheets[ $sheet ][] = array_values( $tweets[ $k ] );
		$t++;

		// Map that data into the graphing data
		$graphs['twitter-tweets-by-engagement']['labels'][] = $tweets[ $k ]['Tweet Text'];
		$graphs['twitter-tweets-by-engagement']['datasets'][0]['data'][] = $tweets[ $k ]['Retweets'];
		$graphs['twitter-tweets-by-engagement']['datasets'][1]['data'][] = $tweets[ $k ]['Replies'];
		$graphs['twitter-tweets-by-engagement']['datasets'][2]['data'][] = $tweets[ $k ]['Likes'];
		$graphs['twitter-tweets-by-engagement']['datasets'][3]['data'][] = $tweets[ $k ]['URL Clicks'];
		$graphs['twitter-tweets-by-engagement']['datasets'][4]['data'][] = $tweets[ $k ]['Hashtag Clicks'];
		$graphs['twitter-tweets-by-engagement']['datasets'][5]['data'][] = $tweets[ $k ]['Detail Expands'];
		$graphs['twitter-tweets-by-engagement']['datasets'][6]['data'][] = $tweets[ $k ]['Media Views'];
	}

	$t = 0;
	$sheets[ $sheet ][] = [ '' ];
	$sheets[ $sheet ][] = [ '' ];

	// Do the same for the top 10 by impressions
	$sheets[ $sheet ][] = [ 'Top 10 Tweets by Impressions' ];
	foreach ( $tw_most_imp as $k => $v ) {
		if ( $t == 0 ) {
			$sheets[ $sheet ][] = array_keys( $tweets[ $k ] );
		}
		$sheets[ $sheet ][] = array_values( $tweets[ $k ] );
		$t++;

		$graphs['twitter-tweets-by-impression']['labels'][] = $tweets[ $k ]['Tweet Text'];
		$graphs['twitter-tweets-by-impression']['datasets'][0]['data'][] = $tweets[ $k ]['Impressions'];
	}

	// Parse through the account_stats/graph data from Twitter
	$sheet = 'Twitter Graphs';
	$json = json_decode( file_get_contents( BASE . DS . "twitter" . DS . "graphs" . DS . "graphs-" . $end . ".json" ) );

	$graphs['overall-totals']['twitter']['data'] = $json->totals->impressions;
	// Divide the start and end times in the JSON file by 1000, since they measure in microseconds
	$twstart = ( $json->startTime / 1000 );
	$twend = ( $json->endTime / 1000 );

	// Figure out the time interval between each data point
	$interval = ( $twend - $twstart ) / count( $json->timeSeries->orgImpressions );

	$c = 0;
	$graph = [];
	// Loop through the data points and save them into an intermediate array for the spreadsheet
	for ( $i = $twstart; $i < $twend; ) {
		$tw = [
			'Date/Time' => date( 'Y-m-d', $i ),
			'Organic Impressions' => ( empty( $json->timeSeries->orgImpressions[ $c ] ) ? 0 : $json->timeSeries->orgImpressions[ $c ] ),
			'Paid Impressions' => ( empty( $json->timeSeries->prImpressions[ $c ] ) ? 0 : $json->timeSeries->prImpressions[ $c ] ),
			'Impressions' => ( empty( $json->timeSeries->impressions[ $c ] ) ? 0 : $json->timeSeries->impressions[ $c ] ),
			'URL Clicks' => ( empty( $json->timeSeries->urlClicks[ $c ] ) ? 0 : $json->timeSeries->urlClicks[ $c ] ),
			'Retweets' => ( empty( $json->timeSeries->retweets[ $c ] ) ? 0 : $json->timeSeries->retweets[ $c ] ),
			'Favorites' => ( empty( $json->timeSeries->favorites[ $c ] ) ? 0 : $json->timeSeries->favorites[ $c ] ),
			'Replies' => ( empty( $json->timeSeries->replies[ $c ] ) ? 0 : $json->timeSeries->replies[ $c ] ),
			'Engagements' => ( empty( $json->timeSeries->engagements[ $c ] ) ? 0 : $json->timeSeries->engagements[ $c ] ),
			'Engagement Rate' => round( $json->timeSeries->engagementRate[ $c ], 1 )."%",
			'Tweets' => ( empty( $json->timeSeries->tweets[ $c ] ) ? 0 : $json->timeSeries->tweets[ $c ] )
		];
		$graph[] = $tw;
		$i += $interval;
		$c++;

		// Map it into the graphing data
		$graphs['twitter-account-tweets']['labels'][] = $tw['Date/Time'];
		$graphs['twitter-account-tweets']['datasets'][0]['data'][] = $tw['Tweets'];

		$graphs['twitter-account-impressions']['labels'][] = $tw['Date/Time'];
		$graphs['twitter-account-impressions']['datasets'][0]['data'][] = $tw['Organic Impressions'];
		$graphs['twitter-account-impressions']['datasets'][1]['data'][] = $tw['Paid Impressions'];

		$graphs['twitter-account-engagements']['labels'][] = $tw['Date/Time'];
		$graphs['twitter-account-engagements']['datasets'][0]['data'][] = $tw['URL Clicks'];
		$graphs['twitter-account-engagements']['datasets'][1]['data'][] = $tw['Retweets'];
		$graphs['twitter-account-engagements']['datasets'][2]['data'][] = $tw['Replies'];
		$graphs['twitter-account-engagements']['datasets'][3]['data'][] = $tw['Favorites'];
	}

	// Insert the spreadsheet data
	$t = 0;
	foreach ( $graph as $v ) {
		if ( $t == 0 ) {
			$sheets[ $sheet ][] = array_keys( $v );
		}
		$sheets[ $sheet ][] = array_values( $v );
		$t++;
	}