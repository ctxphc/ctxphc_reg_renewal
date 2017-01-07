/**
 * Created by ken_kilgore1 on 3/5/2015.
 */


var $j = jQuery.noConflict();


$j(document).ready(function () {
    "use strict";
    var $formID = $j('#createmember');
    $j($formID).validate({
        rules: {
            mb_first_name: {
                required: true,
                minlength: 2
            },
            mb_last_name: {
                required: true,
                minlength: 2
            },
            mb_birthday: {
                required: true,
                date: true
            },
            mb_occupation: "required"
        },
        messages: {
            mb_first_name: {
                required: "Please enter your first name.",
                minlength: "Must be at least 2 characters long"
            },
            mb_last_name: {
                required: "Please enter your last name.",
                minlength: "Must be at least 2 characters long."
            }
        }
    });
});


$j(document).ready(function () {
    "use strict";
    var $j = jQuery.noConflict();
    $j("#cm_spouse_spacer").hide();
    $j("#cm-spouse").hide();
    $j("#cm_family_spacer").hide();
    $j("#cm-family").hide();
    $j("form input[name='memb_type'][type='radio']").click(function () {

        if ($j("#memb_type_1:checked[type='radio']").val() === "1") {
            $j("#cm_spouse_spacer").hide();
            $j("#cm-spouse").hide();
            $j("#cm_family_spacer").hide();
            $j("#cm-family").hide();
        } else if ($j("#memb_type_2:checked[type='radio']").val() === "2") {
            $j("#cm_spouse_spacer").hide();
            $j("#cm-spouse").hide();
            $j("#cm_family_spacer").show();
            $j("#cm-family").show();
        } else if ($j("#memb_type_3:checked[type='radio']").val() === "3") {
            $j("#cm_spouse_spacer").show();
            $j("#cm-spouse").show();
            $j("#cm_family_spacer").hide();
            $j("#cm-family").hide();
        } else if ($j("#memb_type_4:checked[type='radio']").val() === "4") {
            $j("#cm_spouse_spacer").show();
            $j("#cm-spouse").show();
            $j("#cm_family_spacer").show();
            $j("#cm-family").show();
        }
    });
});
