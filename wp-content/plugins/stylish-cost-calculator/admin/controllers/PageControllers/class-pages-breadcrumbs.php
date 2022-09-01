<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * * Main class for all pages, the page classes inherit this class
 * todo: here must be enqueue most of the js and css
 */


class PagesBreadcrumbs extends SccSero {

	public function __construct() {

		if ( is_admin() ) {
			wp_register_script( 'scc-bootstrap-min2', SCC_URL . 'assets/lib/bootstrap/bootstrap.bundle.min.js', array( 'jquery' ), '5.1.3', true );
			wp_register_style( 'scc-bootstrap-min2', SCC_URL . 'assets/lib/bootstrap/bootstrap.min.css', '5.1.3' );
			wp_register_style( 'gf-admin-style', SCC_URL . 'assets/css/scc-back-end.css', array(), STYLISH_COST_CALCULATOR_VERSION );
			wp_register_script( 'scc-sweet-alert', SCC_URL . 'assets/lib/sweetalert2/sweetalert2.min.js', array( 'jquery' ), STYLISH_COST_CALCULATOR_VERSION, true );
			wp_register_style( 'scc-sweet-alert', SCC_URL . 'assets/lib/sweetalert2/sweetalert2.min.css', array(), STYLISH_COST_CALCULATOR_VERSION );
			wp_enqueue_style( 'scc-fonts', 'https://fonts.googleapis.com/css2?family=Poppins&display=swap' );
			wp_enqueue_style( 'scc-material', 'https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined' );
			wp_enqueue_style( 'scc-sweet-alert' );
			wp_enqueue_script( 'scc-sweet-alert' );
			wp_enqueue_script( 'wp-util' );
			wp_enqueue_script( 'scc-bootstrap-min2' );
			wp_enqueue_style( 'scc-bootstrap-min2' );
			wp_enqueue_style( 'gf-admin-style' );
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'scc-sweetalert' );
			wp_enqueue_script( 'scc-sweetalert' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			// wp_enqueue_style('wp-jquery-ui-dialog');
			wp_enqueue_style( 'scc-jquery-ui-css', SCC_URL . 'assets/css/jquery-ui.css', array(), STYLISH_COST_CALCULATOR_VERSION );
			wp_register_script( 'scc-backend', SCC_URL . 'assets/js/scc-backend.js', array( 'jquery' ), STYLISH_COST_CALCULATOR_VERSION, true );
			wp_enqueue_script( 'scc-backend' );
		}
	}
}
