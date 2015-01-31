<?php
/**
* Plugin Name: Aviation Weather Briefing from NOAA
* Plugin URI: http://howtoflyahelicopter.com/aviation-weather-briefing/
* Description: This plugin retrieves data from NOAA API's to display METAR/TAF, Significant Weather and Upper Winds data on any post or page.
* Version: 1.0 
* Author: Simon Attard		
* Author URI: http://howtoflyahelicopter.com
* License: Copyright (C) 2014  Simon Attard
*/
include ("functions_wx.php");
register_activation_hook(__FILE__,'create_tables');
add_shortcode ('winds','winds');
add_shortcode ('metar_taf','metar_taf');
add_shortcode ('sigwx','sigwx');
add_action('admin_menu','aviation_wx_admin_actions');


