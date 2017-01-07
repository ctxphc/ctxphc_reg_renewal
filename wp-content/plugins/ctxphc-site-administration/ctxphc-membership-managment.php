<?php
function ctxphc_member_listing() {
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$paged -= 1;
$limit = 20;
$offset = $paged * $limit;

$args  = array(
    'role'   => 'subscriber',
    'number' => $limit,
    'offset' => $offset,
);

// Create the WP_User_Query object
global $wp_query;
$wp_query = new WP_User_Query($args);

// Get the results
$ctxphc_members = $wp_query->get_results();

if($ctxphc_members) {
    $total_members = $wp_query->total_users;
    $total_pages = intval($total_members / $limit) + 1; ?>

    <div class="wrap about-wrap">
	<div>
	    <div>
		<nav id="nav-single" style="clear:both; float:none; margin-top:20px;">
		    <h3 class="assistive-text">Member Navigation</h3>
		    <?php if ($page != 1) { ?>
			<span class="nav-previous"><a rel="prev" href="<?php the_permalink() ?>page/<?php echo $page - 1; ?>/"><span class="meta-nav">←</span> Previous</a></span>
		    <?php } ?>
		    <?php if ($page < $total_pages ) { ?>
			<span class="nav-next"><a rel="next" href="<?php the_permalink() ?>page/<?php echo $page + 1; ?>/">Next <span class="meta-nav">→</span></a></span>
		    <?php } ?>
		</nav>
	    </div>

	    <table class="member-list"> <?php

		// loop through each member
		$line_number = 0;
		foreach( $ctxphc_members as $member ){
		   // echo "<div>";
		    $member_info = get_userdata($member->ID);
		   // var_dump($member_info);
		   // echo "</div>";
		    $line_number++;
		    if ( $line_number & 1) { ?>
			<tr class="odd">
			    <td> <?php echo $member_info->user_firstname ; ?></td>
			    <td> <?php echo $member_info->user_lastname ; ?></td>
			    <td> <?php echo $member_info->user_email ; ?></td>
			</tr>
		    <?php } else { ?>
			<tr class="even">
			    <td> <?php echo $member_info->user_firstname ; ?></td>
			    <td> <?php echo $member_info->user_lastname ; ?></td>
			    <td> <?php echo $member_info->user_email ; ?></td>
			</tr>
		    <?php } //endif
		} ?>
	    </table> <!-- .member-list-table -->
	    <div>
		<nav id="nav-single" style="clear:both; float:none; margin-top:20px;">
		    <h3 class="assistive-text">Member Navigation</h3>
		    <?php if ($page != 1) { ?>
			<span class="nav-previous"><a rel="prev" href="<?php the_permalink() ?>page/<?php echo $page - 1; ?>/"><span class="meta-nav">←</span> Previous</a></span>
		    <?php } ?>
		    <?php if ($page < $total_pages ) { ?>
			<span class="nav-next"><a rel="next" href="<?php the_permalink() ?>page/<?php echo $page + 1; ?>/">Next <span class="meta-nav">→</span></a></span>
		    <?php } ?>
		</nav>
	    </div>
	</div>
    </div>

<?php } else { ?>
    <div class="post">
        <p>Sorry, no posts matched your criteria.</p>
    </div>
<?php }
}  ?>