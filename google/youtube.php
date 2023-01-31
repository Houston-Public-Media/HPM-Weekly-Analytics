<?php
	/**
	 * Use the Google API PHP Client to create and authenticate a YouTube Analytics Client
	 * If an access token already exists, it will use that, or it will walk you through obtaining one
	 */
	function getYTClient() {
		$client = new Google_Client();
		$client->setApplicationName( 'APIs Explorer PHP Samples' );
		$client->setScopes([ 'https://www.googleapis.com/auth/youtube.readonly' ]);
		$client->setAuthConfig( YT_CLIENT );
		$client->setAccessType( 'offline' );
		$client->setApprovalPrompt( 'force' );

		// Load previously authorized credentials from a file.
		$credentialsPath = YT_ACCESS;
		if ( file_exists( $credentialsPath ) ) {
			$accessToken = json_decode( file_get_contents( $credentialsPath ), true );
		} else {
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			printf( "Open the following link in your browser:\n%s\n", $authUrl );
			print 'Enter verification code: ';
			$authCode = trim( fgets( STDIN ) );

			// Exchange authorization code for an access token.
			$accessToken = $client->fetchAccessTokenWithAuthCode( $authCode );

			// Check to see if there was an error.
			if ( array_key_exists( 'error', $accessToken ) ) {
				throw new Exception( join( ', ', $accessToken ) );
			}

			// Store the credentials to disk.
			if ( !file_exists( dirname( $credentialsPath ) ) ) {
				mkdir( dirname( $credentialsPath ), 0700, true );
			}
			file_put_contents( $credentialsPath, json_encode( $accessToken ) );
			printf( "Credentials saved to %s\n", $credentialsPath );
		}
		$client->setAccessToken( $accessToken );

		// Refresh the token if it's expired.
		if ( $client->isAccessTokenExpired() ) {
			$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
			file_put_contents( $credentialsPath, json_encode( $client->getAccessToken() ) );
		}
		return $client;
	}

	$client = getYTClient();

	// Define service object for making API requests.
	$service = new Google_Service_YouTubeAnalytics( $client );

	// Pull top 20 videos by views for time period
	$params = [
		'dimensions' => 'video',
		'endDate' => $end,
		'ids' => 'channel=='.YT_CHANNEL_ID,
		'maxResults' => 20,
		'metrics' => 'views,likes,dislikes,comments,estimatedMinutesWatched,subscribersGained',
		'sort' => '-views',
		'startDate' => $start
	];

	$params_over = [
		'endDate' => $end,
		'ids' => 'channel=='.YT_CHANNEL_ID,
		'metrics' => 'views',
		'startDate' => $start
	];

	// Make the request, and set up intermediate arrays to hold the data
	$response = $service->reports->query( $params );
	$response_over = $service->reports->query( $params_over );
	$videos = $vid_ids = [];

	// Loop through the response rows
	foreach ( $response->rows as $r ) {
		// Save video IDs into an array so we can pull individual video analytics as a batch
		$vid_ids[] = $r[0];
		$videos[$r[0]] = [
			'Views' => ( empty( $r[1] ) ? 0 : $r[1] ),
			'Likes' => ( empty( $r[2] ) ? 0 : $r[2] ),
			'Dislikes' =>( empty( $r[3] ) ? 0 : $r[3] ),
			'Comments' => ( empty( $r[4] ) ? 0 : $r[4] ),
			'Estimated Minutes Watched' => ( empty( $r[5] ) ? 0 : $r[5] ),
			'Subscribers Gained' => ( empty( $r[6] ) ? 0 : $r[6] )
		];
	}
	$graphs['overall-totals']['youtube']['data'] = $response_over->rows[0][0];
	$vids = implode( ',', $vid_ids );
	$youtube = new Google_Service_YouTube( $client );

	// Pull extended analytics for those 20 videos
	$ytlist = $youtube->videos->listVideos(
		'snippet,statistics',
		[ 'id' => $vids ]
	);

	$sheet = 'Top YouTube Videos';
	$sheets[$sheet] = [];
	$sheets[$sheet][] = [
		'Title', 'URL', 'Date', 'Views', 'Likes', 'Dislikes', 'Comments', 'Estimated Minutes Watched', 'Subscribers Gained', 'Lifetime Views', 'Lifetime Likes', 'Lifetime Dislikes', 'Lifetime Favorites', 'Lifetime Comments'
	];
	foreach ( $ytlist['items'] as $yti ) {
		$ytid = $yti->id;

		// Map that data as a row for the spreadsheet
		$sheets[$sheet][] = [
			$yti->snippet->title,
			'https://youtube.com/watch?v='.$ytid,
			date( 'Y-m-d g:i A', strtotime( $yti->snippet->publishedAt ) ),
			( empty( $videos[ $ytid ]['Views'] ) ? 0 : $videos[ $ytid ]['Views'] ),
			( empty( $videos[ $ytid ]['Likes'] ) ? 0 : $videos[ $ytid ]['Likes'] ),
			( empty( $videos[ $ytid ]['Dislikes'] ) ? 0 : $videos[ $ytid ]['Dislikes'] ),
			( empty( $videos[ $ytid ]['Comments'] ) ? 0 : $videos[ $ytid ]['Comments'] ),
			( empty( $videos[ $ytid ]['Estimated Minutes Watched'] ) ? 0 : $videos[ $ytid ]['Estimated Minutes Watched'] ),
			( empty( $videos[ $ytid ]['Subscribers Gained'] ) ? 0 : $videos[ $ytid ]['Subscribers Gained'] ),
			( empty( $yti->statistics->viewCount ) ? 0 : $yti->statistics->viewCount ),
			( empty( $yti->statistics->likeCount ) ? 0 : $yti->statistics->likeCount ),
			( empty( $yti->statistics->dislikeCount ) ? 0 : $yti->statistics->dislikeCount ),
			( empty( $yti->statistics->favoriteCount ) ? 0 : $yti->statistics->favoriteCount ),
			( empty( $yti->statistics->commentCount ) ? 0 : $yti->statistics->commentCount )
		];

		// Map it as graphing data
		$graphs['youtube-videos-by-views']['labels'][] = $yti->snippet->title;
		$graphs['youtube-videos-by-views']['datasets'][0]['data'][] = ( empty( $videos[ $ytid ]['Views'] ) ? 0 : $videos[ $ytid ]['Views'] );
		$graphs['youtube-videos-by-views']['datasets'][1]['data'][] = ( empty( $videos[ $ytid ]['Estimated Minutes Watched'] ) ? 0 : $videos[ $ytid ]['Estimated Minutes Watched'] );

		$graphs['youtube-videos-by-engagement']['labels'][] = $yti->snippet->title;
		$graphs['youtube-videos-by-engagement']['datasets'][0]['data'][] = ( empty( $yti->statistics->commentCount ) ? 0 : $yti->statistics->commentCount );
		$graphs['youtube-videos-by-engagement']['datasets'][1]['data'][] = ( empty( $videos[ $ytid ]['Subscribers Gained'] ) ? 0 : $videos[ $ytid ]['Subscribers Gained'] );
		$graphs['youtube-videos-by-engagement']['datasets'][2]['data'][] = ( empty( $yti->statistics->likeCount ) ? 0 : $yti->statistics->likeCount );
	}
?>