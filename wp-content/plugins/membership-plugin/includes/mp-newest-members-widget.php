<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/29/2014
 * Time: 5:04 PM
 */

//global $wpdb;
$rpt_type        = 1;
$order_by        = 'ID';
$order           = 'desc';
$current_members = get_ctxphc_members( $rpt_type, $order_by, $order );
// echo '<br> This is where I will include the 10 newest members basic info<br>';
?>
	<div class="main">
		<ul>
			<?php
			$ac = 0;
			while ( $ac <= 9 ) {
				$text = $current_members[ $ac ]->first_name;
				$text .= ' ' . $current_members[ $ac ]->last_name;
				$text .= ' ' . $current_members[ $ac ]->email;
				// $text = sprintf( $text, $active_members->email );
				// printf( '<li class="%1"><span>%2$s</span></li>', 'newest-members', $text );
				echo '<li class="newest-member"><span>' . $text . '</span></li>';
				$ac ++;
			}
			?>
		</ul>
	</div>
<?php