<?php

class SccSero {

	private $str;
	private $a1;
	private $a2;

	function __construct() {
		$this->appsero_init_tracker_stylish_cost_calculator_premium();
	}

	function appsero_init_tracker_stylish_cost_calculator_premium() {
		if ( ! class_exists( 'Appsero\Client' ) ) {
			include_once __DIR__ . '/lib/appsero/Client.php';
		}
		$this->getOpt();
		$client = new Appsero\Client( $this->getA1(), $this->getA2(), SCC_DIR . '/stylish-cost-calculator.php' );
		// Active insights
		$client->insights()->init();
		// Active automatic updater
		$client->updater();
		// Active license page and checker
		global $appserver;
		$appserver = $client->license();
		// $appserver->add_settings_page( $this->get_args() );
		if ( $appserver->is_valid() ) {
			$this->setServerSts( $appserver->is_valid() );
			for ( $i = 0; $i < 4; $i++ ) {
				$this->setStr( $i );
				$o = ( $i < 3 ) ? '1' : 'scc_license_key';
				update_option( $this->str( $i ), $o );
			}
		} else {
			$this->setServerSts( false );
			for ( $i = 0; $i < 4; $i++ ) {
				$o = ( $i < 3 ) ? 0 : '';
				update_option( $this->str( $i ), $o );
			}
		}
	}

	function get_args() {
		return array(
			'type'        => 'submenu',
			'menu_title'  => 'License',
			'page_title'  => 'Stylish Cost Calculator Premium Settings',
			'menu_slug'   => 'stylish_cost_calculator_premium_settings',
			'parent_slug' => 'scc-tabs',
		);
	}

	function getOpt() {
		$this->setA1( 'acbd38f5-c224-4569-b20d-2fcd27054b1f' );
		$this->setA2( 'Stylish Cost Calculator' );
	}

	function str( int $i ) {
		switch ( $i ) {
			case 0:
				return 'df_appsero_license';
				break;
			case 1:
				return 'df_scc_licensed';
				break;
			case 2:
				return 'df_scclk_opt';
				break;
			case 3:
				return 'df_scc_license_key';
				break;
		}
	}

	/**
	 * Get the value of serverSts
	 */
	function getServerSts() {
		return $this->serverSts;
	}

	/**
	 * Set the value of serverSts
	 *
	 * @return  self
	 */
	function setServerSts( $serverSts ) {
		$this->serverSts = $serverSts;

		return $this;
	}

	/**
	 * Get the value of str
	 */
	function getStr() {
		return $this->str;
	}

	/**
	 * Set the value of str
	 *
	 * @return  self
	 */
	function setStr( $str ) {
		$this->str = $str;

		return $this;
	}

	/**
	 * Get the value of a1
	 */
	public function getA1() {
		return $this->a1;
	}

	/**
	 * Set the value of a1
	 *
	 * @return  self
	 */
	public function setA1( $a1 ) {
		$this->a1 = $a1;

		return $this;
	}

	/**
	 * Get the value of a2
	 */
	public function getA2() {
		return $this->a2;
	}

	/**
	 * Set the value of a2
	 *
	 * @return  self
	 */
	public function setA2( $a2 ) {
		$this->a2 = $a2;

		return $this;
	}
}
