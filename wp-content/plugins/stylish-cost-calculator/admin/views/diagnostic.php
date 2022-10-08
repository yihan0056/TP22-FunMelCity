<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Stylish_Cost_Calculator_Diagnostic {

	public static function init() {
	}
	public function __construct()
    {
        //  add_action( 'admin_init', array($this,'admin_init') );
        add_action('admin_menu', array($this, 'admin_menu'), 90);
        // add_action('wp_ajax_sccGetDebugItems', array($this, 'get_debug_items'));
        // add_action('wp_ajax_nopriv_sccGetDebugItems', array($this, 'get_debug_items'));
        global $pagenow;
        if ($pagenow == "admin.php" && "stylish_cost_calculator_Diagnostic" == $_GET['page']) {
            $httpArgs = array(
            'method'      => 'GET',
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'cookies'     => array()
            );
            $this->apiConnection = wp_remote_get("https://api.appsero.com", $httpArgs);
            $this->apiAppseroConnection = wp_remote_get("https://appsero.com", $httpArgs);
            $this->apiSccConnection = wp_remote_get("https://stylishcostcalculator.com", $httpArgs);
        };
    }

    function diagnostic_page($is_ajax = false)
    {
        if (!defined('ABSPATH')) {
            exit; // Exit if accessed directly
        }
?>
       <?php if ( ! $is_ajax ) : ?>
        <style>
            #border1 {
                border: 2px black;
                border-radius: 10px;
                background-color: #f8f9ff;
                padding: 15px;
                margin-left: 25px;
                margin-bottom: 15px;
                box-shadow: 0px 0px;
                width: 48%;
                max-width: 400px;
            }

            .green_dot {
                height: 16px;
                width: 16px;
                background-color: green;
                border-radius: 50%;
                display: inline-block;
                margin-left: 10px;
                margin-right: 10px;
            }

            .red_dot {
                height: 16px;
                width: 16px;
                background-color: red;
                border-radius: 50%;
                display: inline-block;
                margin-left: 10px;
                margin-right: 10px;
            }

            .yellow_dot {
                height: 16px;
                width: 16px;
                background-color: #dcdcdc;
                border-radius: 50%;
                display: inline-block;
                margin-left: 10px;
                margin-right: 10px;
            }
        </style>
        <?php endif; ?>
<?php
        global $wp_version;
        $results = [];
        $results["curl_available"] = in_array('curl', get_loaded_extensions());
        $test_char = "" . DB_CHARSET;
        $postMaxAmt = (int)(str_replace('M', '', ini_get('post_max_size')));
        $php_version = phpversion();
        ob_start();
        phpinfo(INFO_MODULES);
        $contents = ob_get_clean();
        $moduleAvailable = strpos($contents, 'mod_security') !== false;
        $plugin_conflicted = 0;
        $theme_conflicted = 0;
        $theme = wp_get_theme();
        $spl_license_return = get_option('act_ser_conn_refused');
        $license_key_activated = get_option('spl_license_return');
        $plugins_detected = "";
        $all_in_one_security_detected = "";
        $site_url = site_url();
        function url()
        {
            if (isset($_SERVER['HTTPS'])) {
                $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
            } else {
                $protocol = 'http';
            }
            return $protocol . "://" . $_SERVER['HTTP_HOST'];
        }
        ob_start();
        echo '<div class="row"><div class="scc_title_bar" >Diagnostics</div></div>';
        ?>
        <p style="padding-left:25px;font-weight:400px;font-size: 1em">Please action any items in red by emailing your admin or hosting company support. Dont worry about orange or yellow items.</p>
        <div class="debug-items-wrapper" style="max-width: 950px;background: white;padding: 20px;">
        <?php
        //CURRENT SITE URL AND ACTIVATED THEAM NAME
        echo '<div id="border1"><span style="font-size:1em;font-weight:bold;">Domain URL:</span> ';
        echo esc_html(url()) . '<br><span style="font-size:1em;font-weight:bold;">Active Theme: </span>' . esc_html(wp_get_theme()) . '<br>';
        echo "</div>";
        $plugin_data = get_plugin_data(SCC_DIR . '/stylish-cost-calculator.php');
        $scc_version = $plugin_data['Version'];
        // SCC Version
        if ($plugin_data['Version']) {
            echo '<div id="border1"><span class="green_dot"></span>  <span style="font-size:1em;font-weight:bold;">SCC Version:</span> ';
            echo esc_html($scc_version) . " <br></div>";
        }
        // Mysql version
        global $wpdb;
        $mysqlVersion = $wpdb->db_version();
        if ($mysqlVersion) {
            echo '<div id="border1"><span class="green_dot"></span>  <span style="font-size:1em;font-weight:bold;">MySQL Version:</span> ';
            echo esc_html($mysqlVersion) . " <br></div>";
        }
        // curl present check
        if (in_array('curl', get_loaded_extensions())) {
            $curl_present = "Yes";
        } else {
            $curl_present = "No";
        }
        if ($curl_present) {
            echo '<div id="border1"><span class="green_dot"></span>  <span style="font-size:1em;font-weight:bold;">CURL Library Present:</span>';
            echo esc_html($curl_present) . " <br></div>";
        }
        if (in_array('gd', get_loaded_extensions())) {
            echo '<div id="border1"><span class="green_dot"></span>  <span style="font-size:1em;font-weight:bold;">PHP GD extension: </span>available</div>';
        } else {
            echo '<div id="border1"><span class="yellow_dot"></span>  <span style="font-size:1em;font-weight:bold;">PHP GD extension: </span>missing</div>';
        }
        $memory_limit = WP_MAX_MEMORY_LIMIT;
        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if ($matches[2] == 'M') {
                $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } else if ($matches[2] == 'K') {
                $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }
        $results["ram_info"] = WP_MAX_MEMORY_LIMIT;
        $results["ram_sufficient"] = true;
        if ((536870912 <= $memory_limit)) {
            echo '<div id="border1"><span class="green_dot"></span>  <span style="font-size:1em;font-weight:bold;">WordPress memory limit:</span> ';
            echo esc_html(WP_MAX_MEMORY_LIMIT) . " <br></div>";
        } else {
            $results["ram_sufficient"] = false;
            echo '<div id="border1"><span class="red_dot"></span>  <span style="font-size:1em;font-weight:bold;">WordPress memory limit:</span> ';
            echo esc_html(WP_MAX_MEMORY_LIMIT) . " <br><br>";
            echo 'Increase PHP Memory Limit to 512M or higher for PHP with WordPress. To do this, edit php.ini and wp-config.php files, or ask your hosting comapny to do so. <br><br><a href="http://designful.freshdesk.com/en/support/solutions/articles/48001141896-wp-memory-limit-or-the-email-quote-gets-stuck-at-90-" target="_blank">Full tutorial here.</a><br></div>';
        }
        // PHP VERSION HIGHER THAN 5.0
        $results["php_version"] = $php_version;
        $results["php_supported_version"] = true;
        if (version_compare($php_version, '7.2', '>=')) {
            echo '<div id="border1"><span class="green_dot"></span>  <span style="font-size:1em;font-weight:bold;">PHP Version:</span> ';
            echo esc_html($php_version) . " <br></div>";
        } else {
            $results["php_supported_version"] = false;
            echo '<div id="border1"><span class="red_dot"></span>  <span style="font-size:1em;font-weight:bold;">PHP Version:</span> ';
            echo esc_html($php_version) . " <br><br>";
            echo "Change your PHP level in your cPanel, or ask your hosting comapny to do so. <br></div>";
        }
        // WP VERSION HIGHER THAN 5.4
        $results["wp_version"] = $wp_version;
        $results["wp_supported_version"] = true;
        if (version_compare($wp_version, '5.6', '>=')) {
            // WordPress version is greater than 4.3
            echo '<div id="border1"><span class="green_dot"></span> <span style="font-size:1em;font-weight:bold;">WordPress Version:</span> ';
            echo esc_html($wp_version) . "</div>";
        } else {
            $results["wp_supported_version"] = false;
            echo '<div id="border1"><span class="red_dot"></span> <span style="font-size:1em;font-weight:bold;">WordPress Version:</span> ';
            echo esc_html($wp_version) . "<br><br>  ";
            echo "Your WordPress core version is really outdated. Please backup, then upgrade.<br></div>";
        }
        // MOD SECURITY IS OFF
        if ($moduleAvailable == null) {
            $results["has_mod_security"] = false;
            echo '<div id="border1"><span class="green_dot"></span> <span style="font-size:1em;font-weight:bold;">MOD Security:</span> Off </div> ';
        } else {
            $results["has_mod_security"] = true;
            $results["mod_security_message"] = esc_html($moduleAvailable);
            echo '<div id="border1"><span class="red_dot"></span> <span style="font-size:1em;font-weight:bold;">MOD Security:</span> ';
            echo esc_html($moduleAvailable) . " <br> </div>";
        }
        // Check if aWP Forms SMTP plugin is not activated
        $results['is_smtp_plugin_active'] = false;
        if (is_plugin_active('wp-mail-smtp/wp_mail_smtp.php') == false && is_plugin_active('wp-mail-smtp-pro/wp_mail_smtp.php') == false) {
            $results['is_smtp_plugin_active'] = true;
            echo '<div id="border1"><span class="yellow_dot"></span> <span style="font-size:1em;font-weight:bold;">Email Delivery: </span>';
            echo "<br><br>Right now your site might setup to send emails via PHP, this is not a ideal and can cause your email quote PDF forms to be sent to your user's spam folder. We recommend to install the <a href='https://en-ca.wordpress.org/plugins/wp-mail-smtp/' target='_blank'f>WP Forms SMTP plugin</a>.<br><br><a href='https://designful.freshdesk.com/en/support/solutions/articles/48000945308-email-setup-troubleshoot-sending-quotes-by-email-smtp-server-' target='_blank'>Full tutorial here.</a>";
            echo "<br></div>";
        }
        // Check if any plugins are conflicted
        if (is_plugin_active('siteorigin-panels/siteorigin-panels.php')) {
            $results['has_plugin_siteorigin_builder'] = true;
            $plugin_conflicted = $plugin_conflicted + 1;
            $plugins_detected = "Page Builder by SiteOrigin";
        }
        // Check If beacer page builder Activated
        if (is_plugin_active('beaver-builder-lite-version/fl-builder.php')) {
            $results['has_plugin_beaver_builder'] = true;
            $plugin_conflicted = $plugin_conflicted + 1;
            $plugins_detected = "WordPress Page Builder � Beaver Builder";
        }
        // Check If wpFastestCache ( Mike )
        if (is_plugin_active('wp-fastest-cache/wpFastestCache.php')) {
            $results['has_plugin_wp_fastest_cache'] = true;
            echo '<div id="border1"><span class="yellow_dot"></span> <span style="font-size:1em;font-weight:bold;">Potential Conflict: </span>';
            echo "WP Fastest Cache<br><br>";
            echo 'Warning: You are using a cache plugin. You should read <a href="https://designful.freshdesk.com/support/solutions/articles/48000950262-important-information-regarding-cache-plugins" target="_blank"> this page.</a></br>';
            echo "</div>";
        }
        // Check If jQuery Migrate Helper Plugin is installed and activated
        if (is_plugin_active('enable-jquery-migrate-helper/enable-jquery-migrate-helper.php')) {
            $results['has_plugin_jqmigrate_helper'] = true;
            echo '<div id="border1"><span class="yellow_dot"></span> <span style="font-size:1em;font-weight:bold;">Potential Conflict: </span>';
            echo "jQuery Migrate Helper<br><br>";
            echo 'Warning: You are using jQuery Migrate Helper plugin. It may cause frontend javascript not work. Please, uninstall this plugin.</br>';
            echo "</div>";
        }
        // Check if Autoptimize ( Mike )
        if (is_plugin_active('autoptimize/autoptimize.php')) {
            $results['has_plugin_autoptimize'] = true;
            echo '<div id="border1"><span class="yellow_dot"></span> <span style="font-size:1em;font-weight:bold;">Potential Conflict: </span>';
            echo "Autoptimize<br><br>";
            echo 'Warning: You are using a cache plugin. You should read <a href="https://designful.freshdesk.com/support/solutions/articles/48000950262-important-information-regarding-cache-plugins" target="_blank"> this page.</a></br>';
            echo "</div>";
        }
        // Check if W3 Total Cache ( Mike )
        if (is_plugin_active('w3-total-cache/w3-total-cache.php')) {
            $results['has_plugin_w3total_cache'] = true;
            echo '<div id="border1"><span class="yellow_dot"></span> <span style="font-size:1em;font-weight:bold;">Potential Conflict: </span>';
            echo "W3 Total Cache<br><br>";
            echo 'Warning: You are using a cache plugin. You should read <a href="https://designful.freshdesk.com/support/solutions/articles/48000950262-important-information-regarding-cache-plugins" target="_blank"> this page.</a></br>';
            echo "</div>";
        }
        if ($plugin_conflicted == null) {
            echo '<div id="border1"><span class="green_dot"></span> <span style="font-size:1em;font-weight:bold;">Conflicted Plugins:</span> None </div>';
        } else {
                $results['has_conflicting_plugin'] = true;
                echo '<div id="border1"><span class="yellow_dot"></span> <span style="font-size:1em;font-weight:bold;">Conflicted Plugins:</span>';
                echo esc_html($plugin_conflicted) . "<br>";
                echo "Warning: You are using a page builder. Please make sure you are using the correct container size and widget to add our shortcode to.";
                echo "</div>";
        }
        if (!is_array($this->apiConnection)) {
            echo '<div id="border1"><span class="red_dot"></span> <span style="font-size:1em;font-weight:bold;">Pingback to License Server:</span> ';
            echo "Cannot ping our activation server. Please, ask your hosting company to add our IP to the whitelist. IP = 18.213.20.182";
            echo '</div>';
            if (!is_array($this->apiAppseroConnection)) {
                echo '<div id="border1"><span class="red_dot"></span> <span style="font-size:1em;font-weight:bold;">Pingback to AppSero Main Server:</span> ';
                echo "Cannot pingback the main appsero website. Could be a sign of an outgoing firewall issue.";
                echo '</div>';
            }else{
                echo '<div id="border1"><span class="green_dot"></span> <span style="font-size:1em;font-weight:bold;">Pingback to AppSero Main Server:</span> Successful';
                echo '</div>';
            }
            if (!is_array($this->apiSccConnection)) {
                echo '<div id="border1"><span class="red_dot"></span> <span style="font-size:1em;font-weight:bold;">Pingback to SCC Main Server:</span> ';
                echo "Cannot ping main SCC website. Could be an outgoing firewall problem.";
                echo '</div>';
            }else{
                echo '<div id="border1"><span class="green_dot"></span> <span style="font-size:1em;font-weight:bold;">Pingback to SCC Main Server:</span> Successful';
                echo '</div>';
            }
        }else{
            echo '<div id="border1"><span class="green_dot"></span> <span style="font-size:1em;font-weight:bold;">Pingback to License Server:</span> Successful';
            echo '</div>';
        }
        if ($test_char == "utf8mb4") {
            $results['has_good_charset'] = true;
            echo '<div id="border1"><span class="green_dot"></span> <span style="font-size:1em;font-weight:bold;">Charset:</span> ';
            echo esc_html($test_char);
            echo '</div>';
        } elseif ($test_char == "utf8") {
            $results['has_good_charset'] = true;
            echo '<div id="border1"><span class="yellow_dot"></span> <span style="font-size:1em;font-weight:bold;">Charset:</span> ';
            echo esc_html($test_char);
            echo "<br><Br>Suggestion: You should edit the DB_CHARSET variable in your wp_config.php file to utf8mb4";
            echo '</div>';
        } else {
            $results['has_good_charset'] = false;
            $results['bad_charset_msg'] = $test_char;
            echo '<div id="border1"><span class="red_dot"></span> <span style="font-size:1em;font-weight:bold;">Charset:</span> ';
            echo esc_html($test_char);
            echo "<br><Br>Warning: You should edit the DB_CHARSET variable in your wp_config.php file to utf8mb4";
            echo '</div>';
        }
        if ($postMaxAmt > 20) {
            $results['has_good_upload_limit'] = true;
            echo '<div id="border1"><span class="green_dot"></span> <span style="font-size:1em;font-weight:bold;">Maximum Allowed Post Data:</span>';
            echo " $postMaxAmt M";
            echo '</div>';
        }
        if ($postMaxAmt < 20) {
            $results['has_good_upload_limit'] = false;
            echo '<div id="border1"><span class="yellow_dot"></span> <span style="font-size:1em;font-weight:bold;">Maximum Allowed Post Data:</span>';
            echo " $postMaxAmt M";
            echo "<br><br>Warning: You should increase 'post_max_size' to some value greater than <b>20M</b>.";
            echo "<br><a href='http://designful.freshdesk.com/en/support/solutions/articles/48001141896-wp-memory-limit-or-the-email-quote-gets-stuck-at-90-' target='_blank'>Full tutorial here.</a>";
            echo '<br></div>';
        }
        ?>
        </div>
        <br>
        <p style="padding-left:25px;padding-bottom:20px;font-weight:400px;font-size: 1em">
            If you use CloudFlare, please make sure you have <a target="_blank" href="https://help.mediavine.com/en/articles/450233-cloudflare-rocket-loader-conflict#:~:text=To%20disable%20Rocket%20Loader%2C%20open,and%20toggle%20the%20feature%20off.">disabled Rocket Loader</a>.
        </p>
        <?php
        $pageContent = ob_get_clean();
        if ( ! $is_ajax ) {
            echo wp_kses_post_deep($pageContent);
        }
        return $results;
    }
    function admin_menu()
    {
        add_submenu_page('scc-tabs', __('Diagnostic', 'stylishpl'), __('Diagnostic', 'stylishpl'), 'manage_options', 'stylish_cost_calculator_Diagnostic', array($this, 'diagnostic_page'));
    }
}
$stylish_cost_calculator_Diagnostic = new Stylish_Cost_Calculator_Diagnostic();
?>
