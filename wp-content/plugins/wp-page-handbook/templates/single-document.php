<?php
$css_url = plugins_url( WP_PAGE_HANDBOOK_NAME . '/public/css/wp-page-handbook-public.css' );
$logo = get_field( 'logo' );
$cover = get_field( 'cover' );
$doc_title = the_title( '', '', FALSE );
$intro_content = get_field('introduction_content');
$closing_content = get_field('closing_content');

echo "<link rel='stylesheet' href='$css_url' type='text/css' media='all' />";
$html = <<<HTML
<html>
<body>
  <div class="pdf cover">
    <img src="$cover" alt="Document Cover" />
  </div>
  <div class='pdf'>
    <img class="logo" src="$logo" alt="Logo" />
    <h1 class="doc-title">$doc_title</h1>
      <p>$intro_content</p>
HTML;

// Products
foreach( get_field( 'included_content' ) as $content ) {

  if( $content == 'Products' ) {

    $html .= "<h2 class='doc-section'>Products</h2>";

    if( get_field( 'product_content' ) == "Category(s)" ) {

      foreach( get_field( 'products_categories') as $category_ID ) {

        $cat_name = get_the_category_by_ID( $category_ID );
        $args = array(
          'status' => array( 'draft', 'publish' ),
          //'type' => array_merge( array_keys( wc_get_product_types() ) ),
          //'parent' => null,
          //'sku' => '',
          'category' => array( $cat_name ),
          //'tag' => array(),
          'limit' => -1,
          //'offset' => null,
          //'page' => 1,
          //'include' => array(),
          //'exclude' => array(),
          'orderby' => 'name',
          'order' => 'ASC',
          //'return' => 'objects',
          'paginate' => false,
          //'shipping_class' => array(),
        );
        $products = wc_get_products( $args );

        $html .= "<h3 class='product-line'>$cat_name</h3>";

        foreach( $products as $product ) {

          $name = $product->get_name();
          $desc = wp_strip_all_tags ( $product->get_description() );

          $html .= <<<HTML
          <div class="wp-product">
            <p><strong>$name</strong> $desc</p>
          </div>

          HTML;

        } // end foreach

        $html .= "<hr/>";

      } // end foreach

    } else { // individual products

      foreach( get_field( 'products') as $product_ID ) {

        $title = get_the_title( $product_ID ) . "<br/>";
        $content = wph_get_content( $product_ID ) . "<br/>";

      } // end foreach
    }

  } // end if products
}
$html .= <<<HTML
    <p>$closing_content</p>
  </div><!-- end pdf -->
</body>
</html>
HTML;


echo $html;

function wph_get_content( $ID ) { // array

  $post = get_post( $ID );
  return apply_filters('the_content', $post->post_content);

}
