<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 12/29/2014
 * Time: 4:56 PM
 */

?>
	<div class="main">
		<ul>
			<li><a href="<?php bloginfo( 'wpurl' . 'wp-admin/index.php?page=active_members' ) ?>">Active
					Members</a></li>
			<li><a href="<?php bloginfo( 'wpurl' . 'wp-admin/index.php?page=pending_members' ) ?>">Pending
					Members</a></li>
			<li><a href="<?php bloginfo( 'wpurl' . 'wp-admin/index.php?page=archived_members' ) ?>">Archived
					Members</a></li>
			<li><a href="<?php bloginfo( 'wpurl' . 'wp-admin/index.php?page=all_members' ) ?>">All
					Members</a></li>
			<li><a href="<?php plugins_url( 'includes/parrot-points.php', __FILE__ ) ?>">Parrot
					Point Status</a></li>
		</ul>
	</div>
<?php