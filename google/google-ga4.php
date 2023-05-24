<?php
	global $graphs, $sheets, $start, $end;
	use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
	use Google\Analytics\Data\V1beta\DateRange;
	use Google\Analytics\Data\V1beta\Dimension;
	use Google\Analytics\Data\V1beta\Metric;
	use Google\Analytics\Data\V1beta\MetricAggregation;
	use Google\Analytics\Data\V1beta\FilterExpression;
	use Google\Analytics\Data\V1beta\Filter;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;

/**
 * @throws ApiException
 */
function googleArticleSources( $row ): array {
		global $analytics, $start, $end, $ga, $find, $replace, $startu, $endu;
		$path = $row->getDimensionValues()[0]->getValue();
		preg_match( '/\/articles\/[a-z0-9\-\/]+\/[0-9]{4}\/[0-9]{2}\/[0-9]{2}\/([0-9]+)\/.+/', $path, $match );
		if ( empty( $match ) ) {
			return [];
		}
		$id = $match[1];
		$post = file_get_contents( 'https://www.houstonpublicmedia.org/wp-json/wp/v2/posts/' . $id );
		$cats = file_get_contents( 'https://www.houstonpublicmedia.org/wp-json/wp/v2/categories?post=' . $id );
		$pjs = json_decode( $post );
		$catjs = json_decode( $cats );
		$title = html_entity_decode( str_replace( $find, $replace, $pjs->title->rendered ), ENT_QUOTES, 'UTF-8' );
		$date = strtotime( $pjs->date );
		if ( $date >= $startu && $date <= $endu ) {
			$title = "📅  " . $title;
		}
		$authors = $tags = [];
		foreach( $pjs->coauthors as $coa ) {
			$authors[] = $coa->display_name;
		}
		foreach( $catjs as $ca ) {
			$tags[] = html_entity_decode( $ca->name );
		}
		$date_format = date( 'Y-m-d g:i A', $date );

		// Secondary GA pull to gather source / medium information for each article
		$sources = $analytics->runReport([
			'property' => 'properties/' . $ga,
			'dateRanges' => [
				new DateRange([
					'start_date' => $start,
					'end_date' => $end,
				]),
			],
			'dimensions' => [
				new Dimension([ 'name' => 'sessionSourceMedium' ])
			],
			'metrics' => [
				new Metric([ 'name' => 'sessions' ])
			],
			'dimensionFilter' => new FilterExpression([
				'filter' => new Filter([
					'field_name' => 'pagePath',
					'string_filter' => new Filter\StringFilter([
						'match_type' => Filter\StringFilter\MatchType::BEGINS_WITH,
						'value' => $path
					])
				])
			])
		]);
		$google = $facebook = $twitter = $rss = $newsbreak = $direct = $organic = $email = $referral = $social = '0';

		// Parsing the source / medium pull from GA
		foreach ( $sources->getRows() as $source ) {
			$source_ex = explode( ' / ', $source->getDimensionValues()[0]->getValue() );
			$metric = $source->getMetricValues()[0]->getValue();
			$medium = ( !empty( $source_ex[1] ) ? $source_ex[1] : '' );
			$stype = trim( $source_ex[0] );
			if ( $medium == '(none)' ) {
				$direct += $metric;
			} elseif ( $medium == 'organic' ) {
				$organic += $metric;
			} elseif ( $medium == 'social' ) {
				$social += $metric;
			} elseif ( $medium == 'email' ) {
				$email += $metric;
			} elseif ( $medium == 'referral' ) {
				$referral += $metric;
			}
			if ( str_contains( $stype, 'google' ) ) {
				$google += $metric;
			} elseif ( str_contains( $stype, 'facebook' ) ) {
				$facebook += $metric;
			} elseif ( str_contains( $stype, 't.co' ) ) {
				$twitter += $metric;
			} elseif ( str_contains( $stype, 'rss' ) ) {
				$rss += $metric;
			} elseif ( str_contains( $stype, 'newsbreak' ) ) {
				$newsbreak += $metric;
			}
		}

		// Adding the row to the sheet
		return [
			$title,
			'https://www.houstonpublicmedia.org' . $path,
			implode( ' / ', $authors ),
			$date_format,
			implode( ', ', $tags ),
			$row->getMetricValues()[0]->getValue(),
			$row->getMetricValues()[1]->getValue(),
			$direct,
			$google,
			$facebook,
			$twitter,
			$rss,
			$newsbreak,
			$direct,
			$organic,
			$email,
			$referral,
			$social
		];
	}

	/**
	 * We have our main Google Analytics property for the site, as well as a separate property for Google AMP
	 * If you only have one you want to check, or more than two, modify this array
	 */
	$gas = [
		'GA4' => GA4_PROPERTY
	];

	// Setting up device colors for the graphing application
	$ga_device_colors = [
		'desktop' => 'rgba(255,0,0,1)',
		'tablet' => 'rgba(0,0,255,1)',
		'mobile' => 'rgba(0,255,0,1)'
	];

	try {
		$analytics = new BetaAnalyticsDataClient( [
			'credentials' => GA_CLIENT
		] );
	} catch ( ValidationException $e ) {
		echo $e->getMessage() . PHP_EOL;
		die;
	}
	foreach ( $gas as $g => $ga ) {
		$ga_acct_name = 'ga-'.strtolower( $g );

		// Pull article numbers from GA
		try {
			$result = $analytics->runReport( [
				'property'        => 'properties/' . $ga,
				'dateRanges'      => [
					new DateRange( [
						'start_date' => $start,
						'end_date'   => $end,
					] ),
				],
				'dimensions'      => [
					new Dimension( [ 'name' => 'pagePath' ] )
				],
				'metrics'         => [
					new Metric( [ 'name' => 'screenPageViews' ] ),
					new Metric( [ 'name' => 'activeUsers' ] )
				],
				'dimensionFilter' => new FilterExpression( [
					'filter' => new Filter( [
						'field_name'    => 'pagePath',
						'string_filter' => new Filter\StringFilter( [
							'match_type' => Filter\StringFilter\MatchType::BEGINS_WITH,
							'value'      => '/articles/'
						] )
					] )
				] ),
				'limit' => 25
			] );
		} catch ( ApiException $e ) {
			echo $e->getMessage() . PHP_EOL;
			die;
		}

		$sheet = 'Top Stories ('.$g.')';
		$sheets[ $sheet ] = [];


		// Parsing the numbers from GA
		foreach ( $result->getRows() as $k => $row ) {
			if ( $k == 0 ) {
				$sheets[ $sheet ][] = [
					'Article Info', '', '', '', '', 'Pageviews', '', 'Pageviews from Source', '', '', '', '', '', 'Source Types', '', '', '', ''
				];
				$sheets[ $sheet ][] = [
					'Title', 'URL', 'Author', 'Date', 'Categories and Tags', 'Total', 'Unique', 'Direct', 'Google', 'Facebook', 'Twitter', 'RSS Feeds', 'Newsbreak App', 'Direct/No Referrer', 'Organic', 'Email', 'Referral', 'Social'
				];
			}
			try {
				$gaSources = googleArticleSources( $row );
			} catch ( ApiException $e ) {
				echo $e->getMessage() . PHP_EOL;
				die;
			}

			if ( !empty( $gaSources ) ) {
				// Adding the row to the sheet
				$sheets[ $sheet ][] = $gaSources;

				$others = $gaSources[5] - ( $gaSources[7] + $gaSources[8] + $gaSources[9] + $gaSources[10] + $gaSources[11] + $gaSources[12] );

				// Mapping the data into the graphing data
				$graphs[ $ga_acct_name.'-articles' ]['labels'][] = $gaSources[0];
				$graphs[ $ga_acct_name.'-articles' ]['datasets'][0]['data'][] = $gaSources[7];
				$graphs[ $ga_acct_name.'-articles' ]['datasets'][1]['data'][] = $gaSources[8];
				$graphs[ $ga_acct_name.'-articles' ]['datasets'][2]['data'][] = $gaSources[9];
				$graphs[ $ga_acct_name.'-articles' ]['datasets'][3]['data'][] = $gaSources[10];
				$graphs[ $ga_acct_name.'-articles' ]['datasets'][4]['data'][] = $gaSources[11];
				$graphs[ $ga_acct_name.'-articles' ]['datasets'][5]['data'][] = $gaSources[12];
				$graphs[ $ga_acct_name.'-articles' ]['datasets'][6]['data'][] = ( max( $others, 0 ) );
			}
		}

		// User / Session pull from GA
		try {
			$result2 = $analytics->runReport( [
				'property'           => 'properties/' . $ga,
				'dateRanges'         => [
					new DateRange( [
						'start_date' => $start,
						'end_date'   => $end,
					] ),
				],
				'dimensions'         => [
					new Dimension( [ 'name' => 'dateHour' ] )
				],
				'metrics'            => [
					new Metric( [ 'name' => 'sessions' ] ),
					new Metric( [ 'name' => 'activeUsers' ] )
				],
				'metricAggregations' => [
					MetricAggregation::TOTAL,
				]
			] );
		} catch ( ApiException $e ) {
			echo $e->getMessage() . PHP_EOL;
			die;
		}

		$graphs['overall-totals'][ $ga_acct_name ]['data'] += $result2->getTotals()[0]->getMetricValues()[1]->getValue();

		// New sheet
		$sheet = 'Hourly Stats ('.$g.')';
		$sheets[ $sheet ] = [];

		// Parsing User / Session results
		foreach ( $result2->getRows() as $k => $row ) {
			if ( $k == 0 ) {
				$sheets[ $sheet ][] = [
					'Day and Time', 'Sessions', 'Users'
				];
			}
			$hourly = $row->getDimensionValues()[0]->getValue();
			$time_string = substr( $hourly, 4, 2 ) . '/' . substr( $hourly, 6, 2 ) . '/' . substr( $hourly, 0, 4 ) . ' ' . substr( $hourly, 8, 2 ) . ':00';
			$sheets[ $sheet ][] = [
				$time_string, $row->getMetricValues()[0]->getValue(), $row->getMetricValues()[1]->getValue()
			];
			$graphs[ $ga_acct_name.'-hourly' ]['labels'][] = $time_string;
			$graphs[ $ga_acct_name.'-hourly' ]['datasets'][0]['data'][] = $row->getMetricValues()[1]->getValue();
		}

		// Inserting blank rows
		$sheets[ $sheet ][] = [
			'', '', ''
		];
		$sheets[ $sheet ][] = [
			'', '', ''
		];

		// Pulling device category stats from GA
		try {
			$result3 = $analytics->runReport( [
				'property'   => 'properties/' . $ga,
				'dateRanges' => [
					new DateRange( [
						'start_date' => $start,
						'end_date'   => $end,
					] ),
				],
				'dimensions' => [
					new Dimension( [ 'name' => 'deviceCategory' ] )
				],
				'metrics'    => [
					new Metric( [ 'name' => 'sessions' ] ),
					new Metric( [ 'name' => 'activeUsers' ] )
				]
			] );
		} catch ( ApiException $e ) {
			echo $e->getMessage() . PHP_EOL;
			die;
		}

		// Parsing
		foreach ( $result3->getRows() as $k => $row ) {
			if ( $k == 0 ) {
				$sheets[ $sheet ][] = [
					'Device Category', 'Sessions', 'Users'
				];
			}
			$sheets[ $sheet ][] = [
				ucwords( $row->getDimensionValues()[0]->getValue() ), $row->getMetricValues()[0]->getValue(), $row->getMetricValues()[1]->getValue()
			];
			$graphs[ $ga_acct_name.'-devices' ]['labels'][] = ucwords( $row->getDimensionValues()[0]->getValue() );
			$graphs[ $ga_acct_name.'-devices' ]['datasets'][0]['data'][] = $row->getMetricValues()[1]->getValue();
			$graphs[ $ga_acct_name.'-devices' ]['datasets'][0]['backgroundColor'][] = $ga_device_colors[ $row->getDimensionValues()[0]->getValue() ];
		}

		// Inserting blank rows
		$sheets[ $sheet ][] = [
			'', '', ''
		];
		$sheets[ $sheet ][] = [
			'', '', ''
		];

		// Overall information
		$sheets[ $sheet ][] = [
			'Total Sessions', $result2->getTotals()[0]->getMetricValues()[0]->getValue()
		];
		$sheets[ $sheet ][] = [
			'Total Users', $result2->getTotals()[0]->getMetricValues()[1]->getValue()
		];

		$shows = [
			[
				'slug' => 'houston-matters',
				'title' => 'Houston Matters'
			], [
				'slug' => 'town-square',
				'title' => 'Town Square'
			], [
				'slug' => 'i-see-u',
				'title' => 'I SEE U'
			]
		];
		foreach ( $shows as $show ) {
			$show_graph = 'ga-'.$show['slug'].'-articles';
			try {
				$result = $analytics->runReport( [
					'property'        => 'properties/' . $ga,
					'dateRanges'      => [
						new DateRange( [
							'start_date' => $start,
							'end_date'   => $end,
						] ),
					],
					'dimensions'      => [
						new Dimension( [ 'name' => 'pagePath' ] )
					],
					'metrics'         => [
						new Metric( [ 'name' => 'screenPageViews' ] ),
						new Metric( [ 'name' => 'activeUsers' ] )
					],
					'dimensionFilter' => new FilterExpression( [
						'filter' => new Filter( [
							'field_name'    => 'pagePath',
							'string_filter' => new Filter\StringFilter( [
								'match_type' => Filter\StringFilter\MatchType::BEGINS_WITH,
								'value'      => '/articles/shows/' . $show['slug']
							] )
						] )
					] ),
					'limit'           => 25
				] );
			} catch ( ApiException $e ) {
				echo $e->getMessage() . PHP_EOL;
				die;
			}

			$sheet = 'Top Stories ('.$show['title'].')';
			$sheets[ $sheet ] = [];


			// Parsing the numbers from GA
			foreach ( $result->getRows() as $k => $row ) {
				if ( $k == 0 ) {
					$sheets[ $sheet ][] = [
						'Article Info', '', '', '', '', 'Pageviews', '', 'Pageviews from Source', '', '', '', '', '', 'Source Types', '', '', '', ''
					];
					$sheets[ $sheet ][] = [
						'Title', 'URL', 'Author', 'Date', 'Categories and Tags', 'Total', 'Unique', 'Direct', 'Google', 'Facebook', 'Twitter', 'RSS Feeds', 'Newsbreak App', 'Direct/No Referrer', 'Organic', 'Email', 'Referral', 'Social'
					];
				}
				try {
					$gaSources = googleArticleSources( $row );
				} catch ( ApiException $e ) {
					echo $e->getMessage() . PHP_EOL;
					die;
				}

				if ( !empty( $gaSources ) ) {
					// Adding the row to the sheet
					$sheets[ $sheet ][] = $gaSources;

					//Mapping the data into the graphing data
					$graphs[ $show_graph ]['labels'][] = $gaSources[0];
					$graphs[ $show_graph ]['datasets'][0]['data'][] = $gaSources[7];
					$graphs[ $show_graph ]['datasets'][1]['data'][] = $gaSources[8];
					$graphs[ $show_graph ]['datasets'][2]['data'][] = $gaSources[9];
					$graphs[ $show_graph ]['datasets'][3]['data'][] = $gaSources[10];
					$graphs[ $show_graph ]['datasets'][4]['data'][] = $gaSources[11];
					$graphs[ $show_graph ]['datasets'][5]['data'][] = $gaSources[12];
					$graphs[ $show_graph ]['datasets'][6]['data'][] = $gaSources[5] - ( $gaSources[7] + $gaSources[8] + $gaSources[9] + $gaSources[10] + $gaSources[11] + $gaSources[12] );
				}
			}
		}
	}