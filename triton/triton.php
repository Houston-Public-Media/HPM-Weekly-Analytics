<?php
	/**
	 * Triton Webcast Metrics. If you have a username and password for the analytics dashboard, you can use this
	 *
	 * A note: I could never get Triton to give me any documentation for the API, so I bascially spent some time
	 * 		watching the Network panel of Chrome Developer Tools and worked backwards. All of the requests that went
	 *		to wcm2api.tritondigital.com gave it away
	 */
	$curl = curl_init();
	$wcm_user = WCM_USER;
	$wcm_pass = WCM_PASSWORD;
	$wcm_base = 'https://wcm2api.tritondigital.com/';
	$wcm_data = [
		'listeners' => [
			'news' => [],
			'classical' => [],
			'mixtape' => []
		],
		'devices' => [
			'news' => [],
			'classical' => [],
			'mixtape' => []
		]
	];

	// Modify this with your station IDs and names. We have 3 streams we track
	$stations = [
		'news' => 23682,
		'classical' => 23683,
		'mixtape' => 23685
	];

	/**
	 * For basic number of listeners, Triton can pull data up to the current day or so, but I also want information
	 * 		about what devices people are using, which is always 2 weeks behind
	 */
	$tstart = $startu - ( 60 * 60 * 24 * 14 );
	$tend = $endu - ( 60 * 60 * 24 * 14 );
	$date_start = date( 'Y-m-d', $tstart )."T00:00:00.000Z";
	$date_end = date( 'Y-m-d', $tend )."T00:00:00.000Z";

	// Loop through the stations/streams
	foreach ( $stations as $k => $s ) :

		/**
		 * I copied this JSON request body wholesale from the analytics dashboard. It will give you a nested list of devices
		 * 		organized by type
		 */
		$devices = '{"op":"limit","operand":{"op":"sort","operand":{"op":"apply","operand":{"op":"apply","operand":{"op":"split","operand":{"op":"filter","operand":{"op":"filter","operand":{"op":"filter","operand":{"op":"filter","operand":{"op":"ref","name":"wcm"},"expression":{"op":"overlap","operand":{"op":"ref","name":"__time"},"expression":{"op":"literal","value":{"start":"'.$date_start.'","end":"'.$date_end.'"},"type":"TIME_RANGE"}}},"expression":{"op":"is","operand":{"op":"ref","name":"ivt"},"expression":{"op":"literal","value":"valid"}}},"expression":{"op":"is","operand":{"op":"ref","name":"station"},"expression":{"op":"literal","value":"'.$s.'"}}},"expression":{"op":"or","operand":{"op":"or","operand":{"op":"or","operand":{"op":"or","operand":{"op":"or","operand":{"op":"or","operand":{"op":"and","operand":{"op":"is","operand":{"op":"ref","name":"dayOfWeek"},"expression":{"op":"literal","value":1}},"expression":{"op":"overlap","operand":{"op":"ref","name":"hour"},"expression":{"op":"literal","value":{"start":0,"end":24},"type":"NUMBER_RANGE"}}},"expression":{"op":"and","operand":{"op":"is","operand":{"op":"ref","name":"dayOfWeek"},"expression":{"op":"literal","value":2}},"expression":{"op":"overlap","operand":{"op":"ref","name":"hour"},"expression":{"op":"literal","value":{"start":0,"end":24},"type":"NUMBER_RANGE"}}}},"expression":{"op":"and","operand":{"op":"is","operand":{"op":"ref","name":"dayOfWeek"},"expression":{"op":"literal","value":3}},"expression":{"op":"overlap","operand":{"op":"ref","name":"hour"},"expression":{"op":"literal","value":{"start":0,"end":24},"type":"NUMBER_RANGE"}}}},"expression":{"op":"and","operand":{"op":"is","operand":{"op":"ref","name":"dayOfWeek"},"expression":{"op":"literal","value":4}},"expression":{"op":"overlap","operand":{"op":"ref","name":"hour"},"expression":{"op":"literal","value":{"start":0,"end":24},"type":"NUMBER_RANGE"}}}},"expression":{"op":"and","operand":{"op":"is","operand":{"op":"ref","name":"dayOfWeek"},"expression":{"op":"literal","value":5}},"expression":{"op":"overlap","operand":{"op":"ref","name":"hour"},"expression":{"op":"literal","value":{"start":0,"end":24},"type":"NUMBER_RANGE"}}}},"expression":{"op":"and","operand":{"op":"is","operand":{"op":"ref","name":"dayOfWeek"},"expression":{"op":"literal","value":6}},"expression":{"op":"overlap","operand":{"op":"ref","name":"hour"},"expression":{"op":"literal","value":{"start":0,"end":24},"type":"NUMBER_RANGE"}}}},"expression":{"op":"and","operand":{"op":"is","operand":{"op":"ref","name":"dayOfWeek"},"expression":{"op":"literal","value":7}},"expression":{"op":"overlap","operand":{"op":"ref","name":"hour"},"expression":{"op":"literal","value":{"start":0,"end":24},"type":"NUMBER_RANGE"}}}}},"name":"station","expression":{"op":"ref","name":"station"},"dataName":"wcm"},"expression":{"op":"countDistinct","operand":{"op":"ref","name":"wcm"},"expression":{"op":"ref","name":"cume"}},"name":"CUME"},"expression":{"op":"apply","operand":{"op":"limit","operand":{"op":"sort","operand":{"op":"apply","operand":{"op":"split","operand":{"op":"ref","name":"wcm"},"name":"Family","expression":{"op":"fallback","operand":{"op":"lookup","operand":{"op":"ref","name":"deviceFamily"},"lookupFn":"deviceFamilies"},"expression":{"op":"literal","value":"Not Provided"}},"dataName":"wcm"},"expression":{"op":"countDistinct","operand":{"op":"ref","name":"wcm"},"expression":{"op":"ref","name":"cume"}},"name":"CUME"},"expression":{"op":"ref","name":"CUME"},"direction":"descending"},"value":10},"expression":{"op":"limit","operand":{"op":"sort","operand":{"op":"apply","operand":{"op":"split","operand":{"op":"ref","name":"wcm"},"name":"Device","expression":{"op":"fallback","operand":{"op":"lookup","operand":{"op":"ref","name":"device"},"lookupFn":"devices"},"expression":{"op":"literal","value":"Not Provided"}},"dataName":"wcm"},"expression":{"op":"countDistinct","operand":{"op":"ref","name":"wcm"},"expression":{"op":"ref","name":"cume"}},"name":"CUME"},"expression":{"op":"ref","name":"CUME"},"direction":"descending"},"value":10},"name":"level2"},"name":"level1"},"expression":{"op":"ref","name":"CUME"},"direction":"descending"},"value":10}';

		$get_context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'header' => "Authorization: Basic ".base64_encode( $wcm_user.":".$wcm_pass )."\r\n"
			]
		]);
		$post_context = stream_context_create([
			'http' => [
				'method' => 'POST',
				'header' => "Authorization: Basic ".base64_encode( $wcm_user.":".$wcm_pass )."\r\nCache-Control: no-cache\r\nContent-Length: ".strlen( $devices )."\r\nContent-Type: application/json\r\n",
				'content' => $devices
			]
		]);
		for ( $i = $startu; $i < $endu; ) :
			$data = json_decode( file_get_contents( $wcm_base.'realtime/station/'.$s.'/timebreakdown/'.date( 'Y-m-d', $i ).'?timezoneId=US%2FCentral', FALSE, $get_context ), true );
			$wcm_data['listeners'][$k] = array_merge( $wcm_data['listeners'][$k], $data['listenersByHour'] );
			$i += 86400;
		endfor;

		// Pull device usage by type for the stream
		// $wcm_data['devices'][$k] = json_decode( file_get_contents( $wcm_base.'audience_analysis/station/'.$s, FALSE, $post_context ), true );
	endforeach;

	// Parsing the listener numbers
	$sheet = 'Triton Listeners';
	$sheets[$sheet][] = [
		'Time','News','Classical','Mixtape'
	];

	/**
	 * Since the time periods match up, you can loop through one array and then directly access the others
	 */
	foreach ( $wcm_data['listeners']['news'] as $k => $v ) :
		$time_format = date( 'Y-m-d g:i A', strtotime( $v['time'] ) );
		// Enter the data as a row in the spreadsheet
		$sheets[$sheet][] = [
			$time_format, $wcm_data['listeners']['news'][$k]['value'], $wcm_data['listeners']['classical'][$k]['value'], $wcm_data['listeners']['mixtape'][$k]['value']
		];

		// Map it to the graphing data
		$graphs['triton-hourly']['labels'][] = $time_format;
		$graphs['triton-hourly']['datasets'][0]['data'][] = $wcm_data['listeners']['news'][$k]['value'];
		$graphs['triton-hourly']['datasets'][1]['data'][] = $wcm_data['listeners']['classical'][$k]['value'];
		$graphs['triton-hourly']['datasets'][2]['data'][] = $wcm_data['listeners']['mixtape'][$k]['value'];

		$graphs['overall-totals']['triton-news']['data'] += $wcm_data['listeners']['news'][$k]['value'];
		$graphs['overall-totals']['triton-classical']['data'] += $wcm_data['listeners']['classical'][$k]['value'];
		$graphs['overall-totals']['triton-mixtape']['data'] += $wcm_data['listeners']['mixtape'][$k]['value'];
	endforeach;

	// Parsing the device numbers
	// $sheet = 'Triton By Device';
	// $sheets[$sheet][] = [
	// 	'Station CUME by Device and Device Family ('.date( 'Y-m-d', $tstart ).' - '.date( 'Y-m-d', $tend ).')'
	// ];


	// $sheets[$sheet][] = [
	// 	'Station',
	// 	'Device Family',
	// 	'Device',
	// 	'CUME',
	// 	'CUME Percent'
	// ];
	// foreach ( $stations as $k => $v ) :
	// 	/**
	// 	 * Since I don't know how many individual devices will be included in the data, I came up with a system
	// 	 * 		to generate colors for them in the graphing data. Each device family has a base hue, the saturation
	// 	 * 		is 100%, and the lightness is 50 + ( 5 * number of devices in the family ) %
	// 	 */
	// 	$device_colors = [
	// 		'Mobile Device' => [
	// 			'base' => 0,
	// 			'num' => 0
	// 		],
	// 		'Desktop/Laptop/Server' => [
	// 			'base' => 120,
	// 			'num' => 0
	// 		],
	// 		'Smart Speaker' => [
	// 			'base' => 240,
	// 			'num' => 0
	// 		],
	// 		'Not Provided' => [
	// 			'base' => 180,
	// 			'num' => 0
	// 		],
	// 		'Unknown' => [
	// 			'base' => 300,
	// 			'num' => 0
	// 		],
	// 		'Digital Media Player' => [
	// 			'base' => 60,
	// 			'num' => 0
	// 		],
	// 		'Smart TV' => [
	// 			'base' => 20,
	// 			'num' => 0
	// 		],
	// 		'Unspecified' => [
	// 			'base' => 340,
	// 			'num' => 0
	// 		],
	// 	];

	// 	// Loop through the station data
	// 	foreach ( $wcm_data['devices'][$k]['data'] as $n ) :

	// 		// Overall station CUME information
	// 		$overall_cume = $n['CUME'];
	// 		$sheets[$sheet][] = [
	// 			ucwords( $k ),
	// 			'',
	// 			'',
	// 			$overall_cume,
	// 			'100.0%'
	// 		];

	// 		// Loop through each device family
	// 		foreach ( $n['level1']['data'] as $lvl ) :
	// 			// Gather CUME for each family, and also calculate percentage of overall station CUME
	// 			$family_cume = $lvl['CUME'];
	// 			$family_cume_percent = round( ( $family_cume/$overall_cume ) * 100, 1 )."%";
	// 			$sheets[$sheet][] = [
	// 				ucwords( $k ),
	// 				$lvl['Family'],
	// 				'',
	// 				$family_cume,
	// 				$family_cume_percent
	// 			];

	// 			// Loop through each family's device
	// 			foreach ( $lvl['level2']['data'] as $lvl2 ) :
	// 				// Gather CUME for each device, and also calculate percentage of device family
	// 				$device_cume = $lvl2['CUME'];
	// 				$device_cume_percent = round( ( $device_cume/$family_cume ) * 100, 1 )."%";
	// 				$sheets[$sheet][] = [
	// 					ucwords( $k ),
	// 					$lvl['Family'],
	// 					$lvl2['Device'],
	// 					$device_cume,
	// 					$device_cume_percent
	// 				];

	// 				// Calculate the color for each device for the graphing app
	// 				$dcolor = 'hsla('.$device_colors[ $lvl['Family'] ]['base'].',100%,'.( 50 + ( $device_colors[ $lvl['Family'] ]['num'] * 5 ) ).'%,1)';
	// 				$device_colors[ $lvl['Family'] ]['num']++;

	// 				// Map the graphing data
	// 				$graphs['triton-'.$k.'-devices']['labels'][] = $lvl2['Device'];
	// 				$graphs['triton-'.$k.'-devices']['datasets'][0]['data'][] = $device_cume;
	// 				$graphs['triton-'.$k.'-devices']['datasets'][0]['backgroundColor'][] = $dcolor;
	// 			endforeach;
	// 		endforeach;
	// 	endforeach;
	// endforeach;
?>