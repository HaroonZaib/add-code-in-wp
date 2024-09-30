<?php
/**
 * Plugin Name: Add Code in Wp
 * Description: A plugin to inject JavaScript and CSS across your WordPress website.
 * Version:     1.0
 * Author:  <a href="https://www.linkedin.com/in/haroon-zaib/">Haroon Zaib</a>
 * Text Domain: add-code-in-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function add_code_in_wp_hook() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'add_code_in_wp';
    $charset_collate = $wpdb->get_charset_collate();

$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
if ( ! $wpdb->get_var( $query ) == $table_name ) {

        $sql_data = "CREATE TABLE IF NOT EXISTS $table_name (
            id_js_css int(9) NOT NULL AUTO_INCREMENT,
            code_hook varchar(100) NOT NULL,
            post_data varchar(100) NOT NULL,
            location varchar(100) NOT NULL,
            location_url varchar(100) NOT NULL,            
            code_data mediumtext NOT NULL,
            pages_url varchar(100) NOT NULL,
            PRIMARY KEY (id_js_css)
        ) $charset_collate";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_data);

        $sql_insert = "INSERT INTO $table_name (code_hook ,post_data ,location ,location_url ,code_data, pages_url) VALUES ('allwebdata_head', '', '', '', '', '')
        ,('allwebdata_footer', '', '', '', '', '')
        ,('products_head', '', '', '', '', '')
        ,('products_footer', '', '', '', '', '') ";

        if ($wpdb->query($sql_insert) === TRUE) {
          echo "New records created successfully";
        } else {
          return "Error: " . $wpdb->last_error . "<br>";
        }


    }    
}

// plugin activation hook
register_activation_hook( __FILE__, 'add_code_in_wp_hook' );

function add_code_in_wp_admin_menu() {
    add_menu_page(
        __('Add Code in Wp', 'textdomain'),
        'Add Code in Wp',
        'manage_options',
        'add-code-in-wp',
        'code_settings_page',
        'dashicons-admin-generic',
        
    );  
}
add_action( 'admin_menu', 'add_code_in_wp_admin_menu' );

function code_settings_page(){
    $active_tab = wp_unslash(isset($_GET['tab'])) ? wp_unslash($_GET['tab']) : 'whole_site';
?>
<div class="wrap">
<h2>Code Js Css</h2>
<h2 class="nav-tab-wrapper">
    <a href="?page=add-code-in-wp&tab=whole_site" class="nav-tab <?php echo $active_tab == 'whole_site' ? 'nav-tab-active' : ''; ?>">Whole Site</a>
    <a href="?page=add-code-in-wp&tab=products" class="nav-tab <?php echo $active_tab == 'products' ? 'nav-tab-active' : ''; ?>">Products</a>
</h2>

<?php

switch ($active_tab) {
    case 'whole_site':
        code_in_wp_textarea($active_tab, "allwebdata_head", "allwebdata_footer");
    break;
    case 'products':
        if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
            code_in_wp_textarea($active_tab, "products_head", "products_footer");
        }else{
            echo "<br><br>Please install & Active <b><i>Woocommerce</b></i>";
        }
    break;
    default:
    echo "<br>Your not on correct page, click above or <a href='?page=add-code-in-wp&tab=whole_site'>here</a>";
}

?>

</div>
<?php

}


/**
 * Enqueue my scripts and assets.
 * @param $hook
 */

function my_enqueue() {

    if ( basename($_SERVER['SCRIPT_NAME']) !== 'admin.php' && @wp_unslash($_GET['page']) !== 'add-code-in-wp' ){
        return;
    }
    wp_enqueue_script(
        'add_code_in_wp',
        plugin_dir_url(__FILE__) . 'js/script.js',
        array( 'jquery' ),
        '3.0.0',
        true
    );

    wp_localize_script(
        'add_code_in_wp',
        'add_code_in_wp_obj',
        array(
            'url' => admin_url( 'admin-ajax.php' ),
            //'nonce'    => wp_create_nonce( 'title_example' ),
        )
    );
}


include_once __DIR__ . '/assets/inc.php';

?>