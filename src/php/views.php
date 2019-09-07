<?php

namespace Shea\GovHack19;

use Twig;
use Twig\TwigFilter;

/**
 * Handles app URL routing and view templates.
 * @package Shea\GovHack19
 */
class Views {

	const MINUTE_IN_SECONDS = 60;
	const HOUR_IN_SECONDS = self::MINUTE_IN_SECONDS * 60;
	const DAY_IN_SECONDS = self::HOUR_IN_SECONDS * 24;

	/**
	 * Full absolute path to the template directory.
	 * @var string
	 */
	protected $template_dir;

	/**
	 * Class constructor.
	 *
	 * @param string $base_dir
	 */
	public function __construct( $base_dir ) {
		$this->template_dir = $this->untrailingslash( $base_dir ) . '/templates';
	}

	public function untrailingslash( $url ) {
		return rtrim( $url, '/\\' );
	}

	public function get_base_path() {
		$base_path = parse_url( $_SERVER['DOCUMENT_URI'], PHP_URL_PATH );

		if ( 'index.php' === substr( $base_path, -strlen( 'index.php' ) ) ) {
			$base_path = substr( $base_path, 0, -strlen( 'index.php' ) );
		}

		return $base_path;
	}

	/**
	 * Extract the application path from the current URL
	 * @return string
	 */
	public function get_path() {
		$current_path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
		return str_replace( $this->get_base_path(), '', $current_path );
	}

	/**
	 * Load a specified template file.
	 *
	 * @param string $template
	 * @param array  $data
	 */
	public function load_template( $template, $data = [] ) {
		$loader = new Twig\Loader\FilesystemLoader( $this->template_dir );
		$twig = new Twig\Environment( $loader );

		$twig->addFilter( new TwigFilter( 'prepend_base', function ( $path ) {
			return rtrim( $this->get_base_path(), '/' ) . '/' . ltrim( $path, '/' );
		} ) );

		$twig->addFilter( new TwigFilter( 'human_time_diff', [ $this, 'human_time_diff' ] ) );

		try {
			echo $twig->render( $template . '.html', $data );

		} catch ( Twig\Error\Error $e ) {
			printf( 'An error occurred when loading the template %s: %s on line %d',
				str_replace( $this->template_dir . '/', '', $e->getFile() ),
				$e->getMessage(),
				$e->getTemplateLine()
			);
		}
	}

	/**
	 * Present a time in the recent past in a more human-readable format
	 *
	 * @param string $date_string
	 *
	 * @return string
	 */
	public function human_time_diff( $date_string ) {
		$time = strtotime( $date_string );
		$diff = time() - $time;

		if ( $diff <= 0 || $diff > self::DAY_IN_SECONDS ) {
			return $date_string;
		}

		$diff = (int) abs( $diff );

		$divider = self::MINUTE_IN_SECONDS;
		$unit = 'minute';

		if ( $diff >= self::HOUR_IN_SECONDS ) {
			$divider = self::HOUR_IN_SECONDS;
			$unit = 'hour';
		}

		$amount = min( 1, round( $diff / $divider ) );
		return sprintf( '%d %s%s ago', $amount, $unit, 1 === $amount ? '' : 's' );
	}
}
