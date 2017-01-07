<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/29/2014
 * Time: 4:51 PM
 */


$membership_types   = array(
	'ID',
	'IC',
	'CO',
	'HH'
);
$memb_text_singular = array(
	'ID' => 'Individual',
	'IC' => 'Individual and Child',
	'CO' => 'Couple',
	'HH' => 'Household'
);
$memb_text_plural   = array(
	'ID' => 'Individuals',
	'IC' => 'Individuals with Children',
	'CO' => 'Couples',
	'HH' => 'Households'
);
$total_members      = 0;
?>
	<div class="main">
		<ul>
			<?php

			foreach ( $membership_types as $memb_type ) {
				$num_members   = count_members( $memb_type );

				if ( isset( $num_members->memb_count )){
					$mcount        = $num_members->memb_count;
				} else {
					$mcount = 0;
				}

				if ( $mcount > 1 ){
					$text          = sprintf( '%s ' . sanitize_text_field( $memb_text_plural[ $memb_type ] ), number_format_i18n( $mcount ) );
				} else {
					$text          = sprintf( '%s ' . sanitize_text_field( $memb_text_singular[ $memb_type ] ), number_format_i18n( $mcount ) );
				}

				$text          = sprintf( $text, number_format_i18n( $mcount ) );
				// printf( '<li class="%1$s-count"><a href="list-members.php?membership_type=%1$s">%2$s</a></li>', $memb_type, $text );
				printf( '<li class="%1$s-count" ><span>%2$s </span ></li >', $memb_type, $text );

				$total_members = $total_members + $mcount;
			}

			$text = '--------------------';
			printf( '<li class="%1$s"><span>%2$s</span></li>', 'total-line', $text );
			$total_members = number_format_i18n( $total_members );
			$text          = "$total_members  Total Members<br><br>";
			// printf( '<li class="%1$s-count"><a href="list-members.php?membership_type=%1$s">%2$s</a></li>', 'total-member', $text );
			printf( '<li class="%1$s-count"><span>%2$s </span></li>', 'total-members', $text );
			?>
		</ul>
	</div>
<?php