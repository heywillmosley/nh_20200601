<?php
echo '<link rel="stylesheet" href="' . plugins_url( WP_PAGE_HANDBOOK_NAME . '/public/css/wp-page-handbook-public.css' ) .'" type="text/css" media="all" />';

$title = the_title( '', '', FALSE );

$html = "<div class='pdf'>";

// Products
foreach( get_field('products') as $product_ID ) {
  $title = get_the_title( $product_ID ) . "<br/>";
  $content = wph_get_content( $product_ID ) . "<br/>";

$html .= <<<HTML

<h1>$title</h1>
<p>$content</p>

HTML;
}

$html .= "</div>";

echo $html;



function wph_get_content( $ID ) { // array

  $post = get_post( $ID );
  return apply_filters('the_content', $post->post_content);

}
