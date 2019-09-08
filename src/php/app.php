<?php

namespace Shea\GovHack19;

/**
 * Main application class.
 * @package Shea\GovHack19
 */
class App {

	/**
	 * Instance of Views class.
	 * @var Views
	 */
	protected $views;

	/**
	 * Instance of Data class.
	 * @var Data
	 */
	protected $data;

	/**
	 * Base application directory.
	 * @var string
	 */
	protected $base_dir;

	/**
	 * Base source files directory
	 * @var string
	 */
	protected $source_dir;

	/**
	 * Class constructor.
	 *
	 * @param string $app_path Full absolute path to base application directory.
	 */
	public function __construct( $app_path ) {
		$this->base_dir = rtrim( $app_path, '/' );
		$this->source_dir = $this->base_dir . '/src';

		$this->views = new Views( $this->source_dir );
		$this->data = new Data( $this->base_dir . '/data', $this->views->get_base_path() . '/data' );
	}

	protected function load_index( $alert_data ) {
		$this->views->load_template( 'index', [
			'alerts' => $alert_data,
		] );
	}

	protected function load_alert( $alert_data ) {
		$this->views->load_template( 'alert', [
			'alert' => $alert_data,
		] );
	}

	/**
	 * Run the application.
	 */
	public function start() {
		$alert_data = $this->data->retrieve_fire_data();

		$path = preg_split( '|/|', $this->views->get_path(), -1, PREG_SPLIT_NO_EMPTY );

		if ( ! $path ) {
			$this->load_index( $alert_data );
			return;
		}

		if ( count( $path ) > 1 && 'alerts' === $path[0] ) {

			foreach ( $alert_data as $alert ) {
				if ( $alert['id'] === $path[1] ) {
					$this->load_alert( $alert );
					return;
				}
			}
		}

		$this->load_index( $alert_data );
	}
}
