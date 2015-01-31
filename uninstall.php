<?php
// License: Copyright (C) 2014  Simon Attard
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

// DROP CUSTOM TABLE

global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}upper_winds" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}swx" );
