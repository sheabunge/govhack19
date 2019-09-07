<?php

namespace Shea\GovHack19;

class App {

	protected $page = '';

	protected $views;

	protected $base_dir;

	public function __construct( $app_path ) {
		$this->base_dir = $app_path;
		$this->views = new Views( $this->base_dir );
	}

	public function start() {
		$this->views->load();
	}
}
