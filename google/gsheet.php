<?php
	/**
	 * Use the Google API PHP Client to create and authenticate a Google Drive Client
	 * If an access token already exists, it will use that, or it will walk you through obtaining one
	 */
	function getDriveClient() {
		$client = new Google_Client();
		$client->setApplicationName( 'Google Drive API PHP Quickstart' );
		$client->setScopes( 'https://www.googleapis.com/auth/drive' );
		$client->setAuthConfig( GDRIVE_CREDS );
		$client->setAccessType( 'offline' );

		// Load previously authorized credentials from a file.
		$credentialsPath = GDRIVE_TOKEN;
		if ( file_exists( $credentialsPath ) ) :
			$accessToken = json_decode( file_get_contents( $credentialsPath ), true );
		else :
			// Request authorization from the user.
			$authUrl = $client->createAuthUrl();
			printf( "Open the following link in your browser:\n%s\n", $authUrl );
			print 'Enter verification code: ';
			$authCode = trim( fgets( STDIN ) );

			// Exchange authorization code for an access token.
			$accessToken = $client->fetchAccessTokenWithAuthCode( $authCode );

			// Check to see if there was an error.
			if ( array_key_exists( 'error', $accessToken ) ) :
				throw new Exception( join( ', ', $accessToken ) );
			endif;

			// Store the credentials to disk.
			if ( !file_exists( dirname( $credentialsPath ) ) ) :
				mkdir( dirname( $credentialsPath ), 0700, true );
			endif;
			file_put_contents( $credentialsPath, json_encode( $accessToken ) );
			printf( "Credentials saved to %s\n", $credentialsPath );
		endif;
		$client->setAccessToken( $accessToken );

		// Refresh the token if it's expired.
		if ( $client->isAccessTokenExpired() ) :
			$client->fetchAccessTokenWithRefreshToken( $client->getRefreshToken() );
			file_put_contents( $credentialsPath, json_encode( $client->getAccessToken() ) );
		endif;
		return $client;
	}

	// Set up Drive Client and Drive Service Client
	$client = getDriveClient();
	$driveService = new Google_Service_Drive( $client );

	/**
	 * Layout basic file metadata, since we are going to be converting the XLSX file into a Sheet
	 * If you want to put the Sheet in a folder, set GDRIVE_PARENT in .env with the folder ID
	 */
	$newFileMetadata = new Google_Service_Drive_DriveFile([
		'name' => 'Weekly Analytics for the Week of '.date( 'Y-m-d', $startu ),
		'mimeType' => 'application/vnd.google-apps.spreadsheet',
		'parents' => [ GDRIVE_PARENT ]
	]);

	// Pull in the XLSX file data into a variable
	$content = file_get_contents( BASE . DS . "data" . DS. "analytics-".date( 'Y-m-d', $run_date ).".xlsx" );

	// Create the Sheet in Google Drive
	$file = $driveService->files->create( $newFileMetadata, [
		'data' => $content,
		'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'uploadType' => 'multipart',
		'fields' => 'id'
	]);

	// Confirm it as successful
	printf( $FG_BR_GREEN . $BG_BLACK . $FS_BOLD . "File ID: %s Created" . $RESET_ALL . PHP_EOL, $file->id );
	$file_url = 'https://docs.google.com/spreadsheets/d/' . $file->id . '/view';
?>