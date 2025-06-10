<?php
	global $sheets, $graphs, $end, $startu;
	/**
	 * The analytics dashboard for X has changed several times, but it looks like they've finally settled on a format
	 * Now we are going through
	 */

	// Parse through the account_stats/graph data from Twitter
	$sheet = 'X Graphs';
	$row = 0;
	$total_impressions = 0;
	$x_head = [];
	$temp_data = [];
	$temp_head = [];
	if ( ( $handle = fopen( BASE . DS . "twitter" . DS . "x-stats" . DS . "csv" . DS . $end . ".csv", "r" ) ) !== FALSE ) {
		while ( ( $data = fgetcsv( $handle, 1000 ) ) !== false ) {
			if ( $row === 0 ) {
				$x_head = array_flip( $data );
				$temp_head = $data;
			} else {
				$date = strtotime( $data[0] );
				$data[0] = date( 'Y-m-d', $date );
				$temp_data[] = $data;
			}
			$row++;
		}
	}
	$temp_data[] = $temp_head;
	$temp_data = array_reverse( $temp_data );
	$sheets[ $sheet ] = $temp_data;
	foreach ( $temp_data as $xdata ) {
		if ( !is_numeric( $xdata[1] ) ) {
			continue;
		}
		$graphs['x-impressions']['labels'][] = $xdata[ $x_head[ 'Date' ] ];
		$graphs['x-impressions']['datasets'][0]['data'][] = $xdata[ $x_head[ 'Impressions' ] ];
		$total_impressions += $xdata[ $x_head[ 'Impressions' ] ];

		$url_clicks = $xdata[ $x_head[ 'Engagements' ] ] -
			$xdata[ $x_head[ 'Likes' ] ] -
			$xdata[ $x_head[ 'Replies' ] ] -
			$xdata[ $x_head[ 'Bookmarks' ] ] -
			$xdata[ $x_head[ 'Shares' ] ] -
			$xdata[ $x_head[ 'Reposts' ] ] -
			$xdata[ $x_head[ 'Profile visits' ] ];
		$graphs['x-engagements']['labels'][] = $xdata[ $x_head[ 'Date' ] ];
		$graphs['x-engagements']['datasets'][0]['data'][] = $xdata[ $x_head[ 'Reposts' ] ];
		$graphs['x-engagements']['datasets'][1]['data'][] = $xdata[ $x_head[ 'Replies' ] ];
		$graphs['x-engagements']['datasets'][2]['data'][] = $xdata[ $x_head[ 'Likes' ] ];
		$graphs['x-engagements']['datasets'][3]['data'][] = ( max( $url_clicks, 0 ) );
	}
	$graphs['overall-totals']['X']['data'] = $total_impressions;