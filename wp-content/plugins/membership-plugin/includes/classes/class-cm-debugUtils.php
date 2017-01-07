<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 4/24/2015
 * Time: 10:41 AM
 */

namespace Membership\Classes;


class debugUtils {
	public static function callStack($stacktrace) {
		print str_repeat("=", 50) ."\n";
		$i = 1;
		foreach($stacktrace as $node) {
			print "$i. ".basename($node['file']) .":" .$node['function'] ."(" .$node['line'].")\n";
			$i++;
		}
	}
}