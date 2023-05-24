<?php
	global $sheets, $graphs, $end;
	$sheet = 'Podcasts';
	$row = 0;
	/**
	 * Open the CSV report downloaded from StreamGuys. They have a way to set up weekly reports that they email you
	 * 		which makes downloading the report a lot easier.
	 * 		I then save it in the 'podcasts' folder with a filename 'podcasts-YYYY-MM-DD.csv' (same as the report end date)
	 *
	 */

	if ( ( $handle = fopen( BASE . DS . "podcasts" . DS . "podcasts-" . $end . ".csv", "r" ) ) !== FALSE ) {
		while ( ( $data = fgetcsv( $handle, 1000 ) ) !== FALSE ) {
			if ( $row === 0 ) {
				$sheets[ $sheet ][] = [
					'Name','Downloads','Downloaders'
				];
				$pod_head = array_flip( $data );
			} else {
				$slug = str_replace( '/', '', $data[ $pod_head[ 'Programs' ] ] );
				if ( $slug === 'recast' ) {
					$slug = 'HPM Newscast New';
				}
				$pod_data = [
					'name' => ucwords( str_replace( '-', ' ', $slug ) ),
					'data' => [
						'downloads' => csv_int_check( 'Downloads', $data, $pod_head ),
						'downloaders' => csv_int_check( 'Downloaders', $data, $pod_head )
					]
				];
				$graphs['overall-totals']['podcasts'][ $slug ] = $pod_data;
				$sheets[ $sheet ][] = [
					ucwords( str_replace( '-', ' ', $slug ) ),
					csv_int_check( 'Downloads', $data, $pod_head ),
					csv_int_check( 'Downloaders', $data, $pod_head )
				];
			}
			$row++;
		}
		fclose( $handle );
	}