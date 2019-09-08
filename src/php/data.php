<?php

namespace Shea\GovHack19;

use SimplePie;
use SimplePie_Item;

/**
 * Handles the retrieval and preparation of data.
 * @package Shea\GovHack19
 */
class Data {

	/**
	 * URL to bushfire alerts RSS feed
	 */
	const BUSHFIRE_ALERTS_RSS = 'http://www.fire.tas.gov.au/Show?pageId=colBushfireSummariesRss';

	/**
	 * Base URL used for embedding Google Maps
	 */
	private $maps_url_base;

	/**
	 * Path to directory where data files are stored.
	 * @var string
	 */
	protected $data_dir;

	/**
	 * URL to directory where data files are stored.
	 * @var string
	 */
	protected $data_url;

	/**
	 * Class constructor.
	 *
	 * @param string $data_dir
	 * @param string $data_url
	 */
	public function __construct( $data_dir, $data_url ) {
		$this->data_dir = rtrim( $data_dir, '/' ) . '/';
		$this->data_url = rtrim( $data_url, '/' ) . '/';

		$maps_key = file_get_contents( $this->data_dir . 'mapskey' );
		$this->maps_url_base = 'https://www.google.com/maps/embed/v1/search?key=' . $maps_key;
	}

	/**
	 * Retrieve data from the fire alert feed.
	 * @return array
	 */
	public function retrieve_fire_data() {
		$feed = new SimplePie();
		$feed->set_feed_url( self::BUSHFIRE_ALERTS_RSS );

		$cache_dir = $this->data_dir . '.rsscache';
		if ( is_writable( $cache_dir ) ) {
			$feed->set_cache_location( $cache_dir );
		} else {
			$feed->enable_cache( false );
		}

		$feed->init();

		$data = [];

		/** @var SimplePie_Item $item */
		foreach ( $feed->get_items() as $item ) {

			// extract the incident latitude and longitude from the appropriate tag
			$point = $item->get_item_tags( SIMPLEPIE_NAMESPACE_GEORSS, 'point' );
			preg_match( '/^((?:-)?[0-9]+(?:\.[0-9]+)) ((?:-)?[0-9]+(?:\.[0-9]+))$/', trim( $point[0]['data'] ), $location );

			$item_data = [
				'id'      => $item->get_id( true ),
				'title'   => $item->get_title(),
				'date'    => $item->get_date(),
				'updated' => $item->get_updated_date(),
				'level'   => $item->get_category()->get_term(),
				'link'    => $item->get_link(),
				'lat'     => $location[1],
				'long'    => $location[2],
				'map_url' => $this->maps_url_base . '&q=' . urlencode( $location[1] . ',' . $location[2] ),
			];

			$data[] = $item_data;
		}

		return $data;
	}

	public function load_tree_data() {
		$filepath = $this->data_dir . 'trees/trees.csv';
		$data = [];

		if ( false === ( $handle = fopen( $filepath, 'r' ) ) ) {
			return $data;
		}

		$column_indices = array_flip( array_map( 'strtolower', fgetcsv( $handle, 1000, ',' ) ) );

		$column_map = [
			'name'     => 'botanical_id',
			'url'      => 'data_sheet_url',
			'adr'      => 'address',
			'address'  => 'full_address',
			'lat'      => 'lat',
			'long'     => 'long',
			'place_id' => 'place_id',
		];

		while ( false !== ( $row = fgetcsv( $handle, 1000, ',' ) ) ) {
			$row_data = [];

			foreach ( $column_map as $field => $column ) {
				$row_data[ $field ] = isset( $row[ $column_indices[ $column ] ] ) ? $row[ $column_indices[ $column ] ] : '';
			}

			$row_data['lat'] = (float) $row_data['lat'];
			$row_data['long'] = (float) $row_data['long'];

			$filename = pathinfo( parse_url( $row_data['url'], PHP_URL_PATH ), PATHINFO_FILENAME );
			$image_url = 'trees/images/' . $filename . '-000.png';

			$row_data['image'] = file_exists( $this->data_dir . $image_url ) ? ( $this->data_url . $image_url ) : '';

			$data[] = $row_data;
		}

		fclose( $handle );
		return $data;
	}

	public function load_building_data() {
		$data = [];

		$contents = file_get_contents( $this->data_dir . 'buildings/libraries.json' );
		$contents = json_decode( utf8_encode( $contents ) );

		foreach ( $contents->features as $feature ) {
			$data[] = [
				'type'    => 'library',
				'address' => $feature->properties->address,
				'long'     => $feature->geometry->coordinates[0],
				'lat'    => $feature->geometry->coordinates[1],
				'title'   => $feature->properties->title . ' Public Library',
				'url'     => 'https://libraries.tas.gov.au' . $feature->properties->url,
			];
		}

		return $data;
	}
}
