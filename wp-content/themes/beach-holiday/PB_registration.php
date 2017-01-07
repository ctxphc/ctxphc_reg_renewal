<?php
/*
Template Name: PB Reg
*/
?>

<?php
global $defSel, $wpdb;
$debug = 'true';
$pbreg_data = array();

$attendee_count = 0;
$PB_reg_total = 0;
$item_name = '';
$item_num = '';
$tAmount = '';
$quantity = 0;
$pb_cost = 65.00;
$pbattend_count = 0;
$state = 'TX';
?>

<?php $states_arr = array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois",'IN'=>"Indiana",'IA'=>"Iowa",'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland",'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma",'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming"); ?>

<?php get_header(); ?>

<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
    jQuery(document).ready(function($){
        $("#pbRegForm").validationEngine('attach', {promptPosition : "bottomLeft"});
    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function($){
        $("#pb_attendee_1").show();
        $('#pb_attendee_2').validationEngine('hide');
        $("#pb_attendee_2").hide();
        $('#pb_attendee_3').validationEngine('hide');
        $("#pb_attendee_3").hide();
        $('#pb_attendee_4').validationEngine('hide');
        $("#pb_attendee_4").hide();
        $(".pb_attendeeCount").click(function(){
        if ($("input[name$='attendee_count']:checked").val() === "1" ) {
                $('#pb_attendee_2').validationEngine('hide');
                $("#pb_attendee_2").hide();
                $('#pb_attendee_3').validationEngine('hide');
                $("#pb_attendee_3").hide();
                $('#pb_attendee_4').validationEngine('hide');
                $("#pb_attendee_4").hide();
            } else if ($("input[name$='attendee_count']:checked").val() === "2" ) {
                $("#pb_attendee_2").show();
                $('#pb_attendee_3').validationEngine('hide');
                $("#pb_attendee_3").hide();
                $('#pb_attendee_4').validationEngine('hide');
                $("#pb_attendee_4").hide();
            } else if ($("input[name$='attendee_count']:checked").val() === "3" ) {
                $("#pb_attendee_2").show();
                $("#pb_attendee_3").show();
                $('#pb_attendee_4').validationEngine('hide');
                $("#pb_attendee_4" ).hide();
            } else if ($("input[name$='attendee_count']:checked").val() === "4" ) {
                $("#pb_attendee_2").show();
                $("#pb_attendee_3").show();
                $("#pb_attendee_4").show();
            }
        });
    });
</script>

<?php
if (isset($_POST['submit'])){
    $clean_post_data = array_map('mysql_real_escape_string', $_POST);
    foreach( $clean_post_data as $key => $value ){
        switch ( $key ){
            case ( $key == 'pb_fname'):
                $pbreg_data['first_name'] = $value;
                //error_log( "The first name key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'pb_lname' ):
                $pbreg_data['last_name'] = $value;
                //error_log( "The last name key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'pb_email'):
                $pbreg_data['email'] = $value;
                //error_log( "The email key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'addr1'):
                $pbreg_data['addr1'] = $value;
                //error_log( "The addr1  key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'addr2'):
                $pbreg_data['addr2'] = $value;
                //error_log( "The addr2 key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'city'):
                $pbreg_data['city'] = $value;
                //error_log( "The city key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'state'):
                $pbreg_data['state'] = $value;
                //error_log( "The state key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'zip'):
                $pbreg_data['zip'] = $value;
                //error_log( "The zip key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'club_affiliation'):
                $pbreg_data['club_aff'] = $value;
                //error_log( "The club_affiliation key = $key and the value = $value.", 0 );
                break;
            case ( $key == 'attendee_count'):
                $pbreg_data['quantity'] = $value;
                $pbreg_data['amount'] = $pb_cost * $value;
                switch ( $value ){
                       case '1'; ?>
                           <script type="text/javascript">
                               jQuery(document).ready(function($){
                                   $('#pb_attendee_2').validationEngine('hide');
                                   $("#pb_attendee_2").hide();
                                   $('#pb_attendee_3').validationEngine('hide');
                                   $("#pb_attendee_3").hide();
                                   $('#pb_attendee_4').validationEngine('hide');
                                   $("#pb_attendee_4" ).hide();
                               )};
                           </script>
                           <?php
                           break;
                       case '2'; ?>
                           <script type="text/javascript">
                               jQuery(document).ready(function($){
                                   $("#pb_attendee_2").show();
                                   $('#pb_attendee_3').validationEngine('hide');
                                   $("#pb_attendee_3").hide();
                                   $('#pb_attendee_4').validationEngine('hide');
                                   $("#pb_attendee_4").hide();
                               )};
                           </script>
                           <?php
                         break;
                       case '3'; ?>
                           <script type="text/javascript">
                               jQuery(document).ready(function($){
                                   $("#pb_attendee_2").show();
                                   $("#pb_attendee_3").show();
                                   $('#pb_attendee_4').validationEngine('hide');
                                   $("#pb_attendee_4").css("display","none");
                               )};
                           </script>
                           <?php
                        break;
                       case '4'; ?>
                           <script type="text/javascript">
                               jQuery(document).ready(function($){
                                   $("#pb_attendee_2").show();
                                   $("#pb_attendee_3").show();
                                   $("#pb_attendee_4").show();
                               )};
                           </script>
                           <?php
                           break;
                }
                break;
            case ( strpos( $key, 'pb_attendee_fname' ) ):
                //error_log( "The attendee name key = $key and the value = $value.", 0 );
                $attendee_name = $value;
                break;
            case ( strpos( $key, 'pb_attendee_lname' ) ):
                $pbattend_count++;
                $attendee_name .= ' ' . $value;
                //error_log( "The full attendee name key = $key and the value = $value.", 0 );
                $pbkey = 'attendee_' . $pbattend_count;
                $pbreg_data[$pbkey] = $attendee_name;
                break;
        }
    }

    $pbreg_results = process_pb_registration('ctxphc_pb_reg', $pbreg_data);

    if ( $pbreg_results ) {
        $pbRegID = $pbreg_results;
        $reg_data = $wpdb->get_row("SELECT * FROM ctxphc_pb_reg WHERE pbRegID = $pbRegID");
        foreach ( $reg_data as $pb_key => $pb_value ){
            error_log( "The key is $pb_key and the value is $pb_value.");
        }

        /*****************************************************
         * BEGIN: Pirate's Ball Registration Entry Confirmation and Payment Page
         *****************************************************/
        ?>
        <div id="content">
            <div class="spacer"></div>
            <?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <div id="post_title" class="post_title">
                        <h1><a href="<?php the_permalink() ?>" rel="bookmark"
                               title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                    </div>
                    <!-- Post_title -->

                    <div class="clear"></div>

                    <div class="entry">
                        <?php the_content( 'more...' ); ?>
                        <div class="clear"></div>

                        <div class="spacer"></div>

                        <div class="pb_header">
                            <h2 class="pieces_of_eight">Central Texas Parrot Heads</h2>
                            <h2 class="pieces_of_eight">2014 Pirate's Ball</h2>
                            <h2 class="pb_header">Early Registration</h2>
                        </div>

                        <div class="spacer"></div>

                        <div>
                            <img id='PB_logo' alt="CTXPHC Pirate's Ball 2014 Logo" src="<?php echo get_template_directory_uri(); ?>/includes/images/Pirates_Ball/2014Pirates.jpg" width="300" height="189"/>
                        </div>

                        <div class="spacer"></div>

                        <div>
                            <h3 class="pb_thank_you">Thank you <?php echo "{$reg_data->first_name}  {$reg_data->last_name}"; ?>,</h3>
                            <p class="pb_text">Your registration for the 2014 CTXPHC Pirate's Ball is almost complete!</p>

                            <p class="pb_text">Please verify your information then pay through PayPal below.</p>

                            <div class="spacer"></div>

                            <form id="pbRegForm" name="regForm" method="post" action="">
                                <input type="hidden" value="<?php echo $pbRegID; ?>" name="pb_regID">

                                <fieldset class="pb_reg_form"   id=members_info>
                                    <legend><span class="memb_legend">Your Information</span></legend>
                                    <div id="personal_info">
                                        <label id="pb_fname" for="pb_fname">First Name:</label>
                                        <input type=text class="pb_field validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id="pb_fname" name="pb_fname" value="<?php echo $reg_data->first_name;?>"/>
                                        <label id="pb_lname" for="pb_lname">Last Name:</label>
                                        <input type=text class="pb_field validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" id="pb_lname" name="pb_lname" value="<?php echo $reg_data->last_name;?>"/>
                                    </div>

                                    <div id="pb_email">
                                        <label id="pb_email" for=pb_email>Email:</label>
                                        <input class="pb_field validate[required, custom[email]]" data-prompt-position="bottomLeft" id="pb_email" name="pb_email" type="text" value="<?php echo $reg_data->email;?>"/>
                                        <label id="pb_email_verify" for=pb_email_verify>Verify Email:</label>
                                        <input class="pb_field validate[required, custom[email]]" data-prompt-position="bottomLeft" id="pb_email_verify" name="pb_email_verify" type="text" value="<?php echo $reg_data->email;?>"/>
                                    </div>

                                    <div id='pb_club_affiliation'>
                                        <label id="pb_club_affiliation" for=club_affiliation>PHC Affiliation or None:</label>
                                        <input class="pb_field validate[required, custom[ClubAffiliation]]" data-prompt-position="bottomLeft" id="club_affiliation" name="club_affiliation" type="text" value='<?php echo $reg_data->club_aff; ?>' />
                                    </div>
                                </fieldset>

                                <div class='spacer'></div>

                                <fieldset class="pb_reg_form" id=address>
                                    <legend><span class="memb_legend">Address</span></legend>
                                    <div>
                                        <label id="pb_addr1" for="pb_addr1">Address:</label>
                                        <input id="pb_addr1" name="addr1" type="text" value='<?php echo $reg_data->addr1; ?>' class="pb_field validate[required, custom[address]]" data-prompt-position="bottomLeft" />
                                        <label id="pb_addr2" for="pb_addr2">Apt/Suite:</label>
                                        <input id="pb_addr2" name=addr2 type=text  value='<?php echo $reg_data->addr2; ?>' class="pb_field validate[custom[address-2]]" data-prompt-position="bottomLeft" />
                                    </div>
                                    <div>
                                        <label id="pb_city" for="pb_city">City:</label>
                                        <input id="pb_city" name="city" type="text"  value='<?php echo $reg_data->city; ?>' class="pb_field validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
                                        <label id="pb_state" for="pb_state">State:</label>
                                        <select id="pb_state" name="state" class="pb_field validate[required]">
                                            <?php echo showOptionsDrop($states_arr, $reg_data->state, true); ?>
                                        </select>
                                        <label id="pb_zip" for="pb_zip">Zip:</label>
                                        <input id="pb_zip" name="zip" type=text  value='<?php echo $reg_data->zip; ?>' class="pb_field validate[required, custom[onlyNumberSp]]" data-prompt-position="bottomLeft" />
                                    </div>
                                </fieldset>

                                <div class='spacer'></div>

                                <fieldset class="pb_reg_form" id=pb_Attend_Info>
                                    <legend><span class="memb_legend">Attendees</span></legend>
                                    <div id="pb_attendee_count">
                                        <input class="pb_field pb_attendeeCount" id=pb_attendee_count_1 type="radio" name="attendee_count" value="1" <?php if ( $reg_data->quantity == 1){ echo "checked";} ?> />
                                        <label class="pb_attendeeCount" for=pb_attendee_count_1>1 Attendee $<?php echo $pb_cost; ?></label>
                                        <input class="pb_field pb_attendeeCount" data-prompt-position="bottomLeft" id=pb_attendee_count_2 type="radio" name="attendee_count" value="2" <?php if ( $reg_data->quantity == 2){ echo "checked";} ?>/>
                                        <label class="pb_attendeeCount" for=pb_attendee_count_2>2 Attendees $<?php echo $pb_cost * 2; ?></label>
                                        <input class="pb_field pb_attendeeCount" data-prompt-position="bottomLeft" id=pb_attendee_count_3 type="radio" name="attendee_count" value="3" <?php if ( $reg_data->quantity == 3){ echo "checked";} ?>/>
                                        <label class="pb_attendeeCount" for=pb_attendee_count_3>3 Attendees $<?php echo $pb_cost * 3; ?></label>
                                        <input class="pb_field pb_attendeeCount" data-prompt-position="bottomLeft" id=pb_attendee_count_4 type="radio" name="attendee_count" value="4" <?php if ( $reg_data->quantity == 4){ echo "checked";} ?>/>
                                        <label class="pb_attendeeCount" for=pb_attendee_count_4>4 Attendees $<?php echo $pb_cost * 4; ?></label>
                                    </div>

                                    <div class='pb_reg_attendee' id='pb_attendee_1'>
                                        <?php if ( isset( $reg_data->attendee_1 ) ) {
                                            $names = preg_split('/\s+/', $reg_data->attendee_1 ); ?>
                                            <label class="pb_lbl_left"" id=pb_attendee_fname for=pb_attendee_fname_1>First Name:</label>
                                            <input id=pb_attendee_fname_1 name=pb_attendee_fname_1 type=text class="validate[required, custom[onlyLetterSp]] pb_lbl_left" data-prompt-position="bottomLeft" value="<?php  echo $names[0];?>"/>
                                            <label class="pb_field_col_2"" id=pb_attendee_lname for=pb_attendee_lname_1>Last Name:</label>
                                            <input id=pb_attendee_lname_1 name=pb_attendee_lname_1 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" value="<?php echo $names[1];?>"/>
                                        <?php } else { ?>
                                            <label class="pb_lbl_left"" id=pb_attendee_fname_1 for=pb_attendee_fname_2>First Name:</label>
                                            <input id=pb_attendee_fname_1 name=pb_attendee_fname_1 type=text class="validate[required, custom[onlyLetterSp]] pb_lbl_left" data-prompt-position="bottomLeft" />
                                            <label class="pb_field_col_2"" id=pb_attendee_lname_1 for=pb_attendee_lname_2>Last Name:</label>
                                            <input id=pb_attendee_lname_1 name=pb_attendee_lname_1 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" />
                                        <?php } ?>
                                    </div>
                                    <div class='pb_reg_attendee' id='pb_attendee_2'>
                                        <?php if ( isset( $reg_data->attendee_2 ) ) {
                                            $names = preg_split('/\s+/', $reg_data->attendee_2 ); ?>
                                            <label class="pb_lbl_left"" id=pb_attendee_fname_2 for=pb_attendee_fname_2>First Name:</label>
                                            <input id=pb_attendee_fname_2 name=pb_attendee_fname_2 type=text class="validate[required, custom[onlyLetterSp]] pb_lbl_left" data-prompt-position="bottomLeft" value="<?php  echo $names[0];?>"/>
                                            <label class="pb_field_col_2"" id=pb_attendee_lname_2 for=pb_attendee_lname_2>Last Name:</label>
                                            <input id=pb_attendee_lname_2 name=pb_attendee_lname_2 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" value="<?php echo $names[1];?>"/>
                                        <?php } else { ?>
                                            <label class="pb_lbl_left"" id=pb_attendee_fname_2 for=pb_attendee_fname_2>First Name:</label>
                                            <input id=pb_attendee_fname_2 name=pb_attendee_fname_2 type=text class="validate[required, custom[onlyLetterSp]] pb_lbl_left" data-prompt-position="bottomLeft" />
                                            <label class="pb_field_col_2"" id=pb_attendee_lname_2 for=pb_attendee_lname_2>Last Name:</label>
                                            <input id=pb_attendee_lname_2 name=pb_attendee_lname_2 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" />
                                        <?php } ?>
                                    </div>
                                    <div class='pb_reg_attendee' id='pb_attendee_3'>
                                        <?php if ( isset( $reg_data->attendee_3 ) ) {
                                            $names = preg_split('/\s+/', $reg_data->attendee_3 ); ?>
                                            <label class="pb_lbl_left"" id=pb_attendee_fname_3 for=pb_attendee_fname_3>First Name:</label>
                                            <input id=pb_attendee_fname_3 name=pb_attendee_fname_3 type=text class="validate[required, custom[onlyLetterSp]] pb_lbl_left" data-prompt-position="bottomLeft" value="<?php  echo $names[0];?>"/>
                                            <label class="pb_field_col_2"" id=pb_attendee_lname_3 for=pb_attendee_lname_3>Last Name:</label>
                                            <input id=pb_attendee_lname_3 name=pb_attendee_lname_3 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" value="<?php echo $names[1];?>"/>
                                        <?php } else { ?>
                                            <label class="pb_lbl_left"" id=pb_attendee_fname_3 for=pb_attendee_fname_3>First Name:</label>
                                            <input id=pb_attendee_fname_3 name=pb_attendee_fname_3 type=text class="validate[required, custom[onlyLetterSp]] pb_lbl_left" data-prompt-position="bottomLeft" />
                                            <label class="pb_field_col_2"" id=pb_attendee_lname_3 for=pb_attendee_lname_3>Last Name:</label>
                                            <input id=pb_attendee_lname_3 name=pb_attendee_lname_3 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" />
                                        <?php } ?>
                                    </div>
                                    <div class='pb_reg_attendee' id='pb_attendee_4'>
                                        <?php if ( isset( $reg_data->attendee_4 ) ) {
                                            $names = preg_split('/\s+/', $reg_data->attendee_4 ); ?>
                                            <label class="pb_lbl_left"" id=pb_attendee_fnam_4e for=pb_attendee_fname_4>First Name:</label>
                                            <input id=pb_attendee_fname_4 name=pb_attendee_fname_4 type=text class="validate[required, custom[onlyLetterSp]] pb_lbl_left" data-prompt-position="bottomLeft" value="<?php  echo $names[0];?>"/>
                                            <label class="pb_field_col_2"" id=pb_attendee_lname_4 for=pb_attendee_lname_4>Last Name:</label>
                                            <input id=pb_attendee_lname_4 name=pb_attendee_lname_4 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" value="<?php echo $names[1];?>"/>
                                        <?php } else { ?>
                                            <label class="pb_lbl_left"" id=pb_attendee_fname_4 for=pb_attendee_fname_4>First Name:</label>
                                            <input id=pb_attendee_fname_4 name=pb_attendee_fname_4 type=text class="validate[required, custom[onlyLetterSp]] pb_lbl_left" data-prompt-position="bottomLeft" />
                                            <label class="pb_field_col_2"" id=pb_attendee_lname_4 for=pb_attendee_lname_4>Last Name:</label>
                                            <input id=pb_attendee_lname_4 name=pb_attendee_lname_4 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" />
                                        <?php } ?>
                                    </div>
                                </fieldset>

                                <div class='spacer'></div>

                                <div>
                                    <!--<input class="PB_Reg_button" id="update" type=submit name="update" value="update" />-->
                                </div>

                            </form>

                            <!-- Pirate's Ball CTXPHC Members Only Registration PayPal Button -->
                            <!--
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="VDW65WDHYXXYJ">
                                <input type="hidden" name="quantity" value="<?php echo $reg_data->quantity;?>"/>
                                <input type="hidden" name="custom" value="<?php echo $pbRegID;?>"/>
                                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>
                            -->

                            <!--  Pirate's Ball Early Registration PayPal Button -->
                            <!--
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="4PH2DEAAH4LD8">
                                <input type="hidden" name="quantity" value="<?php echo $reg_data->quantity;?>"/>
                                <input type="hidden" name="custom" value="<?php echo $pbRegID;?>"/>
                                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>
                            -->

                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="XS2AC299UJ63N">
                                <input type="hidden" name="quantity" value="<?php echo $reg_data->quantity;?>"/>
                                <input type="hidden" name="custom" value="<?php echo $pbRegID;?>"/>
                                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>


                            <!-- Pirate's Ball Late Registration PayPal Button -->
                            <!--
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="DU9MPK4H5L3ZQ">
                                <input type="hidden" name="quantity" value="<?php echo $reg_data->quantity;?>"/>
                                <input type="hidden" name="custom" value="<?php echo $pbRegID;?>"/>
                                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>
                            -->
                        </div>

                        <div class="spacer"></div>

                    </div>
                    <!-- entry -->
                </div> <!-- post -->
            <?php
            endwhile;
            endif;
            ?>
        </div> <!-- content -->
        <?php get_sidebar(); ?>
        <?php get_footer(); ?>
    <?php

    } else { //There was an error inserting the record into the pb_registration table.
        //error_log( "there was a problem with the registration and it needs to be addressed before get here!!!!", 0 );

        /*****************************************************
         * BEGIN: Pirate's Ball Registration ERROR Page
         *****************************************************/
        ?>
        <div id="content">
            <div class="spacer"></div>
            <?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <div id="post_title" class="post_title">
                        <h1><a href="<?php the_permalink() ?>" rel="bookmark"
                               title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                    </div>
                    <!-- Post_title -->

                    <div class="clear"></div>

                    <div class="entry">
                        <?php the_content( 'more...' ); ?>
                        <div class="clear"></div>

                        <div class="pb_header">
                            <h2 class="pieces_of_eight">Central Texas Parrot Head Club</h2>
                            <h2 class="pieces_of_eight">2014 Pirate's Ball</h2>
                            <h2 class="pb_header">Early Registration</h2>
                        </div>

                        <div class="spacer"></div>

                        <div>
                            <img id='PB_logo' alt="CTXPHC Pirate's Ball 2014 Logo" src="<?php echo get_template_directory_uri(); ?>/includes/images/Pirates_Ball/2014Pirates.jpg" width="300" height="189"/>
                        </div>

                        <div class="spacer"></div>

                        <div>
                            <h3>There was a problem with Pirate's Ball Registration!!!!!</h3>

                            <p>You will be contacted by CTXPHC Support within 24 hours.</p>

                            <p>Your registration includes:</p>
                            <ol>
                                <li class="pb_list_icon">2 Drink Tickets</li>
                                <li class="pb_list_icon">Friday: Welcome with Live Music by <a
                                        href="http://www.reverbnation.com/donnybrewer">Donny Brewer</a>.
                                </li>
                                <li class="pb_list_icon">Saturday's Walk The Plank Pool Party includes live music by <a
                                        href="http://www.jerrydiaz.com/">Jerry Diaz</a> and BBQ lunch.
                                </li>
                                <li class="pb_list_icon">Saturday Night's Pirate's Ball includes live music by <a
                                        href="http://www.theperfectparrotheadparty.com/">The Perfect Parrot Head
                                        Party</a>.
                                </li>
                                <li class="pb_list_icon">Sunday</li>
                            </ol>

                            <p><a href="https://www.ctxphc.com/pirates-ball-details/" >Click here for additional event and hotel information!</a></p>
                            <p>We look forward to seeing you and celebrating another wonderful CTXPHC Pirate's Ball!</p>
                        </div>

                        <div class="spacer"></div>

                    </div>
                    <!-- entry -->
                </div> <!-- post -->
            <?php
            endwhile;
            endif;
            ?>
        </div> <!-- content -->
        <?php get_sidebar(); ?>
        <?php get_footer(); ?>
    <?php
    }

} else { //initial load of page.

    /*****************************************************
     * BEGIN: Pirate's Ball Registration Page
     *****************************************************/
    ?>
    <div id="content"><div class="spacer"></div>

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div id="post_title" class="post_title">
                    <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                </div> <!-- Post_title -->
                <div class="clear"></div>
                <div class="entry">
                    <?php the_content('more...'); ?><div class="clear"></div>

                    <div class="spacer"></div>

                    <div class="pb_header">
                        <h2 class="pieces_of_eight">Central Texas Parrot Heads</h2>
                        <h2 class="pieces_of_eight">2014 Pirate's Ball</h2>
                        <h2 class="pb_header">Online Registration is CLOSED!</h2>
                    </div>

                    <div class="spacer"></div>

                    <div>
                        <img id='PB_logo' alt="CTXPHC Pirate's Ball 2014 Logo" src="<?php echo get_template_directory_uri(); ?>/includes/images/Pirates_Ball/2014Pirates.jpg" width="300" height="189" />
                    </div>

                    <div class="spacer"></div>

                    <div class="pb_dates">
                        <h3>This year Pirate's Ball will be</h3>
                        <h3>Aug 8<span class="superscript">th</span>, 9<span class="superscript">th</span> & 10<span class="superscript">th</span></h3>
                        <h3>THAT IS THREE FULL DAYS!</h3>
                    </div>

                    <div class="spacer"></div>

                    <div>
                        <img id='PB_CTXPHC_logo' alt="CTXPHC Logo" src="<?php echo get_template_directory_uri(); ?>/includes/images/Home/HomePage-Image.jpg" width="225" height="230" />
                    </div>

                    <!--
                    <div id="pb_reg_includes">
                        <h3><span class="pb_bold">Your registration will include:</span></h3>
                        <ul>
                            <li><span class="pb_bold">Friday</span>: Welcome Party with Live Music by <a href="http://www.reverbnation.com/donnybrewer" >Donny Brewer</a>.</li>
                            <li><span class="pb_bold">Saturday Afternoon</span>: Walk The Plank Pool Party with live music by <a href="http://www.jerrydiaz.com/">Jerry Diaz</a>.</li>
                            <li><span class="pb_bold">Saturday Night</span>: Pirate's Ball with live music by <a href="http://www.theperfectparrotheadparty.com/">The Perfect Parrot Head Party</a> and two free drink tickets!</li>
                            <li><span class="pb_bold">Sunday</span>: Sunday Funday at the pool!</li>
                        </ul>
                    </div>

                    <div class="spacer"></div>

                    <div>
                        <h4 class="pb_center">CTXPHC Early registration cost: $<?php echo $pb_cost; ?> per person</h4>
                        <p class="pb_center"><a href="https://www.ctxphc.com/pirates-ball-details/" >Click here for additional event and hotel information!</a></p>
                    </div>

                    <div class="spacer"></div>

                    <form class="pb_reg_form" id="pbRegForm" name="regForm" method="post" action="">

                        <fieldset class="pb_reg_form"   id=members_info>
                            <legend><span class="memb_legend">Your Information</span></legend>
                            <div id="personal_info">
                                <label class="pb_field_col_1" id="pb_fname" for="pb_fname">First Name:</label>
                                <input id="pb_fname" type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_1" data-prompt-position="bottomLeft" name="pb_fname" />
                                <label class="pb_field_col_2" id="pb_lname" for="pb_lname">Last Name:</label>
                                <input id="pb_lname" type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" name="pb_lname" />
                            </div>

                            <div id="pb_email">
                                <label class="pb_field_col_1" id="pb_email" for=pb_email>Email:</label>
                                <input id="pb_email" class="pb_field validate[required, custom[email]] pb_field_col_1" data-prompt-position="bottomLeft" name="pb_email" type="text" />
                                <label class="pb_field_col_2" id="pb_email_verify" for=pb_email_verify>Verify Email:</label>
                                <input id="pb_email_verify" class="pb_field_col_2 validate[required, custom[email]]" data-prompt-position="bottomLeft" name="pb_email_verify" type="text" value='<?php echo $reg_data['pb_email_verify']; ?>' />
                            </div>

                            <div id='pb_club_affiliation'>
                                <label id="pb_club_affiliation" for=club_affiliation>PHC Affiliation or None:</label>
                                <input class="pb_field validate[required, custom[ClubAffiliation]]" data-prompt-position="bottomLeft" id="club_affiliation" name="club_affiliation" type="text" value='<?php echo $reg_data['$club_aff']; ?>' />
                            </div>
                        </fieldset>

                        <div class='spacer'></div>

                        <fieldset class="pb_reg_form" id=address>
                            <legend><span class="memb_legend">Address</span></legend>
                            <div>
                                <label id="pb_addr1" for="pb_addr1">Address:</label>
                                <input id="pb_addr1" name="addr1" type="text" value='<?php echo $pbreg_data['addr1']; ?>' class="pb_field validate[required, custom[address]]" data-prompt-position="bottomLeft" />
                                <label id="pb_addr2" for="pb_addr2">Apt/Suite:</label>
                                <input id="pb_addr2" name=addr2 type=text value='<?php echo $pbreg_data['addr2']; ?>' class="pb_field validate[custom[address-2]]" data-prompt-position="bottomLeft" />
                            </div>
                            <div>
                                <label id="pb_city" for="pb_city">City:</label>
                                <input id="pb_city" name="city" type="text" value='<?php echo $pbreg_data['city']; ?>' class="pb_field validate[required, custom[onlyLetterSp]]" data-prompt-position="bottomLeft" />
                                <label id="pb_state" for="pb_state">State:</label>
                                <select id="pb_state" name="state" class="pb_field validate[required]">
                                    <?php echo showOptionsDrop($states_arr, $state, true); ?>
                                </select>
                                <label id="pb_zip" for="pb_zip">Zip:</label>
                                <input id="pb_zip" name="zip" type=text value='<?php echo $pbreg_data['zip']; ?>' class="pb_field validate[required, custom[onlyNumberSp]]" data-prompt-position="bottomLeft" />
                            </div>
                        </fieldset>

                        <div class='spacer'></div>

                        <fieldset class="pb_reg_form"   id=pb_Attend_Info>
                            <legend><span class="memb_legend">Attendees</span></legend>
                            <div id="pb_attendee_count">
                                <input class="pb_attendeeCount" id=pb_attendee_count_1 type="radio" name="attendee_count" value="1" checked />
                                <label class=pb_attendeeCount for=pb_attendee_count_1>1 Attendee $<?php echo $pb_cost; ?></label>
                                <input class="pb_attendeeCount" data-prompt-position="bottomLeft" id=pb_attendee_count_2 type="radio" name="attendee_count" value="2" />
                                <label class=pb_attendeeCount for=pb_attendee_count_2>2 Attendees $<?php echo $pb_cost * 2; ?></label>
                                <input class="pb_attendeeCount" data-prompt-position="bottomLeft" id=pb_attendee_count_3 type="radio" name="attendee_count" value="3" />
                                <label class=pb_attendeeCount for=pb_attendee_count_3>3 Attendees $<?php echo $pb_cost * 3; ?></label>
                                <input class="pb_attendeeCount" data-prompt-position="bottomLeft" id=pb_attendee_count_4 type="radio" name="attendee_count" value="4" />
                                <label class=pb_attendeeCount for=pb_attendee_count_4>4 Attendees: $<?php echo $pb_cost * 4; ?></label>
                            </div>

                            <div class="spacer"></div>

                            <div class='pb_reg_attendees' id='pb_attendee_1'>
                                <label class="pb_field_col_1" id=pb_attendee_fname for=pb_attendee_fname_1>First Name:</label>
                                <input id=pb_attendee_fname_1 name=pb_attendee_fname_1 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_1" data-prompt-position="bottomLeft" />
                                <label class="pb_field_col_2" id=pb_attendee_lname for=pb_attendee_lname_1>Last Name:</label>
                                <input id=pb_attendee_lname_1 name=pb_attendee_lname_1 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" />
                            </div>
                            <div class='pb_reg_attendees' id='pb_attendee_2'>
                                <label class="pb_field_col_1" id=pb_attendee_fname for=pb_attendee_fname_2>First Name:</label>
                                <input id=pb_attendee_fname_2 name=pb_attendee_fname_2 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_1" data-prompt-position="bottomLeft" />
                                <label class="pb_field_col_2" id=pb_attendee_lname for=pb_attendee_lname_2>Last Name:</label>
                                <input id=pb_attendee_lname_2 name=pb_attendee_lname_2 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" />
                            </div>
                            <div class='pb_reg_attendees' id='pb_attendee_3'>
                                <label class="pb_field_col_1" id=pb_attendee_fname for=pb_attendee_fname_3>First Name:</label>
                                <input id=pb_attendee_fname_3 name=pb_attendee_fname_3 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_1" data-prompt-position="bottomLeft" />
                                <label class="pb_field_col_2" id=pb_attendee_lname for=pb_attendee_lname_3>Last Name:</label>
                                <input id=pb_attendee_lname_3 name=pb_attendee_lname_3 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" />
                            </div>
                            <div class='pb_reg_attendees' id='pb_attendee_4'>
                                <label class="pb_field_col_1" id=pb_attendee_fname for=pb_attendee_fname_4>First Name:</label>
                                <input id=pb_attendee_fname_4 name=pb_attendee_fname_4 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_1" data-prompt-position="bottomLeft" />
                                <label class="pb_field_col_2" id=pb_attendee_lname for=pb_attendee_lname_4>Last Name:</label>
                                <input id=pb_attendee_lname_4 name=pb_attendee_lname_4 type=text class="validate[required, custom[onlyLetterSp]] pb_field_col_2" data-prompt-position="bottomLeft" />
                            </div>
                        </fieldset>

                        <div class='spacer'></div>

                        <div>
                            <input class="ctxphc_button3" id="pb_submit" type=submit name="submit" value="submit" />
                        </div>
                    </form>
                    -->

                </div> <!-- entry -->
            </div> <!-- post -->
        <?php
        endwhile;
        endif;
        ?>
    </div> <!-- content -->
    <?php get_sidebar(); ?>
    <?php get_footer();?>

<?php }