<?php
	global $sheets, $graphs, $startu, $endu;
	/**
	 * Triton Webcast Metrics. If you have a username and password for the analytics dashboard, you can use this
	 *
	 * A note: I could never get Triton to give me any documentation for the API, so I basically spent some time
	 * 		watching the Network panel of Chrome Developer Tools and worked backwards. All of the requests that went
	 *		to wcm.api.tritondigital.com gave it away
	 * Command to generate a new bearer token
	 * curl -X POST https://login.tritondigital.com/authenticate  -H 'Authorization: Basic <BASE64 ENCODE OF USERNAME:PASSWORD>' -H "Content-Type: application/json"
	 */
	$wcm_base = 'https://wcm.api.tritondigital.com/';
	$wcm_data = [
		'listeners' => [
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

	// Loop through the stations/streams
	foreach ( $stations as $k => $s ) {

		$get_context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'header' => "Authorization: Bearer " . WCM_BEARER . "\r\n"
			]
		]);
		for ( $i = $startu; $i < $endu; ) {
			$devices_out = false;
			$attempts = 0;
			while ( $devices_out === FALSE && $attempts < 3 ) {
				$devices_out = file_get_contents( $wcm_base . 'realtime/station/' . $s . '/timebreakdown/' . date( 'Y-m-d', $i ) . '?timezoneId=America/Chicago', FALSE, $get_context );
				$attempts++;
			}
			$data = json_decode( $devices_out, true );
			$wcm_data['listeners'][ $k ] = array_merge( $wcm_data['listeners'][ $k ], $data['listenersByHour'] );
			$i += 86400;
		}
	}

	// Parsing the listener numbers
	$sheet = 'Triton Listeners';
	$sheets[ $sheet ][] = [
		'Time','News','Classical','The Vibe'
	];

	/**
	 * Since the time periods match up, you can loop through one array and then directly access the others
	 */
	foreach ( $wcm_data['listeners']['news'] as $k => $v ) {
		$time_format = date( 'Y-m-d g:i A', strtotime( $v['time'] ) );
		// Enter the data as a row in the spreadsheet
		$sheets[ $sheet ][] = [
			$time_format, $wcm_data['listeners']['news'][ $k ]['value'], $wcm_data['listeners']['classical'][ $k ]['value'], $wcm_data['listeners']['mixtape'][ $k ]['value']
		];

		// Map it to the graphing data
		$graphs['triton-hourly']['labels'][] = $time_format;
		$graphs['triton-hourly']['datasets'][0]['data'][] = $wcm_data['listeners']['news'][ $k ]['value'];
		$graphs['triton-hourly']['datasets'][1]['data'][] = $wcm_data['listeners']['classical'][ $k ]['value'];
		$graphs['triton-hourly']['datasets'][2]['data'][] = $wcm_data['listeners']['mixtape'][ $k ]['value'];

		$graphs['overall-totals']['triton-news']['data'] += $wcm_data['listeners']['news'][ $k ]['value'];
		$graphs['overall-totals']['triton-classical']['data'] += $wcm_data['listeners']['classical'][ $k ]['value'];
		$graphs['overall-totals']['triton-mixtape']['data'] += $wcm_data['listeners']['mixtape'][ $k ]['value'];
	}