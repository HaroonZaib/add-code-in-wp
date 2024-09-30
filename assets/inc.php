<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

//////////////////////------------------------////////////////////////////
/*
*Loading code for all pages and product pages in Header Tag
*/

function getdata_add_code_in_wp_header()
{
    $allowed_tags = array(
        //formatting
        'script' => array(),
        'style'     => array(),
        'meta'      => array(),
        'link'      => array(),
        'title'     => array(),
    );
    
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
     
        if (is_product()) {
            $web_head_data = add_code_in_wp_records('code_hook' ,'products_head');
            wp_register_script('cssjs_hz_creations', print(wp_kses($web_head_data[0]->code_data, $allowed_tags)) );
            wp_enqueue_script('cssjs_hz_creations');
        }
    }

    $web_head_data = add_code_in_wp_records('code_hook' ,'allwebdata_head');
    wp_register_script('cssjs_hz_creations', print(wp_kses($web_head_data[0]->code_data, $allowed_tags)) );
    wp_enqueue_script('cssjs_hz_creations');

}

add_action('wp_head' , 'getdata_add_code_in_wp_header');


/////////////////////------------------------------/////////////////////////////
/*
*Loading code for all pages and product pages in Footer Tag
*/


function getdata_wp_code_in_wp_footer()
{
    $allowed_tags = array(
        //formatting
        'script' => array(),
        'style'     => array(),
        'meta'      => array(),
        'link'      => array(),
        'title'     => array(),
    );

    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        if(is_product()){
            $web_footer_data = add_code_in_wp_records('code_hook', 'products_footer');
                wp_register_script('js_hz_creations', print(wp_kses($web_footer_data[0]->code_data, $allowed_tags)), '', false, true);
                wp_enqueue_script('js_hz_creations');
        }
    }

    $web_head_data = add_code_in_wp_records('code_hook' ,'allwebdata_footer', '', false, true);
    wp_register_script('js_hz_creations', print(wp_kses($web_head_data[0]->code_data, $allowed_tags)) );
    wp_enqueue_script('js_hz_creations');
}

add_action('wp_footer' , 'getdata_wp_code_in_wp_footer', 100);


/*
* This function will update code in database
*/
function add_code_in_wp_record_table(...$all_data) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'add_code_in_wp';
    $query_ok = '';

    foreach ($all_data as $value) {
        $query_part = "code_data='".$value[0]."' , pages_url='".$value[2]."' WHERE code_hook='".$value[1]."'";
        $sql_update = "UPDATE $table_name SET $query_part ";

        if($wpdb->query($sql_update)){ $query_ok = 'ok'; }else{ $query_ok = 'not_ok'; }

    }
    
    if ($query_ok == 'ok') {
        echo "<span style='background-color: green; color: white; border: 1px solid #d4d2c4; padding: 7px;'>Code Saved Successfully!</span>";
    }
}

/* 
* This function will load code from database
*/

function add_code_in_wp_records($code_hook, $code_hook_name) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'add_code_in_wp';
    $results = $wpdb->get_results( "SELECT * FROM $table_name WHERE $code_hook='$code_hook_name' " );
    
    return $results;
}
/*
*This function will load form data
*/

function code_in_wp_textarea($code_hook, $input_fleld_head, $input_fleld_footer){

    $allowed_tags = array(
        //formatting
        'script' => array(),
        'style'     => array(),
        'meta'      => array(),
        'link'      => array(),
        'title'     => array(),
    );

    $placeholder = "\n<link rel='stylesheet' id='chatgpt_ac_css-css' href='https://example.com/style.css media='all' />\n<style>.class_name{ ... }</style>\n\n<script>JavaScript Code</script>\n<script async data-cfasync='false' src='https://cdn.jquery.com/jquery.js' type='text/javascript'></script>";

    $placeholder_footer = "\n<script>JavaScript Code</script>\n<script async data-cfasync='false' src='https://cdn.jquery.com/jquery.js' type='text/javascript'></script>";
    ?>
    
    <form method="POST" id="add_code_in_wp_form" >
    <input type="hidden" value="<?php echo wp_kses($code_hook, $allowed_tags); ?>" name="add_code_in_wp" />
    <fieldset>
    <div id="response_data"></div>
    <label for="<?php echo wp_kses($input_fleld_head, $allowed_tags); ?>" >Your code will be placed inside <code>&lt;head&gt; &lt;/head&gt;</code> tag:</label>
    <br><textarea cols="100" rows="6" name="<?php echo wp_kses($input_fleld_head, $allowed_tags); ?>" id="<?php echo wp_kses($input_fleld_head, $allowed_tags); ?>" ><?php 
            $get_code_data =  add_code_in_wp_records("code_hook" ,wp_kses($input_fleld_head, $allowed_tags));
            echo wp_kses($get_code_data[0]->code_data, $allowed_tags);
        ?></textarea>
    </fieldset>
    <fieldset>
    <label for="<?php echo wp_kses($input_fleld_footer, $allowed_tags); ?>">Your code will be placed before <code>&lt;/body&gt;</code> tag:</label>
    <br><textarea cols="100" rows="6" id="<?php echo wp_kses($input_fleld_footer, $allowed_tags); ?>" name="<?php echo wp_kses($input_fleld_footer, $allowed_tags); ?>" ><?php 
            $get_code_data =  add_code_in_wp_records("code_hook" ,wp_kses($input_fleld_footer, $allowed_tags));
            echo wp_kses($get_code_data[0]->code_data, $allowed_tags);
        ?></textarea><br><br>
    <input type="hidden" name="pages_url" value="<?php echo(isset($_GET['tab'])) ? '?page='.wp_unslash($_GET['page']).'&tab='.wp_unslash($_GET['tab']) : '?page='.wp_unslash($_GET['page']); ?>">
    <input type="submit" value="Submit" />
    </fieldset>
    </form>
    <br><br><br>
    <p>
        <i>
            This plugin is powered by <a href="https://www.linkedin.com/in/haroon-zaib/">Haroon Zaib</a>
        </i>
    </p>
    <?php

}


//you can use switch statement to do more action performs on different pages
function add_code_in_wp_add_sql(){
    
    $table_form_head_handle = '';
    $table_form_footer_handle = '';
    $table_value_head = '';
    $table_value_footer = '';

    parse_str($_POST['action_value'], $action_value);
    $action_value_key = array_keys($action_value);
            $page_url_js_css = $action_value[$action_value_key[3]];
            $table_form_head_handle = $action_value_key[1];
            $table_form_footer_handle = $action_value_key[2];
            $table_value_head = $action_value[$action_value_key[1]];
            $table_value_footer = $action_value[$action_value_key[2]];
            add_code_in_wp_record_table(array($table_value_head, $table_form_head_handle, $page_url_js_css), array($table_value_footer, $table_form_footer_handle, $page_url_js_css));
}

add_action('wp_ajax_add_code_in_wp', 'add_code_in_wp_add_sql');
