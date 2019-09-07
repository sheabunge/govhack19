<?php

namespace Shea\GovHack19;

use SimplePie;
use SimplePie_Item;

class Data {
	const BUSHFIRE_ALERTS_RSS = 'http://www.fire.tas.gov.au/Show?pageId=colBushfireSummariesRss';

	const MAP_URL_BASE = 'https://www.google.com/maps/embed/v1/search?key=AIzaSyDHcTEGDOEvnSfv7oQbEcGxzIWFxPVDeM4';

	public function retrieve_fire_data() {
		$feed = new SimplePie();
		$feed->set_feed_url( self::BUSHFIRE_ALERTS_RSS );
		$feed->enable_cache( false );
		// $feed->set_cache_location( dirname( dirname( __DIR__ ) ) . '/dist/.rsscache' );
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
				'map_url' => self::MAP_URL_BASE . '&q=' . urlencode( $location[1] . ',' . $location[2] ),
			];

			$data[] = $item_data;
		}

		return $data;
	}
}
