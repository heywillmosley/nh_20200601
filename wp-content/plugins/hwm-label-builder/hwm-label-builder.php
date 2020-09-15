<?php
/**
 * Plugin Name: Label Builder
 * Plugin URI: https://thenewhuman.com
 * Description: Creates botanical labels for New Human products & prints to PDF
 * Version: 1.0
 * Author: Will Mosley
 * Author URI: https://heywillmosley.com
 */

// Defines
$plugin_slug = 'hwm-label-builder';
define( 'HWM_PLUGIN_DIR', WP_PLUGIN_DIR . "/$plugin_slug/" );
define( 'HWM_PLUGIN_URL', WP_PLUGIN_URL . "/$plugin_slug/" );

function hwm_label_builder_activate() {
  if ( !class_exists( 'GFForms' ) ) // Gravity Forms
      die('Plugin NOT activated: Gravity Forms is required to activate this plugin');

  if ( !class_exists( 'ACF' ) ) // Advanced Custom Fields
      die('Plugin NOT activated: Gravity Forms is required to activate this plugin');
}
register_activation_hook( __FILE__, 'hwm_label_builder_activate' );


/* TODO
// Adding Label Printer to Page Template List
function hwm_add_label_printer_template( $templates ) {

  $label_printer_filename = 'page-label-printer.php';
  $label_printer_file = plugin_dir_path( __FILE__ ) . $label_printer_filename;

   $templates[ plugin_dir_path( __FILE__ ) . $label_printer_filename ] = __( 'Label Printer', 'text-domain' );

   return $templates;
}
//add_filter ('theme_page_templates', 'hwm_add_label_printer_template');


function hwm_label_printer_page_template( $template )
{
    if (is_page()) {
        $meta = get_post_meta(get_the_ID());

        if (!empty($meta['_wp_page_template'][0]) && $meta['_wp_page_template'][0] != $template) {
            $template = $meta['_wp_page_template'][0];
        }
    }

    return $template;
}
//add_filter('template_include', 'hwm_label_printer_page_template', 99);


// Add custom page to /label-printer
//add_filter( 'page_template', 'page_label_printer' );
function page_label_printer( $page_template ) {

  if ( is_page( 'label-printer' ) ) {

    $page_template = dirname( __FILE__ ) . '/page-label-printer.php';
    return $page_template;
  }
}
*/

function hwm_label_printer( $product_id, $date, $batch ) {

  $field = get_fields ( $product_id );
  $product = wc_get_product( $product_id );
  $title = $product->get_name();
  $title = substr($title, 0, strpos($title, "(")); // remove oz from title
  $link = get_edit_post_link( $product_id );
  $sku = $product->get_sku();
  $tagline = $field['product_tagline'];
  $directions = $field['directions'];
  $weight = $field['product_weight'];
  $gallons = $field['gallons_in_mix'];
  $doses = $field['number_of_doses'];
  $method = $field['serving_method'];
  $notes = $field['notes'];
  $blend_ml = $field['total_blend_ing'];
  $tracked = $field['tracked_ingredients'];
  $ingredients = $field['ingredients']; // array
  $warning = "As with any supplement, if you have any questions regarding this product, or are pregnant or nursing, consult your healthcare provider.";
  $mfg = substr($date, 0, -8) . '/' . substr($date, 6, 4 );
  $lot = $sku . substr($date, 0, -8) . substr($date, 3, 2 ) . substr($date, 8, 2 ) . $batch;

  $serving_size_text = "$doses $method";

  // remove weight oz text
  $filteredNumbers = array_filter(preg_split("/\D+/", $weight));
  $weight = reset($filteredNumbers);
  $blend = array();
  $track = array();
  $preservatives = array();
  $flavoring = array();
  $brand_whitelist = array('ProtoGenx', 'Celicore', 'Oligo', 'Profusion', 'Pure Essence', 'Flower Essence', 'Cell Salt', 'Flexatone');

  $blend_mg_per_dose = blend_mltomg( $blend_ml, $gallons, $doses, $method );


  // Tracked Ingredients
  $t = 0;
  $track_table = array();
  if( !empty( $tracked ) ) {
    foreach( $tracked as $track ) {
      $id = $track['ingredient'][0];
      $ing_track_title = get_the_title( $id );
      $ml = $track['mL'];

      // Pulled from the ingredient field
      $field = get_fields ( $id ); // ingredient ID
      $unit = $field['unit'];
      $daily_value_multiplier = $field['daily_value_multiplier'];

      $serving = get_serving( $ml, $gallons, $method );
      $daily_value = get_daily_value( $serving, $daily_value_multiplier );

      $track_table[$t]['ingredient'] = $ing_track_title;
      $track_table[$t]['serving'] = $serving;
      $track_table[$t]['daily'] = $daily_value;
      $track_table[$t]['method'] = $method;
      $track_table[$t]['unit'] = $unit;
      ++$t;
    }
  }

  if( empty( $ingredients ) )
    die("Please enter Blend, Preservatives and Favoring to <a href='$link' target'_blank'>$title</a>. Then try again.");

  // dial in to the ingredients
  foreach( $ingredients as $ingredient ) {

    $ing_title = $ingredient->post_title;

    $ing_types = get_the_terms( $ingredient->ID , 'ing_type' );
    foreach( $ing_types as $type )
      $ing_type = $type->name;

    switch ( $ing_type ) {
      case "blend":
        $blend[] = $ing_title;
        break;
      case "preservative":
        $preservatives[] = $ing_title;
        break;
      case "flavoring":
        $flavoring[] = $ing_title;
        break;
    } // end switch
  }

  $blend_list = get_ingredient_list( $blend );
  $preservative_list = get_ingredient_list( $preservatives );
  $flavoring_list = get_ingredient_list( $flavoring );

  // Build out the brand categories
  foreach( get_the_terms( $product_id, 'product_cat' ) as $category) {

    if( in_array( $category->name, $brand_whitelist ) ) {
      $brand = $category->name;
      break;
    }
  }

  $label_size = $weight; // in oz
  $label_size_class = $label_size . 'oz';
  $label_ml = oz2ml( $label_size );

  switch( $label_size ) {
    case 1:
      $pdf_medium = 'pdf-letter-portrait';
      $label_count = 12;
      break;
    case 4:
      $pdf_medium = 'pdf-letter-landscape';
      $label_count = 8;
      break;
    default:
      $pdf_medium = 'pdf-letter-portrait';
      $label_count = 8;
  }

$html = <<<HTML
<style>
@media all {
  @font-face {
    font-family: 'Open Sans';
    font-style: italic;
    font-weight: 400;
    src: local('Open Sans Italic'), local('OpenSans-Italic'), url(https://fonts.gstatic.com/s/opensans/v17/mem6YaGs126MiZpBA-UFUK0Zdcg.ttf) format('truetype');
  }
  @font-face {
    font-family: 'Open Sans';
    font-style: normal;
    font-weight: 400;
    src: local('Open Sans Regular'), local('OpenSans-Regular'), url(https://fonts.gstatic.com/s/opensans/v17/mem8YaGs126MiZpBA-UFVZ0e.ttf) format('truetype');
  }
  @font-face {
    font-family: 'Open Sans';
    font-style: normal;
    font-weight: 600;
    src: local('Open Sans SemiBold'), local('OpenSans-SemiBold'), url(https://fonts.gstatic.com/s/opensans/v17/mem5YaGs126MiZpBA-UNirkOUuhs.ttf) format('truetype');
  }


  /* CSS Reset */
  a,abbr,acronym,address,applet,article,aside,audio,b,big,blockquote,body,canvas,caption,center,cite,code,dd,del,details,dfn,div,dl,dt,em,embed,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,header,hgroup,html,i,iframe,img,ins,kbd,label,legend,li,mark,menu,nav,object,ol,output,p,pre,q,ruby,s,samp,section,small,span,strike,strong,sub,summary,sup,table,tbody,td,tfoot,th,thead,time,tr,tt,u,ul,var,video{margin:0;padding:0;border:0;font-size:100%;font-family:'Open Sans';vertical-align:baseline}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}body{line-height:1}ol,ul{list-style:none}blockquote,q{quotes:none}blockquote:after,blockquote:before,q:after,q:before{content:'';content:none}table{border-collapse:collapse;border-spacing:0}

  /* Keeps our widths right for print */
  html {
    box-sizing: border-box;
  }
  *,
  *:before,
  *:after {
    box-sizing: inherit;
  }

  p {
    margin-bottom: 5px;
    line-height: 1.2;
  }
  h1 {
    font-size: 12px;
    margin-bottom: 3px;
  }
  h2 {
    font-size: 8px;
    margin-bottom: 5px;
  }
  img {
    max-width: 100%;
  }
  strong {
    font-weight: 700;
  }
  small {
    font-size: 80%;
  }
  .document-title {
    font-size: 36px;
    line-height: 1.4;
  }
  .sheet-title {
    margin-top: 10px;
    font-size: 12px;
    line-height: 1.4;
  }
  .pdf {
    position: relative;
    width: 8.5in;
    height: 11in;
    padding: 0.25in 0.25in 0;
  }
  .label {
    position: relative;
    width: 4in;
    height: 4in;
    font-size: 3.6pt;
    background-color: #FFF;
    margin: 0;
    float: left;
    overflow-y: hidden;
  }
  .label .label-directives {
    width: 25%;
    float: left;
    height: 100%;
    position: relative;
  }
  .label .label-directives .label-directives-footer-absolute {
    position: absolute;
    bottom: 5px;
    left: 10px;
  }
  .label .label-logo {
    width: 7%;
    float: left;
    padding: 5px;
  }
  .label .label-logo img {
    max-height: 70%;
  }
  .label .label-brand {
    width: 34%;
    height: 100%;
    background-color: #E06224; /* Correct Orange #F05525 */
    float: left;
  }
  .label .label-facts {
    width: 34%;
    height: 1.75in;
    float: left;
  }
  .row {
    position: relative;
    width: 100%;
    display: block;
    clear: both;
    padding: 0;
    margin: 0;
  }
  .label-directives,
  .label-logo,
  .label-brand,
  .label-facts {
    padding: 10px;
    position: relative;
  }
  .label-mfg,
  .label-lot {
    margin-bottom: 3px;
  }
  .label-lot {
    right: 0;
  }
  .label-brand {
    color: #FFF;
    font-weight: bold;
    font-size: 8px;
    line-height: 1.2;
  }
  .label-brand-logo {
    margin: 0 0 5px;
    width: 50%;
  }
  .label-dietary {
    margin-bottom: 5px;
  }
  .label-tagline {
    margin-bottom: 5px;
  }
  .label-brand-title {
    font-size: 10px;
  }
  .label-facts-box {
    width: 100%;
    border: 1px solid;
    padding: 1px 1px 5px 1px;
  }
  .label-facts-box .label-serving-size {
    font-size: 4px;
    padding: 1px;
  }
  .label-facts-box .label-facts-table {
    border-top: 3px solid #000;
    width: 100%;
  }
  .label-facts-box .label-facts-table tr {
    border-bottom: 0.5px solid #000;
  }
  .label-facts-box .label-facts-table tr:last-child {
    border-bottom: 3px solid #000;
  }
  .label-facts-box .label-facts-table td {
    padding: 1px;
    font-size: 4px;
  }
  .label-facts-box .label-facts-table thead {
    border-bottom: 2px solid #000;
  }
  .label-facts-box .label-facts-table thead td {
    font-size: 3px;
    text-align: center;
    font-weight: bold;
    padding-bottom: 2px;
  }
  .label-facts-box .label-facts-table thead .table-ingredient {
    font-size: 5px;
    vertical-align: bottom;
    text-align: left;
  }
  .label-facts-box .label-facts-table .table-ingredient {
    width: 60%;
    font-size: 4px;
  }
  .label-facts-box .label-facts-table .table-amount {
    width: 20%;
    text-align: center;
  }
  .label-facts-box .label-facts-table .table-daily {
    width: 20%;
    text-align: center;
  }
  .label-facts-box .label-facts-table .table-blend,
  .label-facts-box .label-facts-table .table-flavoring,
  .label-facts-box .label-facts-table .table-preservatives {
    font-size: 3.5px;
    border: 0 solid #000;
  }
  .label-facts-box .label-facts-table .table-blend p,
  .label-facts-box .label-facts-table .table-flavoring p,
  .label-facts-box .label-facts-table .table-preservatives p {
    margin: 2px 0 0;
  }
  .pdf.pdf-letter-landscape {
    height: 8.5in;
    width: 11in;
  }
  .pdf.pdf-1oz {
    padding: 0.25in;
  }
  .pdf.pdf-1oz .label {
    width: 4in;
    height: 1.75in;
  }
  .pdf.pdf-4oz {
    padding: 0.25in 0.5in;
  }
  .pdf.pdf-4oz .label {
    width: 5in;
    height: 2in;
  }
  .pdf.pdf-8oz {
    padding: 0.169;
  }
  .pdf.pdf-8oz .label {
    width: 4in;
    height: 2.5in;
  }
  .pdf.pdf-8oz .label:nth-child(odd) {
    margin-right: 0.167;
  }
}

</style>

<div class="pdf $pdf_medium pdf-$label_size_class">

HTML;

$i = 0;
while ($i < $label_count ) {

  // Add row every two labels
  if( $i % 2 != 1 )
    $html .= "<div class='row'>"; // end row

  $html .= <<<HTML

<div class="label">
        <div class="label-directives">
          <div class="label-directions">
            <p><strong>Directions:</strong> $directions</p>
          </div>
          <div class="label-notes">
            <p><strong>Note:</strong> $notes</p>
  </div>
          <div class="label-warning">
            <p><strong>Warning:</strong> $warning</p>
          </div>
          <div class="label-mfg">MFG $mfg</div>
          <div class="label-lot">Lot # $lot</div>
          <div class="label-by">Manufactured by New Human</div>
          <div class="label-website">thenewhuman.com</div>
        </div>
        <div class="label-logo"><img src="https://www.thenewhuman.com/wp-content/uploads/2020/08/nh_logo_vertical.png" alt="New Human" /></div>
        <div class="label-brand">
          <img class="label-brand-logo" src="https://www.thenewhuman.com/wp-content/uploads/2020/08/Protogenx-Figurehead-500.png" alt="Brand Logo" />
          <h1 class="label-product-title">$title</h1>
          <h2 class="label-brand-title">$brand</h2>
          <div class="label-tagline">$tagline</div>
          <div class="label-dietary">Dietary Supplement</div>
          <div class="label-volume">$label_size fl oz. ($label_ml mL)</div>
        </div>
        <div class="label-facts">
          <div class="label-facts-box">
            <img src="https://www.thenewhuman.com/wp-content/uploads/2020/08/Supplement-Facts-500.png" alt="Supplement Facts" />
            <div class="label-serving-size">Serving Size $serving_size_text</div>
            <table class="label-facts-table">

HTML;
if( !empty( $tracked ) ) {

  $html .= <<<HTML
                <thead>
                  <tr>
                    <td class="table-ingredient">Active Ingredients</td>
                    <td class="table-amount">Amount Per Serving</td>
                    <td class="table-daily">% Daily Value</td>
                  </tr>
                </thead>
                <tbody>
  HTML;
  $html .= get_track_ingredient_table( $track_table );
}

// Show blend if ther are ingredients
if( !empty( $blend ) ) {
$html .= <<<HTML

                <tr class="table-blend">
                  <td colspan="3">
                    <p><strong>{$blend_mg_per_dose}mg of Proprietary Blend:</strong> $blend_list</p>
                  </td>
                </tr>
HTML;
} // end if

// Show blend if ther are ingredients
if( !empty( $preservatives ) ) {
$html .= <<<HTML

                <tr class="table-preservatives">
                  <td colspan="3">
                    <p><strong>Preservatives:</strong> $preservative_list</p>
                  </td>
                </tr>
HTML;
} // end if

// Show blend if ther are ingredients
if( !empty( $flavoring ) ) {
$html .= <<<HTML

                <tr class="table-flavoring">
                  <td colspan="3">
                    <p><strong>Flavoring:</strong> $flavoring_list</p>
                  </td>
                </tr>
HTML;
} // end if

$html .= <<<HTML

              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3"><strong>Inactive Ingredients:</strong> BioMineral Matrix in High Spin Cluster Spring Water.</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div><!-- end label -->

HTML;

  // Add row every two labels
  if( $i % 2 == 1 )
    $html .= "</div>"; // end row

  $i++;
}

$html .= "</div>";

  echo $html;
}

function oz2ml( $oz ) {
  return ceil($oz * 29.5735);
}


// $ingredients array()
function get_ingredient_list( $ingredients ) {
  $ingredient_list = '';
  $ingredient_count = count( $ingredients );
  $i = 0;
  foreach( $ingredients as $ingredient ) {
    $ingredient_list .= $ingredient;

    if( ++$i != $ingredient_count )
      $ingredient_list .= ", ";
  }

  return $ingredient_list;
}

// $track_ingredients array()
function get_track_ingredient_table( $track_table ) {

  $html = "";

  foreach( $track_table as $row ) {

    $ingredient = $row['ingredient'];
    $serving = $row['serving'];
    $daily = $row['daily']; // %
    $unit = $row['unit']; // %

$html .= <<<HTML
  <tr>
    <td class="table-ingredient">$ingredient</td>
    <td class="table-amount">$serving<small>{$unit}</small></td>
    <td class="table-daily">$daily</td>
  </tr>
HTML;

  }

  return $html;
}

// Get Serving size of the ingredient (in mL )
function get_serving( $ml, $gallons, $method ) {

  if( $method == 'teaspoon(s)' )
    $method_amt = 4.92892; // teaspoon
  else
    $method_amt = .2; // 5 drops

  return round( ( $ml / ( $gallons * 3800 ) ) * $method_amt, 3);
}

// Get daily Value of ingredient serving size divided by the daily value multiplier
function get_daily_value( $serving, $daily_value_multiplier ) {

  $daily = round( ( $serving / $daily_value_multiplier ) * 100, 3);


  if($daily < 1)
    return "<1%";
  else
    return "$daily%";
}

// $ml is total blend ml
// size of mix is always in gallons
function blend_mltomg( $ml_of_extracts_only_in_mix, $size_of_mix_in_gallons, $number_of_doses = 1, $method = 'drop(s)') {

  $teaspoon = 4.92892; // teaspoon
  $drop = .04; // drop

  if( $method == 'teaspoon(s)' )
    $unit = $number_of_doses * $teaspoon;
  else // make drops
    $unit = $number_of_doses * $drop;

  $grams_per_mix = 0.1194736842 * $ml_of_extracts_only_in_mix;
  $num_of_doses_per_mix = ( $size_of_mix_in_gallons * 3800 ) / $unit;
  $grams_per_dose = round( ( $grams_per_mix / $num_of_doses_per_mix ) * 1000, 3 );

  return $grams_per_dose;
}

// Render label printer for admins
function hwm_label_printer_form() {
  if( current_user_can('administrator' ) )
    return gravity_form( 22 );
}
add_shortcode('hwm_label_printer_form', 'hwm_label_printer_form');
