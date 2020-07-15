<?php
	$sheet = 'Podcasts';
	$row = 0;
	/**
	 * Open the CSV report downloaded from StreamGuys. They have a way to set up weekly reports that they email you
	 * 		which makes downloading the report a lot easier.
	 * 		I then save it in the 'podcasts' folder with a filename 'podcasts-YYYY-MM-DD.csv' (same as the report end date)
	 *
	 */
	if ( ( $handle = fopen( BASE . DS . "podcasts" . DS . "podcasts-" . $end . ".csv", "r" ) ) !== FALSE ) :
		while ( ( $data = fgetcsv( $handle, 1000, "," ) ) !== FALSE ) :
			if ( $row === 0 ) :
				$sheets[$sheet][] = [
					'Name','Downloads','Downloaders'
				];
			else :
				$slug = str_replace( '/', '', $data[0] );
				if ( $slug === 'recast' ) :
					$slug = 'HPM Newscast New';
				endif;
				$pod_data = [
					'name' => ucwords( str_replace( '-', ' ', $slug ) ),
					'data' => [
						'downloads' => ( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) ),
						'downloaders' => ( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) )
					]
				];
				$graphs['overall-totals']['podcasts'][$slug] = $pod_data;
				$sheets[$sheet][] = [
					ucwords( str_replace( '-', ' ', $slug ) ),
					( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) ),
					( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) )
				];
			endif;
			$row++;
		endwhile;
		fclose($handle);
	endif;