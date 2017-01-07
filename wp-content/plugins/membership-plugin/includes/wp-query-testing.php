<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 1/6/2015
 * Time: 2:49 PM
 */

//debug settings
$debug = false;

if ( $debug ){
	ini_set('display_errors','off');
	ini_set('xdebug.collect_vars', 'on');
	ini_set('xdebug.collect_params', '4');
	ini_set('xdebug.dump_globals', 'on');
	ini_set('xdebug.dump.SERVER', 'REQUEST_URI');
	ini_set('xdebug.show_local_vars', 'on');
	error_reporting(E_ALL|E_STRICT);
} else {
	ini_set('display_errors', 'off');
	error_reporting(E_ERROR|E_WARNING|E_PARSE|E_NOTICE);
}

$user_args = array(
	'role'     => 'subscriber',
	'meta_key' => 'last_name',
	'meta_key' => 'user_email',
	'meta_key' => 'user_phone',
	'meta_key' => 'phone',
	'meta_key' => 'birthday',
	'meta_key' => 'occupation',
	'meta_key' => 'addr1',
	'meta_key' => 'addr2',
	'meta_key' => 'city',
	'meta_key' => 'state',
	'meta_key' => 'zip',
	'meta_key' => 'user_hatch_date',
	'meta_key' => 'user_tag',
	'meta_key' => 'user_initiated',
	'meta_key' => 'user_pending',
	'meta_key' => 'user_archived',
	'meta_key' => 'user_addr',
	'meta_key' => 'user_bday_month',
	'meta_key' => 'user_bday_day',

);
$users     = get_users( $user_args );

render_wp_meta_query( $users );


function render_wp_meta_query( $users ) { ?>
	<h1><?php the_title() ?></h1>
	<div class='post-content'><?php the_content() ?></div>
	<?php
	//var_dump( $users );
	foreach ( $users as $user ) { ?>
		<h2>User Data for <?php echo $user->first_name . ' ' . $user->last_name; ?></h2>
		<ul> <p>User Data</p><?php
			foreach ( $user->data as $ukey => $uval ) {
				echo "<li> $ukey ||| $uval </li>";
			} ?>
		<h3>User Meta Data</h3>
		<?php
			//var_dump( $user );
			$user_metadata = array_map( function ( $a ) {
				return $a[ 0 ];
			}, get_user_meta( $user->ID ) );

			foreach ( $user_metadata as $key => $value ) { ?>
				<li><?php echo $key . ' ||| ' . $value; ?></li>
			<?php } ?>
		</ul>
		<br/>
	<?php }
}


function fix_user_bday_value( $bday_val ) {
	if ( 0 === $bday_val ) {
		$bday_val = null;
	} else {
		$bday_val = sprintf( '%02s', $bday_val );
	}

	return $bday_val;
}

