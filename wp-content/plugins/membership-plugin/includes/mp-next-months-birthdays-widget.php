<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/29/2014
 * Time: 5:02 PM
 */

$date_format = 'm';
$next_month  = get_next_months_name( $date_format );
$rpt_type    = 1;
$order_by    = 'bday';
$order       = 'asc';
$memberss     = get_ctxphc_members( $rpt_type, $order_by, $order );
?>
	<div class="main">
		<ul>
			<?php
			foreach ( $members as $member ) {
				$a_date = explode( '/', $member->bday );
				if ( $a_date[0] === $next_month ) {
					$text = sanitize_text_field( $member->first_name ) . ' ' . sanitize_text_field( $member->last_name );
					$text = $text . ' ' . sanitize_text_field( $member->bday );
					// printf( '<li class="%1$s"><a href="list-members.php?membership_type=%1$s">%2$s</a></li>', 'total-member', $text );
					echo '<li class="next-months-bdays"><span>' . $text . '</span></li>';
				}
			}
			?>
		</ul>
	</div>
<?php