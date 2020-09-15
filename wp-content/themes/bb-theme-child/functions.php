<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

#Store Apps create an extremely spammy popup on all order pages. Hotfix please. WM
add_action('admin_head', 'storeapps_hotfix');

function storeapps_hotfix() {
  echo '<style>
    .post-type-shop_order #TB_window,
    .post-type-shop_order #TB_overlay,
    .post-type-shop_order .ig_content,
    .post-type-product #TB_window,
    .post-type-product #TB_overlay,
    .post-type-product .ig_content {
        display: none !important;
        position: relative !important;
    }
    body.modal-open.post-type-shop_order,
    body.modal-open.post-type-product {
        overflow: visible !important;
    }
  </style>';
}

// Redirect to custom homepage upon login
add_action('wp', 'add_login_check');
function add_login_check()
{
    if ( is_user_logged_in() && is_page( [ 90, // login
                                          //93, // lostpassword
                                          //94, // resetpass
                                          3987000 // home
                                          ] ) ) {
        wp_redirect('/');
        exit;
    }

    if ( !is_user_logged_in() && is_page( [30688888 // Random Disable
                                          ] ) ) {
        wp_redirect('/login');
        exit;
    }
}


// Subscribe to MC List upon new user registration
add_action( 'user_register', 'registration_to_mc', 10, 1 );

function registration_to_mc( $user_id ) {

    $user_info = get_userdata( $user_id );

    $email = $user_info->user_email;;
    $list_id = 'a981a755fb';
    $api_key = '07d44c5c8c9ed311e2b60f9c9a1fb23e-us5';

    $data_center = substr($api_key,strpos($api_key,'-')+1);

    $url = 'https://'. $data_center .'.api.mailchimp.com/3.0/lists/'. $list_id .'/members';

    $json = json_encode([
        'email_address' => $email,
        'status'        => 'subscribed', //pass 'subscribed' or 'pending'
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $api_key);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    $result = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo $status_code;

}


/* Load custom login page styles */
function my_custom_login() {
echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-styles.css" />';
}
add_action('login_head', 'my_custom_login');

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );


// Allow exe or dmg for digital downloads
add_filter('upload_mimes', function($mimetypes, $user)
{
    // Only allow these mimetypes for admins or shop managers
    $manager = $user ? user_can($user, 'manage_woocommerce') : current_user_can('manage_woocommerce');

    if ($manager)
    {
        $mimetypes = array_merge($mimetypes, array (
            'exe' => 'application/octet-stream',
            'dmg' => 'application/octet-stream',
            'mov' => 'video/quicktime'
        ));
    }

    return $mimetypes;
}, 10, 2);


//Send all emails from info@thenewhuman.com
add_filter( 'wp_mail_from', 'your_email' );
function your_email( $original_email_address )
{
  return 'info@thenewhuman.com';
}
add_filter( 'wp_mail_from_name', 'custom_wp_mail_from_name' );
function custom_wp_mail_from_name( $original_email_from )
{
  return 'The New Human';
}


// Skill Certificate Verification for Network Update
//add_filter( 'https_local_ssl_verify', '__return_false' );

// Don't require website url w/ AWP
/**
 * Plugin Name: AffiliateWP - Make URL Field Not Required
 * Plugin URI: http://affiliatewp.com
 * Description: Makes the URL field on the affiliate registration form not required
 * Author: Andrew Munro, Sumobi
 * Author URI: http://sumobi.com
 * Version: 1.0
 */
function affwp_custom_make_url_not_required( $required_fields ) {
  unset( $required_fields['affwp_user_url'] );
  return $required_fields;
}
add_filter( 'affwp_register_required_fields', 'affwp_custom_make_url_not_required' );


// Activate WordPress Maintenance Mode
function wp_maintenance_mode(){
    if(!current_user_can('edit_themes') || !is_user_logged_in()){
        wp_die('<h1 style="color:red">New Human is currently under scheduled maintenance</h1><br />We are performing scheduled maintenance. We will be back online shortly!');
    }
}
//add_action('get_header', 'wp_maintenance_mode');


/** Sort A - Z Packing Slips **/
add_filter( 'wpo_wcpdf_order_items_data', 'wpo_wcpdf_sort_items_by_name', 10, 2 );
function wpo_wcpdf_sort_items_by_name ( $items, $order ) {
    usort($items, 'wpo_wcpdf_sort_by_name');
    return $items;
}

function wpo_wcpdf_sort_by_name($a, $b) {
    if (!isset($a['name'])) $a['name'] = '';
    if (!isset($b['name'])) $b['name'] = '';
    if ($a['name']==$b['name']) return 0;
    return ($a['name']<$b['name'])?-1:1;
}

/**
 * Filter Force Login to allow exceptions for specific URLs.
 *
 * @return array An array of URLs. Must be absolute.
 */
function my_forcelogin_whitelist( $whitelist ) {
  $whitelist[] = site_url( '/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/login' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/terms/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/my-account/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/my-account/lost-password/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/my-account/lost-password/?' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/my-account/customer-logout/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/my-account/customer-logout/?' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/account/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/account/lost-password/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/account/lost-password/?' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/account/customer-logout/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/account/customer-logout/?' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/contact/' . $_SERVER['QUERY_STRING'] );
  $whitelist[] = site_url( '/wp-activate.php' . $_SERVER['QUERY_STRING'] );
  return $whitelist;
}
add_filter('v_forcelogin_whitelist', 'my_forcelogin_whitelist', 10, 1);

/**
 * Bypass Force Login to allow for exceptions.
 *
 * @return bool Whether to disable Force Login. Default false.
 */
function my_forcelogin_bypass( $bypass ) {
  if ( in_category('articles')
    || is_home()
    || is_front_page()
    || is_page(153258) // SV2 Webinar
    || is_page(153145) // SV2 Offer
    || is_page(6740) // Training
    || is_page(90) // Login
    || is_page(160607) // New User
    || is_page(176197) // Team
    || is_page(239163) // Team
    || is_page(238950) // Team
    || is_page(257878) // Clinic
    || is_page(15206) // Apply
    || is_page(177818) // Application Submitted

    ) {
    $bypass = true;
  }
  return $bypass;
}
add_filter('v_forcelogin_bypass', 'my_forcelogin_bypass', 10, 1);

// Display ZD Help Button for logged in users
add_action('wp_head', 'display_zd_logged_in');
function display_zd_logged_in(){

  if (is_user_logged_in()) { ?>
  <!-- Start of newhuman1 Zendesk Widget script -->
    <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=2a88c7d2-77bc-44e5-9925-e63fa468ad46"> </script>
    <!-- End of newhuman1 Zendesk Widget script -->

  <?php };

} // end function display_zd_logged_in()



##### Shortcodes ######
add_shortcode('webinar_dates', 'heywillmosley_webinar');
//echo heywillmosley_webinar();


function heywillmosley_webinar( $atts ) {

  // Ensure timezone is set to ET
  date_default_timezone_set('America/New_York');

    // set up default parameters
    extract( shortcode_atts( array(
     'start_date' => FALSE, // will default to today's date
     'start_time' => '1PM',
     'timespan' => '1 year',
     'interval' => '1 week', // week
     'display_count' => 15
    ), $atts) );

    $interval_type = explode(" ", $interval); // day, week, month, year
    $interval_count = $interval_type[0]; // gets how many weeks, days, etc
    $type = $interval_type[1]; // get interval from string
    $initial_interval = $interval_count;
    $form_url = esc_url(get_permalink()) . 'request'; // current url + request

    $render = "<div class='container'>";
    $render .= "<div class='row'>";
    $render .= "<div class='col'>";


    $render .= "<table class='table'>";
    $render .= "<thead>";
    $render .= "<tr>";
    $render .= "<th scope='col'>Date</th>";
    $render .= "<th scope='col'>Attend</th>";
    $render .= "</tr>";
    $render .= "</thead>";
    $render .= "<tbody>";

    $end_date = strtotime( "$start_date +$timespan" ); // ends in 1 year
    $count = 0; // adds 1 week every loop
    $skip = $count;

    do {
        $cut = $count - $skip;
        if( $cut == $display_count ) // only show certain #
            break;

        if( $count == 0 )
            $date = strtotime( "$start_date $start_time" );
        else {
            $date = strtotime( "$start_date $start_time +$interval_count $type" );
            $interval_count = $initial_interval + $interval_count;
        }


        if( strtotime("now") < $date  ) {

            $pretty_date = date('l F d, Y g:iA T', $date );
            $url_date = urlencode( date('m/d/Y', $date ) ); // escape for url
            $render .= "<tr>";
            $render .= "<td>$pretty_date</td>"; // Date
            $render .= "<td><a href='$form_url?d=$url_date'>Register</a></td>"; // Attend
            $render .= "</tr>";

            ++$count; // how many times we run through this loop

        } else {

            ++$count;
            ++ $skip;
        }

    } while ($date < $end_date);


    $render .= "</tbody>";
    $render .= "</table>";

    $render .= "</div>"; // col
    $render .= "</div>"; // row
    $render .= "</div>"; // container


    return $render;
}

function redirect_webinar_direct_access( ) {

    if( is_page( 238950 && !is_admin() ) ) { // webinar request page // Prod 238950 // Dev 4973
        if ( ! isset( $_GET[ 'd' ] ) || empty( $_GET[ 'd' ] ) ) {  // if date isn't set
            wp_redirect( home_url( '/webinar/' ) );
            exit();
        }
    }
}

add_action( 'template_redirect', 'redirect_webinar_direct_access' );

/*
 * 20200415 WM & IS : Handled redirection if someone stumbles on to the page accidentally
 * Tested to ensure it worked
 * 20200415 IS : Worked on zoom / vimeo get arguments and centering
 * 20200414 WM & IS : Created zoom viewer shortcode
 */
function heywillmosley_webinar_viewer() {

  $type = $_GET['type']; // is a a zoom or vimeo

  // Render based on type
  switch ( $type ) {

    case "vimeo":  // if the link is a vimeo link
      $url = "https://player.vimeo.com/video/" . $_GET['id'];

      $render = "<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 75%; height: auto; margin: 0 auto;} .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style>";
      $render .= "<div class='embed-container'><iframe src='$url' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
      $render .= "</div>";

      break;

    case "zoom":  //if the link is a zoom link
      $url = "https://zoom.us/rec/share/" . $_GET['id'];

      $render = "<div class='iframe-container' style='overflow: hidden; padding-top: 56.25%; position: relative;'>";
      $render .= "<iframe allow='microphone; camera' style='border: 0; height: 100%; left: 0; position: absolute; top: 0; width: 100%;' src='$url' frameborder='0'></iframe>";
      $render .= "</div>";

      break;

    default:  //if it's not any of the above

  } // end switch

  echo $render;

} // end heywillmosley_webinar_viewer

add_shortcode('webinar_viewer', 'heywillmosley_webinar_viewer');
//echo heywillmosley_webinar();

function redirect_webinar_viewer( ) {


  if( is_page( 247411 ) && !is_admin() ) { // is webinar viewer page // Dev page 247100 Prod page 247411

    if ( !isset( $_GET[ 'type' ] ) ) {  // if date isn't set

        wp_redirect( home_url( '/webinars/' ) );
        exit();
    }
  }
}
// Redirect if Get isn't set
add_action( 'template_redirect', 'redirect_webinar_viewer' );

/*
 * New Human Radio
*/

function radio_post_type() {

// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Albums', 'New Human Radio Albums', 'twentytwenty' ),
        'singular_name'       => _x( 'Album', 'New Human Radio Album', 'twentytwenty' ),
        'menu_name'           => __( 'Albums', 'twentytwenty' ),
        'parent_item_colon'   => __( 'Parent Album', 'twentytwenty' ),
        'all_items'           => __( 'All Albums', 'twentytwenty' ),
        'view_item'           => __( 'View Album', 'twentytwenty' ),
        'add_new_item'        => __( 'Add New Album', 'twentytwenty' ),
        'add_new'             => __( 'Add New', 'twentytwenty' ),
        'edit_item'           => __( 'Edit Album', 'twentytwenty' ),
        'update_item'         => __( 'Update Album', 'twentytwenty' ),
        'search_items'        => __( 'Search Album', 'twentytwenty' ),
        'not_found'           => __( 'Not Found', 'twentytwenty' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwenty' ),
    );

// Set other options for Custom Post Type

    $args = array(
        'label'               => __( 'albums', 'twentytwenty' ),
        'description'         => __( 'New Human Radio albums with sounds for the SLT.', 'twentytwenty' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies'          => array( 'sounds' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-format-audio',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,

    );

    // Registering your Custom Post Type
    register_post_type( 'albums', $args );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'radio_post_type', 0 );


add_action('woocommerce_product_set_stock', 'stock_log' ); // woocommerce_update_product

// Track stock changes in WooCommerce
function stock_log( $product_id ) {

  global $wp;
  $product = wc_get_product( $product_id );
  $hwm_url =  add_query_arg( $wp->query_vars, home_url( $wp->request ) );
  $form_id = 19;
  $search_criteria['field_filters'][] = array(
    'key' => 7,
    'value' => $product->id
  );
  $sorting = NULL;
  $paging = NULL;
  $total_count = 1;
  $previous_count_entries = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging, $total_count );
  $difference = "";
  $previous_count = "";

  // If on Quick Edit Page
  if( strpos( $hwm_url, '/wp-admin/admin-ajax.php' ) !== false ) { // editing from the quick edit page

    $current_user = wp_get_current_user();
    $mod_author = $current_user->display_name;
  }
  // If on Product Edit Page
  elseif ( strpos( $hwm_url, '/wp-admin/post.php' ) !== false )
    $mod_author = get_the_modified_author();

  // The System do stuff
  else
    $mod_author = "System";

  // Get previous count if it exists
  foreach( $previous_count_entries as $entry ) {

      $previous_count = $entry[2]; // previous stock count
      $difference = $product->stock_quantity - $previous_count; // set the difference
      break;
  }

  // Add to gravity forms
  $entry = array(
    'form_id' => $form_id,
    1 => $mod_author,
    2 => $product->stock_quantity,
    3 => $product->name,
    7 => $product->id,
    5 => $previous_count,
    6 => $difference
  );

  // Create entry in stock log form
  GFAPI::add_entry( $entry );
}


/*
 * Add Revision support to WooCommerce Products
 */
add_filter( 'woocommerce_register_post_type_product', 'cinch_add_revision_support' );

function cinch_add_revision_support( $args ) {
     $args['supports'][] = 'revisions';

     return $args;
}

function ingredients_post_type() {

// Set UI labels for Custom Post Type
    $labels = array(
        'name'                => _x( 'Ingredients', 'Ingredients', 'twentytwenty' ),
        'singular_name'       => _x( 'Ingredient', 'Ingredient', 'twentytwenty' ),
        'menu_name'           => __( 'Ingredients', 'twentytwenty' ),
        'parent_item_colon'   => __( 'Parent Ingredient', 'twentytwenty' ),
        'all_items'           => __( 'All Ingredients', 'twentytwenty' ),
        'view_item'           => __( 'View Ingredient', 'twentytwenty' ),
        'add_new_item'        => __( 'Add New Ingredient', 'twentytwenty' ),
        'add_new'             => __( 'Add New', 'twentytwenty' ),
        'edit_item'           => __( 'Edit Ingredient', 'twentytwenty' ),
        'update_item'         => __( 'Update Ingredient', 'twentytwenty' ),
        'search_items'        => __( 'Search Ingredient', 'twentytwenty' ),
        'not_found'           => __( 'Not Found', 'twentytwenty' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwenty' ),
    );

// Set other options for Custom Post Type

    $args = array(
        'label'               => __( 'ingredients', 'twentytwenty' ),
        'description'         => __( 'Ingredients used in our products.', 'twentytwenty' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy.
        'taxonomies'          => array( 'ingredients' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-carrot',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true,

    );

    // Registering your Custom Post Type
    register_post_type( 'ingredients', $args );

}

//ins - register custom taxonomy for Ingredient Tags
function ins_reg_ingredient_type() {

// Labels part for the GUI

// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI

  $labels = array(
    'name' => _x( 'Ingredient Type', 'taxonomy general name' ),
    'singular_name' => _x( 'Ingredient Type', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Ingredient Types' ),
    'all_items' => __( 'All Ingredient Types' ),
    'parent_item' => __( 'Parent Ingredient Type' ),
    'parent_item_colon' => __( 'Parent Ingredient Type:' ),
    'edit_item' => __( 'Edit Ingredient Type' ),
    'update_item' => __( 'Update Ingredient Type' ),
    'add_new_item' => __( 'Add New Ingredient Type' ),
    'new_item_name' => __( 'New Ingredient Type Name' ),
    'menu_name' => __( 'Ingredient Types' ),
  );

// Now register the taxonomy

  register_taxonomy('ing_type',array('ingredients'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'ing_type' ),
  ));
}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not
* unnecessarily executed.
*/

add_action( 'init', 'ingredients_post_type', 0 );

//ins - initialize the 'Ingredient Type' category for 'Ingredients'
add_action('init', 'ins_reg_ingredient_type');

// WM - Back to classic editor, yes
add_filter('use_block_editor_for_post', '__return_false', 10);


// Display Clinic Build and Email Admin and User Results
add_shortcode('hwm_clinic_builder', 'hwm_clinic_builder'); // 20200625 WM depreciated add_shortcode('hwm_sv2_build', 'hwm_sv2_build');


function hwm_clinic_builder( $atts ) {

  // set up default parameters
    extract( shortcode_atts( array(

     'show_price' => TRUE,
     'sidebar' => FALSE

    ), $atts) );

    // Convert True False String to Boolean
    switch( $show_price ) {
      case 'TRUE': $show_price = TRUE; break;
      default: $show_price = FALSE; break;
    }

    $user = wp_get_current_user();
    $clinic = hwm_gf_last_user_entry( 20 ); // Get the entry
    $is_mailed = $clinic[11];
    $prospect_msg = hwm_clinic_body ( $clinic, $show_price); // Prospect Message / Render
    $admin_emails = "lwoolley@thenewhuman.com, iswitzer@thenewhuman.com, wmosley@thenewhuman.com, jbrown@energeticwellnessok.com, sjohnson@thenewhuman.com";
    $admin_msg = $user->display_name . " created a Bionetic Clinic Build. Here are the built-out details we sent to the client: (including price)"; // Pre
    $admin_msg .= hwm_clinic_body ( $clinic, TRUE );

    // Mail stuff out
    if( $is_mailed == "no" ) {

      hwm_clinic_mail( $admin_emails, "SV2 Clinic Admin Email", $admin_msg ); // Admin Email
      hwm_clinic_mail( $user->user_email, "SV2 Clinic Prospect Email", $prospect_msg ); // Prospect Email
    }

  return $prospect_msg; // render out to page
}

// Displays sections
function hwm_clinic_section_builder( $field_meta, $section_name, $show_price = FALSE ) {

  // if field set to none option, then set to empty
  if( !is_array( $field_meta ) ) {
    if ( strpos( $field_meta, 'None' ) !== false )
      unset( $field_meta );
  }

  if( isset( $field_meta ) ) {

    // Header
    $render = "<tr class='section-head'>";
    $render .= "<td>";
    $render .= "<h3>$section_name</h3>";
    $render .= "</td>";
    $render .= "</tr>";

    // if field with options
    if( is_array( $field_meta ) ) {

      // Field Options
      foreach ( $field_meta as $f ) {

        $field = explode('|', $f );
        $field_type = $field[0];
        $field_price = '$' . number_format($field[1]);

        $render .= "<tr>";
        $render .= "<td>$field_type</td>";
        if( $show_price )
          $render .= "<td>$field_price</td>";

        $render .= "</tr>";

      } // end foreach field

    } else { // single field

      $field = explode('|', $field_meta );
      $field_type = $field[0];
      $field_price = '$' . number_format($field[1]);
      $render .= "<tr>";
      $render .= "<td>$field_type</td>";
      if( $show_price )
        $render .= "<td>$field_price</td>";
      $render .= "</tr>";
    }
  } // end if field_array

  return $render;

} // end function

function hwm_clinic_body ( $clinic, $show_price = FALSE ) {

  $id = $clinic['id'];
  $sv2_combo_base_name = $clinic['3.1'];
  $sv2_combo_base_price = $clinic['3.2'];
  $lasers = hwm_get_array_parts_with( $clinic, '7', '.' );
  $slt = hwm_get_array_parts_with( $clinic, '4', '.' ); // Sound Light Therapy
  $footbath = $clinic[6];
  $add_seats = hwm_get_array_parts_with( $clinic, '9', '.' );
  $starter_kit = $clinic[8];
  $clinic_total = '$' . number_format( $clinic[5] );
  $pubDate =  new DateTime( $clinic['date_created'] );
  $pubDate = $pubDate->format('D, d M Y');

  // Breakdown
  $render .= hwm_bb_module( 254112 ); // SV2 Clinic Builder Pre Email Text
  $render .= "<div class='table-responsive mt-4 hwm-clinic-build'>";
  $render .= "<h3>My Dream Clinic</h3>";
  $render .= "<table class='table'>";
  $render .= "<tr class='section-head'>";
  $render .= "<td>";
  $render .= "<strong>$sv2_combo_base_name</strong>";
  $render .= hwm_bb_module( 254100 ); // SV2 Base Info
  $render .= "</td>";
  if( $show_price )
    $render .= "<td>$sv2_combo_base_price</td>";
  $render .= "</tr>";
  $render .= hwm_clinic_section_builder( $lasers, 'LLLT', $show_price );
  $render .= hwm_clinic_section_builder( $slt, 'Sound Light Therapy', $show_price );
  $render .= hwm_clinic_section_builder( $footbath, 'Footbath', $show_price );
  $render .= hwm_clinic_section_builder( $add_seats, 'Certified SV2 Insight & Botanical Training Program for Additional Personnel', $show_price );
  $render .= hwm_clinic_section_builder( $starter_kit, 'Starter Kit', $show_price );

  if( $show_price ) {

    $render .= "<tr class='section-head'>";
    $render .= "<td><strong>Total</strong></td>";
    $render .= "<td><strong>$clinic_total</strong></td>";
    $render .= "</tr>";
  }
  $render .= "</table>";
  $render .= "</div>"; // end responsive table
  $render .= "<hr/>";
  //$render .= hwm_bb_module( 254117 ); // SV2 Clinic Builder Post Email Text

  return $render;
}

function hwm_clinic_mail( $to, $subject, $body ) {

    $headers = array('Content-Type: text/html; charset=UTF-8');

    // Send clinic consultation to client
    wp_mail( $to, $subject, $body, $headers );

    // Change mailed to yes in gravity form entry
    GFAPI::update_entry_field( $id, 11, 'yes' );
}

// Returns a field's contents
function hwm_gf_field ( $form_id, $field_id ) {

    $field = GFAPI::get_field( $form_id, $field_id );
    return $field->content;
}

// Display array prints pretty
function hwm_print($array, $return = FALSE ) {

    $render = "<pre>";
    $render .= print_r( $array, TRUE );
    $render .= "</pre>";

    if( !$return )
      echo $render;
    else
      return $render;
}

// Pulls any Beaver Builder Module in to PHP!
function hwm_bb_module( $id ) {

  ob_start();
  FLBuilder::render_query( array(
    'post_type' => 'fl-builder-template',
    'p'         => $id
) );
  return ob_get_clean();
}

// Show last gravity form entry from current user (or user with id)
function hwm_gf_last_user_entry ( $form_id, $user_id = 'Current', $count = 1 ) {

  // Set to current user
  if( $user_id == 'Current' )
    $user = wp_get_current_user();

  // Show "unlimited results", capped at 100
  if( $count == '-1' )
    $count = 100;

  // Entry parameters
  $search_criteria['status'] = 'active';
  $search_criteria['field_filters'][] = array( 'key' => 'created_by', 'value' => $user->ID );
  $paging = array( 'offset' => 0, 'page_size' => $count );
  $sorting = NULL;

  // Get entries
  $entries = GFAPI::get_entries( $form_id, $search_criteria, $sorting, $paging );

  // If 1, return just that entry vs array entries
  if(count( $entries ) == 1 ) {
    foreach( $entries as $entry )
      return $entry;
  } else
  return $entries;
}

function hwm_get_array_parts_with ( $array, $start_with, $seperator ) {

  foreach( $array as $key => $value ) {

    // Set explode key
      $exp_key = explode($seperator, $key);

      // if key matches what we're looking for
      if( $exp_key[0] == $start_with ) {

        // Add to array if value isn't empty
        if( !empty( $value) )
            $result[] = $value;
      }
  }

  if( isset( $result ) )
      return $result;
}

function admin_print( $array ) {
  echo "<pre>";
  print_r( $array );
  echo "</pre>";
}
