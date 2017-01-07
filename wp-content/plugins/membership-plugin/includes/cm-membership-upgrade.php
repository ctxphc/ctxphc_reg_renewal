<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/29/2014
 * Time: 7:17 PM
 */
//todo: create procoess for performing upgrades.  Including database upgrades if needed
$new_version = '2.0.0';

if (get_option(MYPLUGIN_VERSION_KEY) != $new_version) {
	// Execute your upgrade logic here

	// Then update the version value
	update_option(MYPLUGIN_VERSION_KEY, $new_version);
}
