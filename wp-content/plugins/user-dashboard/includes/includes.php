<?php
ob_start();
$upload_dir	=	wp_upload_dir();

/* defined variables */

DEFINE('IMGPATH',plugins_url().'/'.NAME.'/images/');
DEFINE('DASHBOARD',add_query_arg(array('ac' => 'dashboard'), get_permalink()));
DEFINE('LOGIN',add_query_arg(array('ac' => 'login'), get_permalink()));
DEFINE('LOSTPASS',add_query_arg(array('ac' => 'lostpass'), get_permalink()));
DEFINE('REGISTRATION',add_query_arg(array('ac' => 'registration'), get_permalink()));
DEFINE('RENEWAL',add_query_arg(array('ac' => 'renewal'), get_permalink()));
DEFINE('PROFILE',add_query_arg(array('ac' => 'profile'), get_permalink()));
DEFINE('PROFILEIMAGE',add_query_arg(array('ac' => 'profileimage'), get_permalink()));
DEFINE('PROFILEIMAGEDIR',$upload_dir['baseurl']);

/* includes files */
include( plugin_dir_path( __FILE__ ) . 'functions.php');
include( plugin_dir_path( __FILE__ ) . 'filters.php');
include( plugin_dir_path( __FILE__ ) . 'baseclass.php');
include( plugin_dir_path( __FILE__ ) . 'adminclass.php');
include( plugin_dir_path( __FILE__ ) . 'render-user-dashboard-pages.php' );
?>