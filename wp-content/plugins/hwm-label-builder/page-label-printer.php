<?php 
// If all of the GETs required are set, then run label printer, else, redirect to homepage
if ( isset( $_GET[ 'ID' ] ) && isset( $_GET[ 'date' ] ) && $_GET['batch'] )
     hwm_label_printer($_GET['ID'], $_GET['date'], $_GET['batch']);
else {
	wp_redirect( home_url( '/' ) );
	exit();
}