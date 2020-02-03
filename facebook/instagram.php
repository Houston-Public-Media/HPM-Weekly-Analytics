<?php
	// Start Instagram Insights Pull
	$sheet = 'Instagram Insights';
	$fb_page_url = $fb_base.$hpm_insta.'/';

	/**
	 * Since the Facebook Graph API will only allow certain metrics to be pull for a particular period,
	 * 		I broke them up. However, Instagram insights on the Graph API are still fairly thin
	 */
	$insights = [
		0 => [
			'day' => 'follower_count,reach,profile_views,impressions'
		]
	];

	/**
	 * Mapping information for the graphing data
	 */
	$insta_labels = [
		'follower_count' => 0,
		'reach' => 1,
		'profile_views' => 2,
		'impressions' => 3
	];

	/**
	 * Set up arrays for the title row of the spreadsheet, the term glossary sheet, and the results
	 */
	$results = $titles = $glossary = [];
	$glossary[] = [ 'Term', 'Definition' ];
	$titles[] = 'Day';

	/**
	 * The Graph API only allows you to pull 93 days at a time. This script is dealing with weekly increments,
	 * 		so the loop isn't completely necessary, but this way it can be used for longer pulls if need be
	 */
	for ( $i = $startu; $i <= $endu; ) :
		$di = $endu - $i;
		if ( $di > 7776000 ) :
			$endt = $i + 7776000;
		else :
			$endt = $endu;
		endif;

		/**
		 * Loop through the $insights array to pull the various metrics we need
		 */
		foreach ( $insights as $insight ) :
			foreach ( $insight as $k => $v ) :
				$period = $k;
				$metric = $v;
				$args = [
					'pretty' => 0,
					'metric' => $metric,
					'period' => $period,
					'since' => $i,
					'until' => $endt,
					'access_token' => $access,
					'appsecret_proof' => $proof
				];

				// Build and clean the query and generate the URL
				$query = http_build_query( $args, '', '&' );
				$query = str_replace( '%2C', ',', $query );
				$url = "{$fb_page_url}insights?{$query}";

				// Run that URL through cURL
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $url );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				$result = curl_exec( $ch );
				curl_close( $ch );

				// Decode the response from the Graph API and loop through
				$json = json_decode( $result );
				foreach ( $json->data as $d ) :
					$title = $d->title;
					$name = $d->name;

					// Check if the term's definition is in the glossary, and add it if not
					if ( !in_array_r( $d->title, $glossary ) ) :
						$glossary[] = [ $d->title, $d->description ];
					endif;
					foreach ( $d->values as $val ) :
						$endi = strtotime( $val->end_time );
						$time = date( 'Y-m-d', $endi - 86400 );

						// Check if the data title is in the title array, if not, add it
						if ( !in_array( $title, $titles ) ) :
							$titles[] = $title;
						endif;

						// Insert the data into the intermediate $results array
						$results[$time][$name] = $val->value;
					endforeach;
				endforeach;
			endforeach;
		endforeach;
		$i += 7776000;
	endfor;
	$sheets[$sheet][] = $titles;
	$c = 1;

	// Loop through the $results array and map that data into the graphing dataset and spreadsheet
	foreach ( $results as $day => $re ) :
		$g = 1;
		$sheets[$sheet][$c] = [];
		$sheets[$sheet][$c][0] = $day;
		$graphs['instagram-stats']['labels'][] = $day;
		foreach ( $re as $rek => $ree ) :
			$sheets[$sheet][$c][$g] = $ree;
			if ( $rek == 'reach' ) :
				$graphs['overall-totals']['instagram']['data'] += $ree;
			endif;
			$graphs['instagram-stats']['datasets'][$insta_labels[$rek]]['data'][] = $ree;
			$g++;
		endforeach;
		$c++;
	endforeach;

	/**
	 * Do a pull of all of our Instagram posts from the time period, and gather metrics about each
	 */
	$sheet = 'Instagram Posts';

	// Create an intermediate array for the posts, and clear out the $title array
	$posts = $titles = [];

	// Set up the titles for this initial post data pull
	$titles[] = 'Post ID';
	$titles[] = 'Publish Date';
	$titles[] = 'URL';
	$titles[] = 'Caption';
	$titles[] = 'Type';
	$titles[] = 'Likes';
	$titles[] = 'Comments';
	$args = [
		'pretty' => 0,
		'limit' => 100,
		'fields' => 'id,permalink,like_count,timestamp,comments_count,caption,media_type',
		'access_token' => $access,
		'appsecret_proof' => $proof
	];

	// Build and clean the query
	$query = http_build_query( $args, '', '&' );
	$query = str_replace( '%2C', ',', $query );
	$url = "{$fb_page_url}media?{$query}";

	/**
	 * Set a waypoint that we can come back to if need be. Since we can only pull 100 posts from the Graph API,
	 * 		we may need to loop back and request more (if more than 100 posts exist for that time period)
	 */
	a:
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec( $ch );
	curl_close( $ch );

	// Decode the results and loop
	$json = json_decode( $result );
	if ( !empty( $json->data ) ) :
		foreach ( $json->data as $j ) :
			$id = $j->id;
			$pubtime = strtotime( $j->timestamp );

			// Save the post information into an intermediate array
			if ( $pubtime >= $startu && $pubtime <= $endu ) :
				$posts[$id] = [
					'Publish Date' => date( 'Y-m-d H:i:s', $pubtime ),
					'URL' => $j->permalink,
					'Caption' => ( !empty( $j->caption ) ? $j->caption : '' ),
					'Type' => ucwords( strtolower( $j->media_type ) ),
					'Likes' => ( !empty( $j->like_count ) ? $j->like_count : 0 ),
					'Comments' => ( !empty( $j->comments_count ) ? $j->comments_count : 0 )
				];
			endif;
		endforeach;

		// If another page of results is available, reset the query, go back to A, and run it again
		if ( !empty( $json->paging->next ) ) :
			$url = $json->paging->next;
			goto a;
		endif;
	endif;

	$args = [
		'pretty' => 0,
		'metric' => 'engagement,impressions,reach',
		'period' => 'lifetime',
		'access_token' => $access,
		'appsecret_proof' => $proof
	];

	// Build and clean the query for each individual post
	$query = http_build_query( $args, '', '&' );
	$query = str_replace( '%2C', ',', $query );

	/**
	 * Pull enhanced stats for all of the posts
	 */
	foreach ( $posts as $pid => $pv ) :
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "{$fb_base}{$pid}/insights?{$query}" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$res = curl_exec( $ch );
		curl_close( $ch );
		$js = json_decode( $res );
		if ( !empty( $js->data ) ) :
			foreach ( $js->data as $d ) :
				$title = $d->title;
				$name = $d->name;

				// Check if the term's definition is in the glossary, and add it if not
				if ( !in_array_r( $d->title, $glossary ) ) :
					$glossary[] = [ $d->title, $d->description ];
				endif;

				// Check if the term's definition is in the title array, and add it if not
				if ( !in_array( $title, $titles ) ) :
					$titles[] = $title;
				endif;
				foreach ( $d->values as $val ) :
					$posts[$pid][$name] = $val->value;
				endforeach;
			endforeach;
		endif;
	endforeach;

	// Map that data into the spreadsheet
	$sheets[$sheet][] = $titles;
	$c = 1;
	foreach ( $posts as $p => $pv ) :
		$g = 1;
		$sheets[$sheet][$c] = [];
		$sheets[$sheet][$c][0] = $p;
		foreach ( $pv as $ppv ) :
			$sheets[$sheet][$c][$g] = $ppv;
			$g++;
		endforeach;
		$c++;
	endforeach;

	$sheets['Instagram Glossary'] = $glossary;
?>