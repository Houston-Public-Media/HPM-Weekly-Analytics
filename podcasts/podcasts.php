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
				if ( $slug == 'engines-of-our-ingenuity' ) :
					$graphs['overall-totals'][$slug]['data']['downloads'] = ( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) );
					$graphs['overall-totals'][$slug]['data']['downloaders'] = ( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) );
					$sheets[$sheet][] = [
						'Engines of Our Ingenuity',
						( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) ),
						( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) )
					];
				elseif ( $slug == 'houston-matters' ) :
					$graphs['overall-totals'][$slug]['data']['downloads'] = ( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) );
					$graphs['overall-totals'][$slug]['data']['downloaders'] = ( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) );
					$sheets[$sheet][] = [
						'Houston Matters',
						( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) ),
						( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) )
					];
				elseif ( $slug == 'party-politics' ) :
					$graphs['overall-totals'][$slug]['data']['downloads'] = ( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) );
					$graphs['overall-totals'][$slug]['data']['downloaders'] = ( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) );
					$sheets[$sheet][] = [
						'Party Politics',
						( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) ),
						( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) )
					];
				elseif ( $slug == 'unwrap-your-candies-now' ) :
					$graphs['overall-totals'][$slug]['data']['downloads'] = ( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) );
					$graphs['overall-totals'][$slug]['data']['downloaders'] = ( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) );
					$sheets[$sheet][] = [
						'Unwrap Your Candies Now',
						( empty( intval( $data[1] ) ) ? 0 : intval( $data[1] ) ),
						( empty( intval( $data[2] ) ) ? 0 : intval( $data[2] ) )
					];
				endif;
			endif;
			$row++;
		endwhile;
		fclose($handle);
	endif;