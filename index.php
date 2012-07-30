<?php

/* 
Plugin Name: WP Responsive Images
Plugin URI: http://www.stuartbates.com
Description: Automatically serve up mobile friendly images to small screen mobile devices
Author: Stuart Bates
Version: 1.0
Author URI: http://www.stuartbates.com
*/

/*  
Copyright 2012  Stuart Bates  (email : hello@stuartbates.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Includes */
include 'WPResponsiveImages.php';
include 'SBMobileDetect.php';
include 'SBMobileDetectByCookie.php';
include 'SBMobileDetectByUserAgent.php';
include 'SBImage.php';
include 'SBFileCache.php';
include 'SBOutputImage.php';
include 'simple_html_dom.php';
include 'functions.php';

/* Run */
$activeImages = new WPResponsiveImages();