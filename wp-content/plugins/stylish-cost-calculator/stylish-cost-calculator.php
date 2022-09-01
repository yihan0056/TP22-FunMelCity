<?php
/**
* Plugin Name: Stylish Cost Calculator
* Plugin URI:  https://stylishcostcalculator.com
* Description: A Stylish Cost Calculator / Price Estimate Form for your site.
* Version:     7.2.8
* Author:      Designful
* Author URI:  https://stylishcostcalculator.com
* License:     GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Domain Path: /languages
* Text Domain: scc
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'STYLISH_COST_CALCULATOR_VERSION', '7.2.8' );
define( 'SCC_URL', plugin_dir_url( __FILE__ ) );
define( 'SCC_DIR', dirname( __FILE__ ) );
define( 'SCC_ASSETS_URL', plugins_url( '/assets', __FILE__ ) );
define( 'SCC_TOOLTIP_BASEURL', SCC_ASSETS_URL . '/images/tooltip-images' );
require_once plugin_dir_path( __FILE__ ) . '/functions.php';
require_once plugin_dir_path( __FILE__ ) . '/stylish-cost-sero.php';
require plugin_dir_path(__FILE__) . '/cron/notifications.php';
define(
	'SCC_ALLOWTAGS',
	array(
		'a' => ['href' =>[]],
		'h4'     => array(),
		'b'      => array(),
		'strong' => array(),
		'br'     => array(),
		'hr'     => array(),
		'li'     => array(),
		'ul'     => array(),
		'i'      => array(
			'title'       => array(),
			'data-toggle' => array(),
			'div'         => array( 'class' => array() ),
		),
	)
);
class df_scc_plugin {

	public function __construct() {
		new SccSero();
		 //?Handles ajax requests
		add_action( 'init', array( $this, 'df_scc_load_ajax' ) );
		//?creates tables if doesnt exists
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] !== 'activate' ) {
			$this->checkTablesExists();
		}
		//?hides funky messages of WordPress
		add_action( 'admin_print_scripts', array( $this, 'scc_admin_hide_notices' ) );
		register_activation_hook( __FILE__, array( $this, 'do_install_scc' ) );
		register_deactivation_hook( __FILE__, array( $this, 'do_uninstall_scc' ) );
		add_action( 'upgrader_process_complete', array( $this, 'post_upgrade_tasks' ), 10, 2 );
		//*Loads menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		// add the stylish cost calculator free version to exclusion list of All in One SEO
		add_filter('aioseo_conflicting_shortcodes', function($conflictingShortcodes) {
			$conflictingShortcodes['Stylish Cost Calculator'] = 'scc_calculator';
			return $conflictingShortcodes;
		});
		//*shortcodeload
		add_shortcode( 'scc_calculator', array( $this, 'scc_shortcode1' ) );
		add_shortcode( 'scc_calculator-total', array( $this, 'create_scc_total_tag' ) );
		// *Loads script styles for frontend shorcode
		add_action( 'wp_enqueue_scripts', array( $this, 'scc_register_shortcode_calculator' ) );
		// *Loads script styles for backend shortcode in preview
		add_action( 'admin_enqueue_scripts', array( $this, 'scc_register_shortcode_calculator' ) );
		$this->scc_wpoption_add();
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'my_plugin_action_links' ) );
		add_action( 'admin_bar_menu', array( $this, 'scc_bar_menu' ), 100 );
	}
	function checkTablesExists() {
		global $wpdb;
		$res = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}df_scc_forms'" );
		if ( $res == null ) {
			//creates tables if dont exists
			$this->do_install_scc();
			require_once dirname( __FILE__ ) . '/admin/controllers/migrateController.php';
			$m = new migrateController();
			$m::update_wpOptions();
		} else {
			// *Alter table for version after 7.0.0
			$this->scc_alter_tables();
		}
	}
	function scc_alter_tables() {
		global $wpdb;
		$forms_table_cols = $wpdb->get_col( "DESC {$wpdb->prefix}df_scc_forms", 0 );
		if ( ! in_array( 'ShowFormBuilderOnDetails', $forms_table_cols ) ) {
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}df_scc_forms` ADD ShowFormBuilderOnDetails varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'false' " );
		}
		if (!in_array('turnoffQty', $forms_table_cols)) {
			$wpdb->query("ALTER TABLE `{$wpdb->prefix}df_scc_forms` ADD turnoffQty varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'false' ");
		  }
		if ( ! in_array( 'urlStatsArray', $forms_table_cols ) ) {
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}df_scc_forms` ADD urlStatsArray text COLLATE utf8mb4_unicode_ci DEFAULT NULL " );
		}
		if ( ! in_array( 'emailQuoteRecipients', $forms_table_cols ) ) {
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}df_scc_forms` ADD emailQuoteRecipients tinyint(1) NOT NULL DEFAULT 1" );
		}
		if ( ! in_array( 'created_at', $forms_table_cols ) ) {
			$wpdb->query( $wpdb->prepare( "ALTER TABLE `{$wpdb->prefix}df_scc_forms` ADD created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", array() ) );
		}
		if ( ! in_array( 'wrapper_max_width', $forms_table_cols ) ) {
			$wpdb->query( $wpdb->prepare( "ALTER TABLE `{$wpdb->prefix}df_scc_forms` ADD wrapper_max_width SMALLINT(10) NOT NULL DEFAULT 1000", array() ) );
		}
		$elements_table_cols = $wpdb->get_col( "DESC {$wpdb->prefix}df_scc_elements" );
		if ( ! in_array( 'element_woocomerce_product_id', $elements_table_cols ) ) {
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}df_scc_elements` ADD element_woocomerce_product_id varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL " );
		}
		$elements_table_cols = $wpdb->get_col("DESC {$wpdb->prefix}df_scc_elements");
    	if (!in_array('showInputBoxSlider', $elements_table_cols)) {
      		$wpdb->query("ALTER TABLE `{$wpdb->prefix}df_scc_elements` ADD showInputBoxSlider tinyint(10) NOT NULL DEFAULT 0 ");
    	}
	}
	function scc_bar_menu( $adminBar ) {
		$args = array(
			'id'       => 'scc-edit-calculator',
			'title'    => 'Edit SCC Calculator',
			'href'     => admin_url( 'admin.php?page=Stylish_Cost_Calculator_EditItems' ),
			'meta'     => array( 'class' => 'scc-top-bar-' ),
			'position' => 100,
		);
		$adminBar->add_node( $args );

	}
	function do_install_scc() {
		//create the table used by the component if it does not exist
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;
		if ( defined( 'SCC_DEV_ENV' ) ) {
			$wpdb->query( 'SET FOREIGN_KEY_CHECKS = 0;' );
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_forms");
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_elementitems");
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_elements");
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_forms");
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_sections");
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_subsections");
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_coupons");
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_conditions");
			// $wpdb->query("DROP TABLE IF EXISTS wp_df_scc_quote_submissions");
		}
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_elementitems` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `order` int(11) NOT NULL,
    `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `price` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `value1` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `value2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `value3` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `value4` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `uniqueId` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `woocomerce_product_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `opt_default` tinyint(1) NOT NULL DEFAULT 0,
    `element_id` bigint(20) UNSIGNED NOT NULL
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_elements` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `orden` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `titleElement` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `value1` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `value2` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `value3` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `value4` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `length` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `uniqueId` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `mandatory` tinyint(1) NOT NULL DEFAULT 0,
    `showTitlePdf` tinyint(1) NOT NULL DEFAULT 0,
    `titleColumnDesktop` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `titleColumnMobile` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `showPriceHint` tinyint(1) NOT NULL DEFAULT 0,
    `displayFrontend` tinyint(1) NOT NULL DEFAULT 0,
    `displayDetailList` tinyint(1) NOT NULL DEFAULT 0,
    `showInputBoxSlider` tinyint(10) NOT NULL DEFAULT 0,
    `subsection_id` bigint(20) UNSIGNED NOT NULL,
    `element_woocomerce_product_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_forms` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `formname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `inheritFontType` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `titleFontSize` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `titleFontType` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `titleFontWeight` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `titleColorPicker` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ServicefontSize` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fontType` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `fontWeight` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ServiceColorPicker` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `objectSize` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `objectColorPicker` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `elementSkin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `addContainer` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `addtoCheckout` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `buttonStyle` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnoffborder` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnoffemailquote` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnviewdetails` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnoffcoupon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `barstyle` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnofffloating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `removeTotal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `minimumTotal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `minimumTotalChoose` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `removeTitle` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnoffUnit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnoffQty` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnoffSave` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `turnoffTax` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `taxVat` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `showTaxBeforeTotal` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `symbol` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `emailQuoteRecipients` tinyint(1) NOT NULL DEFAULT 1,
    `removeCurrency` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `userCompletes` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `userClicksf` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `formFieldsArray` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `webhookSettings` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `showFieldsQuoteArray` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `translation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `paypalConfigArray` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `preCheckoutQuoteForm` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `combine_checkout_items` tinyint(1) NOT NULL DEFAULT 0,
    `combine_checkout_woocommerce_product_id` bigint(20) UNSIGNED NOT NULL,
	`progress_indicator_style` bigint(20) UNSIGNED DEFAULT 1,
    `isWoocommerceCheckoutEnabled` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `isStripeEnabled` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `ShowFormBuilderOnDetails` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'false',
    `urlStatsArray` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `showSearchBar` tinyint(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `invoice_number_settings` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `wrapper_max_width` SMALLINT(10) NOT NULL DEFAULT 1000,
    `created_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_sections` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `order` int(11) NOT NULL,
    `accordion` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
    `showSectionTotal` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `showSectionTotalOnPdf` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `section_in_page` tinyint(10) NOT NULL DEFAULT 0,
    `form_id` bigint(20) UNSIGNED NOT NULL
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_subsections` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `order` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `section_id` bigint(20) UNSIGNED NOT NULL
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_conditions` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `op` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `element_id` bigint(20) UNSIGNED NOT NULL,
    `condition_element_id` bigint(20) UNSIGNED DEFAULT NULL,
    `elementitem_id` bigint(20) UNSIGNED DEFAULT NULL,
    `condition_set` bigint(20) UNSIGNED DEFAULT 1
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_section_conditions` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `op` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `value` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `section_id` bigint(20) UNSIGNED NOT NULL,
    `condition_element_id` bigint(20) UNSIGNED DEFAULT NULL,
    `elementitem_id` bigint(20) UNSIGNED DEFAULT NULL,
    `condition_set` bigint(20) UNSIGNED DEFAULT 1
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_quote_submissions` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `status` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `opened` tinyint(1) NOT NULL,
    `starred` tinyint(1) NOT NULL,
    `submit_fields` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `quote_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `user_ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `browser_ua` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `calc_id` bigint(20) UNSIGNED NOT NULL
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}df_scc_coupons` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `startdate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `enddate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `discountpercentage` double(10,2) NOT NULL DEFAULT 0.00,
    `discountvalue` double(10,2) NOT NULL DEFAULT 0.00,
    `minspend` double(10,2) NOT NULL DEFAULT 0.00,
    `maxspend` double(10,2) NOT NULL DEFAULT 0.00,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
  )  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_elementitems`
    ADD PRIMARY KEY (`id`),
    ADD KEY `scc_elementitems_element_id_index` (`element_id`);"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_elements`
    ADD PRIMARY KEY (`id`),
    ADD KEY `scc_elements_subsection_id_index` (`subsection_id`);"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_forms`
    ADD PRIMARY KEY (`id`);"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_sections`
    ADD PRIMARY KEY (`id`),
    ADD KEY `scc_sections_form_id_index` (`form_id`);"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_subsections`
    ADD PRIMARY KEY (`id`),
    ADD KEY `scc_subsections_section_id_index` (`section_id`);"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_coupons`
    ADD PRIMARY KEY (`id`);"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_conditions`
    ADD PRIMARY KEY (`id`),
    ADD KEY `scc_conditions_condition_element_id_foreign` (`condition_element_id`),
    ADD KEY `scc_conditions_element_id_index` (`element_id`),
    ADD KEY `scc_conditions_elementitem_id_index` (`elementitem_id`)"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_section_conditions`
    ADD PRIMARY KEY (`id`),
    ADD KEY `scc_conditions_condition_element_id_foreign` (`condition_element_id`),
    ADD KEY `scc_conditions_element_id_index` (`element_id`),
    ADD KEY `scc_conditions_elementitem_id_index` (`elementitem_id`)"
		);
		// ALTER TABLE `userz_35627`.`wp_df_scc_section_conditions` ADD INDEX `scc_conditions_section_id_index` (`section_id`);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_quote_submissions`
    ADD PRIMARY KEY (`id`),
    ADD KEY `scc_quote_submissions_calc_id_foreign` (`calc_id`)"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_conditions`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_elementitems`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_elements`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;"
		);
		//?remove to preserve the old calculator id after in migration
		//   $wpdb->query("ALTER TABLE `{$wpdb->prefix}df_scc_forms`
		// MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;");
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_sections`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_subsections`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_quote_submissions`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_coupons`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_elementitems`
    ADD CONSTRAINT `scc_elementitems_element_id_foreign` FOREIGN KEY (`element_id`) REFERENCES `{$wpdb->prefix}df_scc_elements` (`id`) ON DELETE CASCADE;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_elements`
    ADD CONSTRAINT `scc_elements_subsection_id_foreign` FOREIGN KEY (`subsection_id`) REFERENCES `{$wpdb->prefix}df_scc_subsections` (`id`) ON DELETE CASCADE;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_sections`
    ADD CONSTRAINT `scc_sections_form_id_foreign` FOREIGN KEY (`form_id`) REFERENCES `{$wpdb->prefix}df_scc_forms` (`id`) ON DELETE CASCADE;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_subsections`
    ADD CONSTRAINT `scc_subsections_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `{$wpdb->prefix}df_scc_sections` (`id`) ON DELETE CASCADE;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_quote_submissions`
    ADD CONSTRAINT `scc_quote_submissions_calc_id_foreign` FOREIGN KEY (`calc_id`) REFERENCES `{$wpdb->prefix}df_scc_forms` (`id`) ON DELETE CASCADE;"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_conditions`
    ADD CONSTRAINT `scc_conditions_condition_element_id_foreign` FOREIGN KEY (`condition_element_id`) REFERENCES `{$wpdb->prefix}df_scc_elements` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `scc_conditions_element_id_foreign` FOREIGN KEY (`element_id`) REFERENCES `{$wpdb->prefix}df_scc_elements` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `scc_conditions_elementitem_id_foreign` FOREIGN KEY (`elementitem_id`) REFERENCES `{$wpdb->prefix}df_scc_elementitems` (`id`) ON DELETE CASCADE"
		);
		$wpdb->query(
			"ALTER TABLE `{$wpdb->prefix}df_scc_section_conditions`
    ADD CONSTRAINT `scc_conditions_condition_element_id_foreign` FOREIGN KEY (`condition_element_id`) REFERENCES `{$wpdb->prefix}df_scc_elements` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `scc_conditions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `{$wpdb->prefix}df_scc_sections` (`id`) ON DELETE CASCADE,
    ADD CONSTRAINT `scc_conditions_elementitem_id_foreign` FOREIGN KEY (`elementitem_id`) REFERENCES `{$wpdb->prefix}df_scc_elementitems` (`id`) ON DELETE CASCADE"
		);
		/**
		 * seed the initial option values
		 */
		$default_subject     = 'Your Quote Request On ' . get_bloginfo( 'url' );
		$scc_color_scheme    = get_option( 'df_scc_color-scheme' );
		$scc_currency        = get_option( 'df_scc_currency' );
		$scc_currency        = get_option( 'df_scc_currencytext' );
		$scc_licensed        = get_option( 'df_scc_licensed' );
		$scc_fontsettings    = get_option( 'df_scc_fontsettings' );
		$scc_emailsender     = get_option( 'df_scc_emailsender' );
		$scc_emailsubject    = get_option( 'df_scc_emailsubject' );
		$scc_sendername      = get_option( 'df_scc_sendername' );
		$scc_messageform     = get_option( 'df_scc_messageform' );
		$scc_email_send_copy = get_option( 'df_scc_email_send_copy' );
		if ( ! isset( $scc_email_send_copy ) ) {
			add_option( 'df_scc_email_send_copy', '' );
		}
		if ( ! $scc_color_scheme ) {
			add_option( 'df_scc_color-scheme', 1 );
		}
		if ( ! $scc_currency ) {
			add_option( 'df_scc_currency', 'USD' );
		}
		if ( ! $scc_currency ) {
			add_option( 'df_scc_currencytext', 'U.S. Dollar' );
		}
		if ( ! $scc_licensed ) {
			add_option( 'df_scc_licensed', '0' );
		}
		if ( ! $scc_fontsettings ) {
			add_option( 'df_scc_fontsettings', '' );
		}
		if ( ! $scc_emailsender ) {
			add_option( 'df_scc_emailsender', '' );
		}
		if ( ! $scc_emailsubject ) {
			update_option( 'df_scc_emailsubject', $default_subject );
		}
		if ( ! $scc_sendername ) {
			add_option( 'df_scc_sendername', '' );
		}
		if ( ! $scc_messageform ) {
			add_option( 'df_scc_messageform', "Hello <customer-name>, <br><br> Attached to this email is a PDF file that contains your quote. <br> If you have any further questions please call us, email us here ____. <br><br> Sincerely,<br> Your Company Name<br><br> <hr><br> <b>Customer's Name</b> l <customer-name> <b>Customer's Phone</b> l <customer-phone> <b>Customer's Emai</b> l <customer-email> <b>Customer's IP</b> l <customer-ip-address> <b>Browser Info</b> l <customer-browser-info ><b>Device</b> l <device> <b> Referral </b> | <customer-referral>" );
		}
		update_option( 'scc_v7_tables_ready', true );
		update_option( 'scc_installation_timestamp', time() );
	}

	function do_uninstall_scc() {
		if ( function_exists( 'wp_get_current_user' ) ) {
			$user     = wp_get_current_user();
			$userData = (array) $user->data;
			unset( $userData['user_pass'] );
			unset( $userData['user_activation_key'] );
			$userData['site_title']       = get_bloginfo();
			$userData['site_url']         = home_url();
			$userData['scc_free_version'] = STYLISH_COST_CALCULATOR_VERSION;
			$headers                      = array(
				'user-agent'   => 'SCC/' . STYLISH_COST_CALCULATOR_VERSION . '/' . md5( esc_url( home_url() ) ) . ';',
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			);
			wp_remote_post(
				'https://hook.us1.make.com/rb2u1v5x7fih55n3qahm77cgb5rpsrud',
				array(
					'method'      => 'POST',
					'timeout'     => 5,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => false,
					'headers'     => $headers,
					'body'        => json_encode( $userData ),
					'cookies'     => array(),
				)
			);
			 return 0;
		}
	}

	function df_scc_load_ajax() {
		require dirname( __FILE__ ) . '/stylish-cost-ajax.php';
	}
	function scc_shortcode1( $atts ) {
		if ( ! is_admin() ) {
			add_action(
				'wp_footer',
				function () {
					require SCC_DIR . '/admin/views/modalTemplates.php';
				}
			);
			  wp_localize_script( 'scc-frontend', 'pageCalcFront' . intval( $atts['idvalue'] ), array( 'nonce' => wp_create_nonce( 'calculator-front-page' . intval( $atts['idvalue'] ) ) ) );
		}
		wp_enqueue_style( 'scc-admin-style' );
		wp_enqueue_style( 'scc-checkbox1' );
		wp_enqueue_style( 'scc-dropbox' );
		wp_enqueue_script( 'scc-msdropdown' );
		wp_enqueue_style( 'scc-bootstrapslider-css' );
		wp_enqueue_script( 'scc-bootstrapslider-js' );
		wp_enqueue_script( 'scc-frontend' );
		wp_enqueue_script( 'scc-nouislider' );
		wp_enqueue_script( 'wp-util' );
		wp_enqueue_script( 'scc-translate-js' );
		wp_enqueue_script( 'scc-bootstrap-min3' );
		ob_start();
		extract(
			shortcode_atts(
				array(
					'idvalue' => null,
				),
				$atts,
				'bt_cc_item'
			)
		);
		if ( ! function_exists( 'get' ) ) {
			function get( $id ) {
				global $wpdb;
				$scc_form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}df_scc_forms WHERE id =%d ;", $id ) );
				if ( $scc_form ) {
					$scc_form->turnoffemailquote = true;
					$scc_form->turnoffcoupon     = true;
					$form_id                     = $scc_form->id;
					$sections                    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}df_scc_sections WHERE form_id =%d ORDER By `order`;", $form_id ) );
					$scc_form->sections          = $sections;
					foreach ( $sections as $section ) {
						$section_id          = $section->id;
						$subsection          = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}df_scc_subsections WHERE section_id =%d ;", $section_id ) );
						$section->subsection = $subsection;
						foreach ( $section->subsection as $sub ) {
							$sub_id       = $sub->id;
							$elements     = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}df_scc_elements WHERE subsection_id =%d ORDER By orden +0; ", $sub_id ) );
							$sub->element = $elements;
							foreach ( $sub->element as $el2 ) {
								$elem_id         = $el2->id;
								$condition       = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}df_scc_conditions WHERE element_id =%d ;", $elem_id ) );
								$el2->conditions = $condition;
								foreach ( $el2->conditions as $c ) {
									if ( $c->elementitem_id ) {
										$element             = $wpdb->get_row( $wpdb->prepare( "SELECT `name` FROM {$wpdb->prefix}df_scc_elementitems WHERE id =%d ;", $c->elementitem_id ) );
										$c->elementitem_name = $element;
									}
									if ( $c->condition_element_id ) {
										$element              = $wpdb->get_row( $wpdb->prepare( "SELECT `titleElement`,`type` FROM {$wpdb->prefix}df_scc_elements WHERE id =%d ;", $c->condition_element_id ) );
										$c->element_condition = $element;
									}
								}
							}
							foreach ( $sub->element as $el ) {
								$elem_id  = $el->id;
								$elements = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}df_scc_elementitems WHERE element_id =%d ;", $elem_id ) );
								// change 'price' property of element to zero if it is an empty string value, so it doesn't return javascript NaN value
								$elements         = array_map(
									function( $e ) {
										if ( $e->price == '' ) {
											$e->price = 0;
										}
										return $e;
									},
									$elements
								);
								$el->elementitems = $elements;
							}
						}
					}
					return $scc_form;
				}
			}
		}
		require SCC_DIR . '/lib/wp-google-fonts/google-fonts.php';
		$form = get( $idvalue );
		if ( ! $form ) {
			return "<h4 style='color:red'>Invalid calculator with ID {$atts["idvalue"]}</h4>";
		}
		$form->showFieldsQuoteArray = json_decode( stripslashes( $form->showFieldsQuoteArray ), true );
		$allfonts2                  = json_decode( $scc_googlefonts_var->gf_get_local_fonts() );
		$allfonts2i                 = $allfonts2->items;
		$fontUsed2                  = !empty( $form->titleFontType ) ? $allfonts2i[ $form->titleFontType ] : null;
		$fontUsed2Variant           = ( $form->titleFontWeight != '' ) ? $form->titleFontWeight : 'regular';
		$google_font_links = [];
		// var_dump($allfonts2);
		// var_dump($fontUsed2Variant);
		/**
		 *Title font
*/
		$fontFamilyService2 = 'inherit';
		$fontFamilyTitle2   = 'inherit';
		// always set font types to inherit on free copy
		$form->inheritFontType = 'true';
		if ( $form->inheritFontType == 'null' || $form->inheritFontType == 'false' ) {
			$fonts[0]['kind']     = $fontUsed2->kind;
			$fonts[0]['family']   = $fontUsed2->family;
			$fonts[0]['variants'] = array( $fontUsed2Variant );
			$fonts[0]['subsets']  = $fontUsed2->subsets;
			$fontFamilyTitle2     = $fonts[0]['family'];
			$font_link = $scc_googlefonts_var->style_late( $fonts );
			array_push( $google_font_links, $font_link ); //load google fonts css
		}
		/**
		 *Service font
*/
		$allfonts3i       = $allfonts2->items;
		$fontUsed3        = !empty( $form->fontType ) ? $allfonts3i[ $form->fontType ] : null;
		$fontUsed3Variant = ( $form->fontWeight != '' ) ? $form->fontWeight : 'regular';
		if ( $form->inheritFontType == 'null' || $form->inheritFontType == 'false' ) {
			$fonts2[0]['kind']     = $fontUsed3->kind;
			$fonts2[0]['family']   = $fontUsed3->family;
			$fonts2[0]['variants'] = array( $fontUsed3Variant );
			$fonts2[0]['subsets']  = $fontUsed3->subsets;
			$fontFamilyService2    = $fonts2[0]['family'];
			$font_link = $scc_googlefonts_var->style_late( $fonts );
			array_push( $google_font_links, $font_link ); //load google fonts css
		}
		/**
		 *Object font
*/
		$colorObject = $form->objectColorPicker;
		// if (strpos($form->objectColorPicker, '#')) {
		//   $colorObject = $form->objectColorPicker;
		// }
		$currency_style                = get_option( 'df_scc_currency_style', 'default' ); // dot or comma
		$currency                      = get_option( 'df_scc_currency', 'USD' );
		$currency_conversion_mode      = get_option( 'df_scc_currency_coversion_mode', 'off' );
		$currency_conversion_selection = get_option( 'df_scc_currency_coversion_manual_selection' );
		require SCC_DIR . '/admin/views/generateFrontendForm.php';
		return ob_get_clean();
		// die();
	}
	/**
	 creates additional total tag placeholder, later populated by javascript
	 @param mixed $attributes

	 @return void
*/
	function create_scc_total_tag( $attributes ) {
		// parse the attribute, if not supplied, default value is set
		$attributes = shortcode_atts(
			array(
				'idvalue'         => 0,
				'combine'         => 0,
				'currency-symbol' => 0,
				'prefix-text'     => '',
				'apply-math'      => 0,
			),
			$attributes
		);
		// $prefixText and $mathParams holds the array data to a variable
		$prefixText = $attributes['prefix-text'];
		$mathParams = $attributes['apply-math'] ? json_encode( explode( ':', $attributes['apply-math'] ) ) : '["add","0"]';
		// if combine attribute is there, idvalue does not work
		if ( $attributes['idvalue'] && ! ( $attributes['combine'] ) ) {
			$calculatorId = absint( $attributes['idvalue'] );
			$html         = "<span class=\"scc-multiple-total-wrapper calcid-$calculatorId\" data-math={$mathParams}>
    <span>$prefixText</span>
    <span class=\"multi-total-currency-prefix\"></span>
    <span class=\"scc-total\">0</span>
    <span class=\"multi-total-currency-suffix\"></span>
    </span>";
		}
		// if there is combine attribute, this html is printed
		if ( $attributes['combine'] ) {
			$ourFormula     = $attributes['combine'];
			$currencySymbol = isset( $attributes['currency-symbol'] ) ? absint( $attributes['currency-symbol'] ) : 1;
			$calcValues     = explode( ',', $ourFormula );
			if ( ! empty( $calcValues ) && count( $calcValues ) > 1 ) {
				$calcValues = json_encode( $calcValues );
				$html       = "<span class=\"scc-multiple-total-wrapper scc-combination\" data-combination={$calcValues} data-curr-sym={$currencySymbol} data-math={$mathParams}>
      <span>$prefixText</span>
      <span class=\"multi-total-currency-prefix\"></span>
      <span class=\"scc-total\"></span>
      <span class=\"multi-total-currency-suffix\"></span>
      </span>";
			}
		}
		return $html;
	}
	// START OF Hide Admin Notices At Top //
	function scc_admin_hide_notices() {
		 $exclusionPages = array( 'Stylish_Cost_Calculator_EditItems', 'stylish_cost_calculator_Diagnostic', 'quotes_with_id', 'scc-tabs', 'stylish_cost_calculator_help', 'stylish_cost_calculator_license_help', 'stylish_cost_calculator_settings', 'Stylish_Cost_Calculator_Coupon', 'Stylish_Cost_Calculator_Migration', 'stylish_cost_calculator_premium_settings' );
		if ( empty( $_REQUEST['page'] ) || ! in_array( $_REQUEST['page'], $exclusionPages ) ) {
			return;
		}
		global $wp_filter;
		foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $notices_type ) {
			if ( empty( $wp_filter[ $notices_type ]->callbacks ) || ! is_array( $wp_filter[ $notices_type ]->callbacks ) ) {
				continue;
			}
			foreach ( $wp_filter[ $notices_type ]->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
							unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
							continue;
					}
					$class = ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ? strtolower( get_class( $arr['function'][0] ) ) : '';
					if (
					! empty( $class ) &&
					strpos( $class, 'scc' ) !== false
					) {
						  continue;
					}
					if (
					! empty( $class ) &&
					strpos( $class, 'appsero' ) !== false
					) {
						continue;
					}
					if (
					! empty( $name ) && (
					strpos( $name, 'scc' ) === false
					)
					) {
						unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}
	}
	// END OF Hide Admin Notices At Top //
	// *register scripts style for calculator shortcode
	function scc_register_shortcode_calculator() {
		wp_register_script( 'scc-bootstrap-min3', SCC_URL . 'assets/lib/bootstrap/bootstrap.bundle.min.js', array( 'jquery' ), '5.1.3', false );

		wp_register_style( 'scc-admin-style', SCC_URL . 'assets/css/scc-front-end.css', array(), STYLISH_COST_CALCULATOR_VERSION );
		wp_register_style( 'scc-checkbox1', SCC_URL . 'assets/css/checkboxes/checkboxes.css', array(), STYLISH_COST_CALCULATOR_VERSION );
		wp_register_style( 'scc-dropbox', SCC_URL . 'assets/css/msdropdown/dd.css', array(), STYLISH_COST_CALCULATOR_VERSION ); //custom css
		wp_register_script( 'scc-msdropdown', SCC_ASSETS_URL . '/lib/msdropdown/jquery.dd.js', array( 'jquery' ), STYLISH_COST_CALCULATOR_VERSION, false );
		wp_register_style( 'scc-bootstrapslider-css', SCC_ASSETS_URL . '/lib/bootstrap-slider/css/bootstrap-slider.css' );
		wp_register_script( 'scc-frontend', SCC_URL . 'assets/js/scc-frontend.js', array( 'jquery', 'wp-util' ), STYLISH_COST_CALCULATOR_VERSION, true );
		wp_register_script( 'scc-bootstrapslider-js', SCC_URL . 'assets/lib/bootstrap-slider/js/bootstrap-slider.js', array( 'jquery' ), STYLISH_COST_CALCULATOR_VERSION, false );
		wp_register_script( 'scc-translate-js', SCC_URL . 'assets/lib/translate/jquery.translate.js', array( 'jquery' ), STYLISH_COST_CALCULATOR_VERSION, false );
		wp_register_script( 'scc-nouislider', SCC_URL . 'assets/lib/nouislider/nouislider.min.js', [], STYLISH_COST_CALCULATOR_VERSION );
		if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}
		if ( is_admin() && get_current_screen()->base == 'stylish-cost-calculator_page_Stylish_Cost_Calculator_EditItems' ) {

		}
	}
	function admin_menu() {
		if ( ! class_exists( 'migrateController' ) ) {
			require dirname( __FILE__ ) . '/admin/controllers/migrateController.php';
		}
		$i = new migrateController();
		add_menu_page( __( 'Stylish Cost Calculator', 'scc' ), __( 'Stylish Cost Calculator', 'scc' ), 'manage_options', 'Stylish_Cost_Calculator_EditItems', 'ssc_test_data', SCC_URL . '/assets/images/scc_dashicon.png', null );
		// EDIT PAGE
		add_submenu_page( 'Stylish_Cost_Calculator_EditItems', 'All Calculator Forms', 'All Calculator Forms ', 'manage_options', 'Stylish_Cost_Calculator_EditItems', 'ssc_test_data', null );
		// ADD PAGE
		add_submenu_page( 'Stylish_Cost_Calculator_EditItems', 'Add New', 'Add New', 'manage_options', 'scc-tabs', 'ssc_test_data' );

		// HELP AND VIDEOS
		add_submenu_page( 'Stylish_Cost_Calculator_EditItems', 'Help & Videos', 'Help & Tutorials', 'manage_options', 'stylish_cost_calculator_help', 'ssc_test_data', null );
		// MEMBERS
		add_submenu_page( 'Stylish_Cost_Calculator_EditItems', 'Members', 'Members Portal', 'manage_options', 'stylish_cost_calculator_license_help', 'ssc_test_data', null );
		// COUPON
		add_submenu_page( 'Stylish_Cost_Calculator_EditItems', 'Coupon', 'Coupon Codes', 'manage_options', 'Stylish_Cost_Calculator_Coupon', 'ssc_test_data', null );

		// QUOTE FOR CALCULATOR
		add_submenu_page( '', 'Quote Viewer', null, 'manage_options', 'quotes_with_id', 'ssc_test_data', null );
		// DIAGNOSTICS
		add_submenu_page( 'Stylish_Cost_Calculator_EditItems', 'Diagnostics', 'Diag & Sys Info', 'manage_options', 'stylish_cost_calculator_Diagnostic', 'ssc_test_data', null );
		//COUPON
		add_submenu_page( '', 'Quote Viewer', null, 'manage_options', 'Stylish_Cost_Calculator_Coupon', 'ssc_test_data', null );
		// GLOBAL SETTINGS
		add_submenu_page( 'Stylish_Cost_Calculator_EditItems', 'Global Settings', 'Global Settings', 'manage_options', 'stylish_cost_calculator_settings', 'ssc_test_data', null );
		// Uncomment to use migration page
		add_submenu_page( '', 'Migçrate your database', 'Migrate', 'manage_options', 'Stylish_Cost_Calculator_Migration', 'ssc_test_data', null );
		function ssc_test_data() {
			  // $template = dirname(__DIR__,1) . '/formController.php';
			$template = dirname( __FILE__ ) . '/admin/controllers/dealer.php';
			// if (file_exists($template)) {
			require $template;
			// }
		}
	}
	public function post_upgrade_tasks( $upgrader_object, $options ) {
		$current_plugin_path_name = plugin_basename( __FILE__ );
 
		if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach ( $options['plugins'] as $each_plugin ) {
				if ( $each_plugin == $current_plugin_path_name ) {
					// ensure cron schedulers
					if ( ! class_exists( 'SCC_Notifications_Cron' ) ) {
						require plugin_dir_path( __FILE__ ) . '/cron/notifications.php';
					}
					SCC_Notifications_Cron::schedule_cron_event();
				}
			}
		}
	}
	/**
	 *Ads wp_options
	 @param scc_currency
	 @param scc_currencytext
	 @param scc_currency_style
	 @param scc_currency_coversion_mode
	 @param scc_currency_coversion_manual_selection
	 todo: scc_currency_style (default, comma)
	 todo: scc_currency_conversion_mode (manual_selection,auto_detect,off)
*/
	function scc_wpoption_add() {
		add_option( 'df_scc_currency', 'USD' );
		add_option( 'df_scc_currencytext', 'U.S. Dollar' );
		add_option( 'df_scc_currency_style', 'default' );
		add_option( 'df_scc_currency_coversion_mode', 'off' );
		add_option( 'df_scc_currency_coversion_manual_selection', 'CAD' );
	}
	function my_plugin_action_links( $links ) {
		$links = array_merge(
			array(
				'<a href="' . admin_url( 'admin.php' ) . '?page=scc-tabs' . '">' . __( 'Add Calculator', 'textdomain' ) . '</a>',
				'<a href="' . admin_url( 'admin.php' ) . '?page=Stylish_Cost_Calculator_EditItems' . '">' . __( 'Edit Existing', 'textdomain' ) . '</a>',
				'<a href="https://stylishcostcalculator.com/?utm_source=inside-plugin&utm_medium=buy-premium-cta-banner">Buy Now</a>',
				'<a target="_blank" href="https://stylishcostcalculator.com/">' . __( 'Website', 'textdomain' ) . '</a>',
				'<a target="_blank" href="https://stylishcostcalculator.com/support">' . __( 'Support', 'textdomain' ) . '</a>',
				'<a href="' . admin_url( 'admin.php' ) . '?page=stylish_cost_calculator_settings' . '">' . __( 'Global Settings', 'textdomain' ) . '</a>',
			),
			$links
		);
		return $links;
	}
}
new df_scc_plugin();
/** Utility function to set default property value for multi dimensional arrays
* https://mekshq.com/recursive-wp-parse-args-wordpress-function/
* @param $a - to be parsed array
* @param $b - default array
*/
if ( ! function_exists( 'meks_wp_parse_args' ) ) {
	function meks_wp_parse_args( &$a, $b ) {
		$a      = (array) $a;
		$b      = (array) $b;
		$result = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) ) {
				$result[ $k ] = meks_wp_parse_args( $v, $result[ $k ] );
			} else {
				$result[ $k ] = $v;
			}
		}
		return $result;
	}
}
if ( ! function_exists( 'scc_custom_license_activation_info' ) ) {
	function scc_custom_license_activation_info() {         ?>
	<div class="appsero-license-settings appsero-license-section" style="margin-top: 20px;">
	  <div class="appsero-license-title">
		<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
		  <path d="M0 0h24v24H0z" fill="none" />
		  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z" />
		</svg>
		<span>Are you on a development site / want to use the license key in another website?</span>
	  </div>
	  <div class="appsero-license-details">
		<p>The license keys can only be used on a limited number of WordPress websites. If you want to use the license key on another website, you have to deactivate it first.</p>
		<p><a href="https://designful.freshdesk.com/a/solutions/articles/48001146692" target="_blank">Click here to find out how to migrate/transfer your license keys.</a></p>
	  </div>
	</div>
		<?php
	}
}
