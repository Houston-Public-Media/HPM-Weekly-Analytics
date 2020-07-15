<?php
	use Aws\Ses\SesClient;
	use Aws\Ses\Exception\SesException;
	use Aws\S3\S3Client;
	use Aws\S3\Exception\S3Exception;
	use Aws\CloudFront\CloudFrontClient;
	use Aws\CloudFront\Exception\CloudFrontException;
	use Aws\Exception\AwsException;

	if ( $emails ) :
		// If you chose to email the group, set up the AWS SES Client
		$client = SesClient::factory([
			'credentials' => [
				'key' => $aws_key,
				'secret' => $aws_secret
			],
			'version'=> 'latest',
			'region' => 'us-west-2'
		]);

		// Format and send the email. Remember to enter modify the email with your branding
		try {
			$result = $client->sendEmail([
				'Destination' => [
					'ToAddresses' => $email_arr
				],
				'Message' => [
					'Body' => [
						'Html' => [
							'Charset' => 'UTF-8',
							'Data' => '<h2 style="font-size: 20px; line-height: 24px;">HPM Analytics Report for the Week of '.date( 'F j, Y', $startu ).'</h2><p>The weekly analytics report for the week of '.date( 'F j, Y', $startu ).' is ready <a href="'.STORAGE_SHARE.'">for you to review</a>.</p><h4 style="font-size: 16px; line-height: 20px;">Charts, Graphs, Etc.</h4><p>You can view visualizations of these reports at <a href="'.APP_URL.'">'.APP_URL.'</a>.</p><h4 style="font-size: 16px; line-height: 20px;">Previous Weekly Reports</h4><p>Previous weekly reports can be <a href="'.STORAGE_SHARE.'">found here</a>.</p>'
						],
					'Text' => [
						'Charset' => 'UTF-8',
						'Data' => 'The weekly analytics report for the week of '.date( 'F j, Y', $startu ).' is ready for you to review:\n\n'.STORAGE_SHARE.'\n\n**Charts, Graphs, Etc.**\n\nVisualizations of these reports are available at '.APP_URL.' \n\n**Previous Reports**\n\nPrevious weekly reports can be found here:\n\n'.STORAGE_SHARE
					],
				],
				'Subject' => [
					'Charset' => 'UTF-8',
						'Data' => 'HPM Analytics Report for the Week of '.date( 'F j, Y', $startu )
					],
				],
				'Source' => FROM_EMAIL
			]);
			$messageId = $result->get( 'MessageId' );
			echo( $FG_BR_GREEN . $BG_BLACK . $FS_BOLD . "Email sent! Message ID: $messageId" . $RESET_ALL . PHP_EOL );
		} catch (SesException $error) {
			echo( $FG_BR_RED . $BG_BLACK . $FS_BOLD . "The email was not sent. Error message: ".$error->getAwsErrorMessage() . PHP_EOL );
		}
	endif;

	// Set up the S3 client
	$s3 = new S3Client([
		'credentials' => [
			'key' => $aws_key,
			'secret' => $aws_secret
		],
		'region' => 'us-west-2',
		'version' => 'latest'
	]);

	// Set up the Cloudfront Client
	$cf = new CloudFrontClient([
		'credentials' => [
			'key' => $aws_key,
			'secret' => $aws_secret
		],
		'region' => 'us-west-2',
		'version' => 'latest'
	]);

	$week_file = BASE . DS . 'data' . DS . date( 'Y-m-d', $startu ) . '.json';
	$reports_file = BASE . DS . 'data' . DS . 'reports.json';

	// Upload the data file for the week to Amazon S3
	try {
		$rs3 = $s3->putObject([
			'Bucket' => $s3_bucket,
			'Key' => 'assets/analytics/'.date( 'Y-m-d', $startu ).'.json',
			'SourceFile' => $week_file,
			'ACL' => 'public-read',
			'ContentType' => 'application/json'
		]);
	} catch (S3Exception $e) {
		header("HTTP/1.1 500 Server Error");
		echo $e->getMessage();
		die;
	} catch (AwsException $e) {
		header("HTTP/1.1 500 Server Error");
		echo $e->getAwsRequestId() . PHP_EOL . $e->getAwsErrorType() . PHP_EOL . $e->getAwsErrorCode() . PHP_EOL;
		die;
	}

	if ( $rerun == false ) :
		// Upload the reports file to Amazon S3
		try {
			$ss3 = $s3->putObject([
				'Bucket' => $s3_bucket,
				'Key' => 'assets/analytics/reports.json',
				'SourceFile' => $reports_file,
				'ACL' => 'public-read',
				'ContentType' => 'application/json'
			]);
		} catch (S3Exception $e) {
			header("HTTP/1.1 500 Server Error");
			echo $FG_BR_RED . $BG_BLACK . $FS_BOLD . $e->getMessage() . $RESET_ALL . PHP_EOL;
			die;
		} catch (AwsException $e) {
			header("HTTP/1.1 500 Server Error");
			echo $FG_BR_RED . $BG_BLACK . $FS_BOLD . $e->getAwsRequestId() . PHP_EOL . $e->getAwsErrorType() . PHP_EOL . $e->getAwsErrorCode() . $RESET_ALL . PHP_EOL;
			die;
		}
	endif;

	// Invalidate the cache for the /assets/analytics folder
	try {
		$rcf = $cf->createInvalidation([
			'DistributionId' => $cf_distro,
			'InvalidationBatch' => [
				'CallerReference' => microtime(),
				'Paths' => [
					'Items' => [ '/assets/analytics/*' ],
					'Quantity' => 1
				]
			]
		]);
	} catch (CloudFrontException $e) {
		header("HTTP/1.1 500 Server Error");
		echo $FG_BR_RED . $BG_BLACK . $FS_BOLD . $e->getMessage() . $RESET_ALL . PHP_EOL;
		die;
	} catch (AwsException $e) {
		header("HTTP/1.1 500 Server Error");
		echo $FG_BR_RED . $BG_BLACK . $FS_BOLD . $e->getAwsRequestId() . PHP_EOL . $e->getAwsErrorType() . PHP_EOL . $e->getAwsErrorCode() . $RESET_ALL . PHP_EOL;
		die;
	}
?>