<?php
add_action( 'wp_dashboard_setup', 'ctxphc_admin_dashboard_setup' );
function ctxphc_dashboard_admin_setup() {
    add_meta_box( 'ctxphc_membership_count_wigdet', 'CTXPHC Membership Counts', 'ctxphc_membership_count_widget', 'ctxphc_admin_dashboard', 'side', 'high' );
}
function ctxphc_membership_count_widget() {
    // widget content goes here
    Global $wpdb;
    $bdayData = array();
    $ActSingles = count_active_singles();
    $ActCouples = count_active_couples();
    $ActFamilies = count_active_families();
    $ActMembers = $ActSingles + $ActCouples + $ActFamilies;

    ?>
<div>
    Singles: <?php echo $ActSingles; ?>
</div>
<div>
    Couples: <?php echo $ActCouples; ?>
</div>
<div>
    Families: <?php echo $ActFamilies; ?>
</div>
<?php }




echo "<link rel='stylesheet' media='screen,projection' type='text/css' href='ctxphc_dashboard.css'>";


//Begin CTXPHC Main Dashboard Page?>

<div class="wrap">
    <div id="icon-index" class="icon32"><br></div>
    <h2>CTXPHC Dashboard</h2>
    <div id="dashboard-widgets-wrap">
	<div id="dashboard-widgets" class="metabox-holder columns-2">
	    <div id="postbox-container-1" class="postbox-container">
		<div id="normal-sortables" class="meta-box-sortables ui-sortable">
		    <div id="dashboard_right_now" class="postbox ">
			<div class="handlediv" title="Click to toggle"><br></div>
			<h3 class="hndle"><span>Right Now</span></h3>
			<div class="inside">
			    <div class="table table_content">
				<p class="sub">Membership Totals</p>
				<table>
				    <tbody>
					<tr class="first">
					    <td class="first b b-posts">
						    <?php echo $ActSingles; ?>
					    </td>
					    <td class="t posts">
						    Singles
					    </td>
					</tr>
					<tr>
					    <td class="first b b_pages">
						    <?php echo $ActCouples; ?>
					    </td>
					    <td class="t pages">
						    Couples
					    </td>
					</tr>
					<tr>
					    <td class="first b b-cats">
						    <?php echo $ActFamilies; ?>
					    </td>
					    <td class="t cats">
					    Families
				    </td>
				</tr>
				<tr>
				    <td class="first b b-tags">
					    <?php echo $ActMembers; ?>
				    </td>
				    <td class="t tags">
					    Total Members
				    </td>
				</tr>
			    </tbody>
			</table>
		    </div> <!-- table table_content -->
		</div> <!-- end class inside -->
	    </div>
	    <div id="dashboard_recent_comments" class="postbox ">
		<div class="handlediv" title="Click to toggle">
			<br>
		</div>
		<h3 class="hndle"><span>NewsLetter Upload</span></h3>
		<div class="inside">
			<form id="quick-press" method="post" action="http://www.ctxphc.com/wp-content/plugins/ctxphc-admin/newletterUpload.php" name="post">
			 <span>This will be where the newletter upload form is filled in.</span>
			</form>
		</div>
	    </div>
	</div>  <!-- end of normal-sortables -->
    </div>  <!-- end of postbox-containter-1 -->
    <div id="postbox-container-2" class="postbox-container">
    <div id="side-sortables" class="meta-box-sortables ui-sortable">
	<div id="dashboard_quick_press" class="postbox ">
	    <div class="handlediv" title="Click to toggle"><br></div>
	    <h3 class="hndle"><span>Upcoming Birthdays</span></h3>
	    <div class="inside">
		<div class="table table_content">
		    <p class="sub"><?php echo date('F', mktime(0,0,0,date('m') + 1,1)); ?></p>
		    <table id="dash-bdays">
		        <tbody>
			    <?php $nMonthBDays = $wpdb->get_results("SELECT memb_id, memb_fname, memb_lname, memb_bday_month, memb_bday_day, memb_email
			    FROM  ctxphc_ctxphc_members WHERE memb_bday_month = MONTH( CURDATE() + INTERVAL 1 MONTH ) ORDER by memb_bday_day");
			    if ($nMonthBDays) {
				global $nMonthBDay;
				    foreach ( $nMonthBDays as $nMonthBDay) {?>
				        <tr class="first">
					    <td class="b b-comments">
						<?php echo "{$nMonthBDay->memb_fname} {$nMonthBDay->memb_lname}"; ?>
					    </td>
					    <td class="b b-comments">
						<?php echo "{$nMonthBDay->memb_email}"; ?>
					    </td>
					    <td class="last t comments">
						<?php echo "{$nMonthBDay->memb_bday_month}/{$nMonthBDay->memb_bday_day}"; ?>
					    </td>
					</tr>
					<?php }
					$spouseBDays = $wpdb->get_results("SELECT sp_fname, sp_lname, sp_bday_month, sp_bday_day, sp_email
					    FROM ctxphc_ctxphc_memb_spouses WHERE sp_bday_month = MONTH( CURDATE() + INTERVAL 1 MONTH ) ORDER by sp_bday_day");
					    if ($spouseBDays) {
					        foreach ( $spouseBDays as $spouseBDay) { ?>
						    <tr class="first">
							<td>
							    <?php echo "{$spouseBDay->sp_fname} {$spouseBDay->sp_lname}"; ?>
							</td>
							<td class="b b-comments">
							    <?php echo "{$spouseBDay->sp_email}"; ?>
							</td>
							<td>
							    <?php echo "{$spouseBDay->sp_bday_month}/{$spouseBDay->sp_bday_day}"; ?>
							</td>
						    </TR>
						<?php }
					    }
					    $famBDays = $wpdb->get_results("SELECT fam_fname, fam_lname, fam_bday_month, fam_bday_day, fam_email
					    FROM ctxphc_ctxphc_family_members  WHERE fam_bday_month = MONTH( CURDATE() + INTERVAL 1 MONTH ) ORDER by fam_bday_day");
					    if ($famBDays) {
						foreach ( $famBDays as $famBDay) { ?>
						    <TR>
						        <td>
							    <?php echo "{$famBDay->fam_fname} {$famBDay->fam_lname}"; ?>
						        </td>
						        <td class="b b-comments">
							    <?php echo "{$famBDay->fam_email}"; ?>
						        </td>
							<td>
							    <?php echo "{$famBDay->fam_bday_month}/{$famBDay->fam_bday_day}"; ?>
							</td>
						    </TR>
						<?php }
					    }
					} ?>
				    </tbody>
			    </table>
		    </div>
			</div>  <!-- end class inside -->
		    </div>  <!-- end dashboard_quick_press -->
		</div>
	    </div>  <!-- end of Postbox Container 2 -->
	</div>  <!-- end of Dashboard Widget -->
    </div> <!-- end of dashboard-widgets-wrap -->
</div>

<div class="clear"></div>