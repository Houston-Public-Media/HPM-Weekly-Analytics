<?php
	global $sheets, $graphs, $end, $startu;
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

	// Parse through the account_stats/graph data from Twitter
	$sheet = 'X Graphs';
	$json = json_decode( file_get_contents( BASE . DS . "twitter" . DS . "x-stats" . DS . $end . ".json" ) );
	$total_impressions = 0;

	// Figure out the time interval between each data point
	$interval = 86400;
	$graph = [];
	// Loop through the data points and save them into an intermediate array for the spreadsheet
	if ( !empty( $json->data->user ) ) {
		$json_result = $json->data->user;
	} elseif ( !empty( $json->data->result ) ) {
		$json_result = $json->data->result;
	} elseif ( !empty( $json->data->viewer_v2->user_results ) ) {
		$json_result = $json->data->viewer_v2->user_results;
	}
	foreach ( $json_result->result->organic_metrics_time_series as $i => $v ) {
		$twstart = $startu + ( $interval * $i );
		$tw = [
			'Date/Time' => date( 'Y-m-d', $twstart ),
			'Impressions' => 0,
			'UrlClicks' => 0,
			'Retweets' => 0,
			'Likes' => 0,
			'Replies' => 0,
			'Engagements' => 0,
			'ProfileVisits' => 0,
			'Follows' => 0,
			'VideoViews' => 0,
			'MediaViews' => 0
		];
		foreach ( $v->metric_values as $vv ) {
			if ( !empty( $vv->metric_value ) ) {
				$tw[ $vv->metric_type ] = $vv->metric_value;
				if ( $vv->metric_type == 'Impressions' ) {
					$total_impressions += $vv->metric_value;
				}
			}
		}
		$graph[] = $tw;

		$graphs['x-impressions']['labels'][] = $tw['Date/Time'];
		$graphs['x-impressions']['datasets'][0]['data'][] = $tw['Impressions'];

		$graphs['x-engagements']['labels'][] = $tw['Date/Time'];
		$graphs['x-engagements']['datasets'][0]['data'][] = $tw['LinkClicks'];
		$graphs['x-engagements']['datasets'][1]['data'][] = $tw['Retweets'];
		$graphs['x-engagements']['datasets'][2]['data'][] = $tw['Replies'];
		$graphs['x-engagements']['datasets'][3]['data'][] = $tw['Likes'];
	}

	$graphs['overall-totals']['X']['data'] = $total_impressions;

	// Insert the spreadsheet data
	$t = 0;
	foreach ( $graph as $v ) {
		if ( $t == 0 ) {
			$sheets[ $sheet ][] = array_keys( $v );
		}
		$sheets[ $sheet ][] = array_values( $v );
		$t++;
	}