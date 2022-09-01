<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once dirname( __FILE__ ) . '/class-pages-breadcrumbs.php';

class PageNew extends PagesBreadcrumbs {


	public function __construct() {
		parent::__construct();
		require dirname( __DIR__, 2 ) . '/views/adminHeader.php';
		require dirname( __DIR__, 2 ) . '/views/addCalculator.php';
		require dirname( __DIR__, 2 ) . '/views/adminFooter.php';
	}

	// PASAR SI VERSION PAGA O NO

	public function isGranted() {
		// GRANTED SC
	}
}
new PageNew();
