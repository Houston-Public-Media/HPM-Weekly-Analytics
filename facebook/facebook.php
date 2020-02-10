<?php
	// Start Facebook Insights Pull
	$sheet = 'Facebook Insights';
	$fb_page_url = $fb_base.$hpm_fb.'/';

	/**
	 * Since the Facebook Graph API will only allow certain metrics to be pull for a particular period,
	 * 		I broke them up into several sets. That way, I can manage the order they show up in the spreadsheet
	 */
	$insights = [
		0 => [
			'day' => 'page_impressions,page_impressions_unique,page_impressions_paid,page_impressions_organic,page_impressions_viral'
		],
		1 => [
			'day' => 'page_engaged_users,page_consumptions',
		],
		2 => [
			'day' => 'page_fan_adds,page_fans'
		],
		3 => [
			'day' => 'page_positive_feedback_by_type,page_negative_feedback_by_type,page_actions_post_reactions_total'
		]
	];

	/**
	 * Mapping information for the graphing data
	 */
	$fb_label = [
		'page_impressions_paid' => 1,
		'page_impressions_unique' => 0,
		'page_impressions_organic' => 2,
		'page_impressions_viral' => 3,
		'Like (Reaction)' => 0,
		'Love' => 1,
		'Wow' => 2,
		'Haha' => 3,
		'Sorry' => 4,
		'Anger' => 5
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
					'access_token' => $fb_access,
					'appsecret_proof' => $fb_proof
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
						$endf = strtotime( $val->end_time );
						$time = date( 'Y-m-d', $endf - 86400 );

						/**
						 *  Break down the positive and negative reactions by type
						 */
						if (
							$name == 'page_actions_post_reactions_total' ||
							$name == 'page_negative_feedback_by_type' ||
							$name == 'page_positive_feedback_by_type'
						) :
							foreach ( $val->value as $n => $ra ) :
								if ( $n == 'like' ) :
									if ( $name == 'page_actions_post_reactions_total' ) :
										$ne = 'Like (Reaction)';
									elseif ( $name == 'page_positive_feedback_by_type' ) :
										$ne = 'Like (Feedback)';
									endif;
								else :
									$ne = ucwords( str_replace( '_', ' ', $n ) );
								endif;

								// Insert the data into the intermediate $results array
								$results[$time][$ne] = ( empty( $ra ) ? 0 : $ra );

								// Check if the data title is in the title array, if not, add it
								if ( !in_array( $ne, $titles ) ) :
									$titles[] = $ne;
								endif;
							endforeach;
						else :
							// Check if the data title is in the title array, if not, add it
							if ( !in_array( $title, $titles ) ) :
								$titles[] = $title;
							endif;

							// Insert the data into the intermediate $results array
							$results[$time][$name] = ( empty( $val->value ) ? 0 : $val->value );
						endif;
					endforeach;
				endforeach;
			endforeach;
		endforeach;
		$i += 7776000;
	endfor;
	$sheets[$sheet][] = $titles;

	// Loop through the $results array and map that data into the graphing dataset and spreadsheet
	$c = 1;
	foreach ( $results as $day => $re ) :
		$g = 1;
		$sheets[$sheet][$c] = [];
		$sheets[$sheet][$c][0] = $day;
		$graphs['facebook-impressions']['labels'][] = $day;
		$graphs['facebook-likes']['labels'][] = $day;
		$graphs['facebook-reactions']['labels'][] = $day;
		foreach ( $re as $rek => $ree ) :
			$sheets[$sheet][$c][$g] = $ree;
			if (
				$rek == 'page_impressions_paid' ||
				$rek == 'page_impressions_unique' ||
				$rek == 'page_impressions_viral' ||
				$rek == 'page_impressions_organic'
			 ) :
				$graphs['facebook-impressions']['datasets'][$fb_label[$rek]]['data'][] = $ree;
				if ( $rek == 'page_impressions_unique' ) :
					$graphs['overall-totals']['facebook']['data'] += $ree;
				endif;
			elseif ( $rek == 'page_fans' ) :
				$graphs['facebook-likes']['datasets'][0]['data'][] = $ree;
			elseif (
				$rek == 'Like (Reaction)' ||
				$rek == 'Love' ||
				$rek == 'Wow' ||
				$rek == 'Haha' ||
				$rek == 'Sorry' ||
				$rek == 'Anger'
			) :
				$graphs['facebook-reactions']['datasets'][$fb_label[$rek]]['data'][] = $ree;
			endif;
			$g++;
		endforeach;
		$c++;
	endforeach;

	/**
	 * Do a pull of all of our Facebook posts from the time period, and gather metrics about each
	 */
	$sheet = 'Facebook Posts';

	// Create an intermediate array for the posts, and clear out the $title array
	$posts = $titles = [];

	// Set up the titles for this initial post data pull
	$titles[] = 'Post ID';
	$titles[] = 'Publish Date';
	$titles[] = 'URL';
	$titles[] = 'Message';
	$titles[] = 'Type';
	$titles[] = 'Shares';
	$args = [
		'pretty' => 0,
		'limit' => 100,
		'fields' => 'id,created_time,message,permalink_url,shares,type',
		'since' => $startu,
		'until' => $endu,
		'access_token' => $fb_access,
		'appsecret_proof' => $fb_proof
	];

	// Build and clean the query
	$query = http_build_query( $args, '', '&' );
	$query = str_replace( '%2C', ',', $query );
	$url = "{$fb_page_url}posts?{$query}";

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

			// Save the post information into an intermediate array
			$posts[$id] = [
				'Publish Date' => date( 'Y-m-d H:i:s', strtotime( $j->created_time ) ),
				'URL' => $j->permalink_url,
				'Message' => ( !empty( $j->message ) ? $j->message : '' ),
				'Type' => $j->type,
				'Shares' => ( !empty( $j->shares ) ? $j->shares->count : 0 )
			];
		endforeach;

		// If another page of results is available, reset the query, go back to A, and run it again
		if ( !empty( $json->paging->next ) ) :
			$url = $json->paging->next;
			goto a;
		endif;
	endif;


	$args = [
		'pretty' => 0,
		'metric' => 'post_impressions,post_impressions_unique,post_impressions_paid,post_impressions_fan,post_impressions_fan_paid,post_impressions_organic,post_impressions_viral,post_engaged_users,post_engaged_fan',
		'period' => 'lifetime',
		'access_token' => $fb_access,
		'appsecret_proof' => $fb_proof
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

	$sheets['Facebook Glossary'] = $glossary;
?>