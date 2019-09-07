<?php

namespace Shea\GovHack19;

use Twig;

class Views {

	protected $template_dir;

	/**
	 * Class constructor.
	 *
	 * @param string $base_dir
	 */
	public function __construct( $base_dir ) {
		$this->template_dir = rtrim( $base_dir, '/\\' ) . '/templates';
	}

	/**
	 * Load the current view.
	 */
	public function load() {
		$path = $this->get_path();

		if ( '' !== 'path' ) {
			$path = '';
		}

		$this->load_template( $path );
	}

	/**
	 * Extract the application path from the current URL
	 * @return string
	 */
	protected function get_path() {
		$base_path = parse_url( $_SERVER['DOCUMENT_URI'], PHP_URL_PATH );
		if ( 'index.php' === substr( $base_path, -strlen('index.php' ) ) ) {
			$base_path = substr( $base_path, 0, -strlen('index.php' ) );
		}

		$current_path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

		return str_replace( $base_path, '', $current_path );
	}

	/**
	 * Load a specified template file.
	 *
	 * @param string $template
	 */
	public function load_template( $template ) {

		if ( ! $template ) {
			$template = 'index';
		}

		$loader = new Twig\Loader\FilesystemLoader( $this->template_dir );
		$twig = new Twig\Environment( $loader, [
			'debug' => true,
		] );

		try {
			echo $twig->render( $template . '.html', [
				'ver' => 2,
			] );

		} catch ( Twig\Error\Error $e ) {
			printf( 'An error occurred when loading the template %s: %s on line %d',
				str_replace( $this->template_dir . '/', '', $e->getFile() ),
				$e->getMessage(),
				$e->getTemplateLine()
			);
		}
	}

}
