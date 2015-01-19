<?php
// $Id: view-embedded.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $

/**
 * This page prints a particular instance of deva
 *
 * @author Masoud Sadjadi
 * @version $Id: view-embedded.php,v 1.0.0.0 2014/06/28 
 * @package mod/deva/embedded
 */
	// setcookie('encrypted_login_4_moodle', $_GET["encrypted_login"], 0, '/', 'localhost', false, false);
	// setcookie('plaintext_login_4_moodle', $_GET["plaintext_login"], 0, '/'); // , 'localhost', false, false);
	setcookie('encrypted_password_4_moodle', $_GET["encrypted_password"], 0, '/'); // , 'localhost', false, false);	

	require_once('../../config.php');
    require_once($CFG->libdir.'/gdlib.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/user/editadvanced_form.php');
    require_once($CFG->dirroot.'/user/editlib.php');
    require_once($CFG->dirroot.'/user/profile/lib.php');
	
	require_once($CFG->dirroot.'/mod/scheduler/fullcalendar/calendar.php');
	require_once($CFG->dirroot.'/mod/scheduler/fullcalendar/quotasystem.php');

	$id 		= $_GET["id"]; // id
	$username 	= $_GET["username"]; // username for embedded verions
	$hours 		= $_GET["hours"]; // hours for embedded verions
	$minutes 	= $_GET["minutes"]; // minutes for embedded verions
	$embedded 	= true;

	// echo "id is $id and username is $username";

	if (!$user = get_record('user', 'username', $username)) {
		error("A user with the username $username does NOT exist in vLab.");
		exit();
	}

	if (!isloggedin() || ($USER->username != $username)) {
		$user = get_record('user', 'username', $username);
		// Trying to login as this user
		// Create the new USER object with all details and reload needed capabilitites
    	$USER = get_complete_user_data('id', $user->id);
   		check_enrolment_plugins($USER);
		load_all_capabilities();   // reload capabilities
	}

	if ($id) {
    	if (!$cm = get_coursemodule_from_id('deva', $id)) {
        	error('Course Module ID was incorrect');
			exit();
    	}

    	if (!$course = get_record('course', 'id', $cm->course)) {
        	error('Course is misconfigured');
			exit();
	    }

    	if (!$deva = get_record('deva', 'id', $cm->instance)) {
        	error('Course module is incorrect');
			exit();
	    }
	} else {
		error('You must specify a course_module ID');
		exit();
	}

	require_login($course, true, $cm);

	add_to_log($course->id, "deva", "view", "embedded/view-embedded.php?id=$cm->id", "$deva->id");

	/// Print the page header
	$strdevas = get_string('modulenameplural', 'deva');
	$strdeva = get_string('modulename', 'deva');

	$navlinks = array();
	$navlinks[] = array('name' => $strdevas, 'link' => "index.php?id=$course->id", 'type' => 'activity');
	$navlinks[] = array('name' => format_string($deva->name), 'link' => '', 'type' => 'activityinstance');

	if ($embedded) {
		$navigation = array('newnav' => true, 'navlinks' => $navigation); // build_navigation($navlinks);
		print_header('','',$navigation);
	} else {
		$navigation = build_navigation($navlinks);
		print_header_simple(format_string($deva->name), '', $navigation, '', '', true,
			update_module_button($cm->id, $course->id, $strdeva), navmenu($course, $cm));
	}

	/// Print the main part of the page
	//**********************************************************************************************
	// DEVA
       
	//29.06.2011 - jam
	$courseURL = "$CFG->wwwroot/course/view-embedded.php?id=".$course->id;         
	// sms: 6/29/2014 Added to support embedded version
	$kupoweredbyitsURL = "embedded/KU-poweredby-ITS.html";

	//Role
	$sql = "SELECT * FROM {$CFG->prefix}role_assignments WHERE userid =" . $USER->id;
	$role_assignment = get_record_sql($sql);
	$sql2 = "SELECT * FROM {$CFG->prefix}role WHERE id = " . $role_assignment->roleid;
	$role_obj = get_record_sql($sql2);
	$role = $role_obj->shortname;

	function curPageURL() {
    	$pageURL = 'http';
    	if ($_SERVER["HTTPS"] == "on") {
        	$pageURL .= "s";
    	}
    	$pageURL .= "://";
    	if ($_SERVER["SERVER_PORT"] != "80") {
        	$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    	} else {
        	$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    	}
    	return $pageURL;
	}
?>

<script language="javascript">
	var isControlOnTab = false;
</script>

<!--Libraries-->
<link type="text/css" href="jquery-ui/css/redmond-light/jquery-ui-1.8.5.custom.css" rel="stylesheet" />

<script type="text/javascript" src="jquery-ui/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.4.custom.min.js"></script>
<script type='text/javascript' src='../scheduler/fullcalendar/fullcalendar.js'></script>
<script type='text/javascript' src='jquery-ui/js/dateFormat.js'></script>
<!-- sms: 6/28/2014 Changed to support embedded version -->
<!--<script type="text/javascript" src="js/dialogboxes.js"></script>-->
<script type="text/javascript" src="js/dialogboxes-embedded.js"></script>

<!--Our scripts-->
<<<<<<< HEAD
<!-- Masoud Sadjadi (SMS): Jan. 27, 2015 
Added the embedded version to make sure the appointment is not 
automatically renewed for the embedded version, if deva page is 
on when the time goes out! -->
=======
>>>>>>> Avoiding automatic vLab schedule renewal.
<script type='text/javascript' src='js/vmcontrols-embedded.js'></script>
<script type='text/javascript' src='js/vmcObjs.js'></script>
<script type='text/javascript' src='js/message.js'></script>
<!-- sms: 6/28/2014 Changed to support embedded version -->
<!-- <script type='text/javascript' src='js/deva-tabs.js'></script> -->
<script type='text/javascript' src='js/deva-tabs-embedded.js'></script>

<script type='text/javascript' src='js/jquery.countDown.js'></script>
<script type='text/javascript' src='js/jquery.loading.1.6.4.js'></script>

<!--CSS-->
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.loading.1.6.css" />

<style type="text/css">
	#box { background:url(progress-bar-back.gif) right center no-repeat; width:200px; height:20px; float:left; }
	#perc { background:url(progress-bar.gif) right center no-repeat; height:20px; }
	#text { font-family:tahoma, arial, sans-serif; font-size:11px; color:#000; float:left; padding:3px 0 0 10px; }
	.ui-progressbar-value { background-image: url(css/images/pbar-ani.gif); }	
	#toolbar { padding: 15px 6px; }
	
	/* Aligns the divs in the header navbar  - class added in vmcontrols.js */
	.navbar { padding: 0; }
	#content { margin-top: -5px; }

	.timer-navbar { position: relative; height: 35px; }
	.timer-breadcrumb { padding: 8px 0 10px 5px; min-width: 400px; }
	.timer-navbutton { padding: 5px 5px 4px 5px; font-size: 14px; }
	.timer-navbutton-cert { padding: 5px 5px 12px 5px; font-size: 16px; }
	.timer-red { color:#FF0000; font-weight: bold; }
	#editapptime { width: 18px; height: 18px; margin-right: 5px; }
	
	.certificate-nav { display:none; }
	.minWidth { min-width: 900px; }
</style>

<style type="text/css" media="screen">
	@import "jquery-ui/dataTables/media/css/demo_table_jui.css";
	
	/*
	 * Override styles needed due to the mix of three different CSS sources! For proper examples
	 * please see the themes example in the 'Examples' section of this site
	 */
	.dataTables_info { padding-top: 0; }
	.dataTables_paginate { padding-top: 0; }
	.css_right { float: right; }
	#example_wrapper .fg-toolbar { font-size: 0.8em }
	#theme_links span { float: left; padding: 2px 10px; }
</style>

<style type="text/css">
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    .ui-widget {
        font-family:Lucida Grande,Lucida Sans,Arial,sans-serif;
        font-size:0.8em;
    }
</style>

<script language="javascript">
	var iscerttest = false;
	$(document).ready(function() {
		isControlOnTab = false;
		//vmc_init();
	
		$("#infoMessage").hide();
		$("#errorMessage").hide();
	});
</script>

<input id ="bottomFrameHeightPercentage" 	type="hidden" value="" />
<input id ="userid" 						type="hidden" value="<?php echo $USER->id; ?>" />
<input id ="username" 						type="hidden" value="<?php echo $USER->username; ?>" />
<input id ="encryptedPassword"				type="hidden" value="<?php echo $_GET["encrypted_password"]; ?>" />
<input id ="role" 							type="hidden" value="<?php echo $role; ?>" />
<input id ="email" 							type="hidden" value="<?php echo $USER->email; ?>" />
<input id ="url" 							type="hidden" value="<?php echo curPageURL(); ?>" />
<!-- sms: 6/28/2014 Changed to support embedded version -->
<!-- <input id ="courseURL" 				type="hidden" value="< ?php echo $courseURL; ?>" /> -->
<input id ="courseURL" 						type="hidden" value="<?php 	if ($embedded) {
																			echo $kupoweredbyitsURL;
																		} else {
																			echo $courseURL; 
																		} 
																	?>" />
<input id ="course" 						type="hidden" value="<?php echo $COURSE->fullname; ?>" />
<input id="resourcetype" 					type="hidden" value="<?php echo $resourcetype = 'VIRTUAL LAB'; ?>" />
<!-- sms: 6/28/2014 Added to support embedded version -->
<input id="hours" 							type="hidden" value="<?php echo $hours; ?>" />
<input id="minutes" 						type="hidden" value="<?php echo $minutes; ?>" />
<input id="embedded" 						type="hidden" value="true" />

<style type="text/css">
	A.devaTabs:link { border: 1px solid #DFEFFC; padding: 2px 5px 2px 5px; background: #dfeffc url(jquery-ui/css/redmond/images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #2e6e9e; font-size:80%;}
	A.devaTabs:visited { border: 1px solid #DFEFFC; padding: 2px 5px 2px 5px; background: #dfeffc url(jquery-ui/css/redmond/images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #2e6e9e; font-size:80%;}
	A.devaTabs:active { border: 1px solid #DFEFFC; padding: 2px 5px 2px 5px; background: #dfeffc url(jquery-ui/css/redmond/images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #2e6e9e; font-size:80%;}
	A.devaTabs:hover {border: 1px solid #DFEFFC; padding: 2px 5px 2px 5px; background: #dfeffc url(jquery-ui/css/redmond/images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #2e6e9e; font-size:80%;}
</style>
	
<div id="vmcDebug2"></div>
<div id="vmcDebug"></div>
<div id="wrapper">
    <div id="message" 								title="System message"></div>
    <div id="createitem-form" 		class="form" 	title="Item Details"></div>
    <div id="createpackage-form" 	class="form" 	title="Package Information"></div>
    <div id="additemtopkg-form" 	class="form"  	title="Package Item details"></div>
    <div id="tabs-wrapper" 			class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header">
    	<center><div id="devaTabs"></div></center>
    </div>
    <div id="devaTabContent"></div>
    <div id="dialog"></div>
    <div id="progressbarContainer" tabindex="-1">
		<center><div id="progressbar"></div></center>
    </div>
    <div id="progess-overlay" style="z-index: 5000; position: absolute;"></div>
</div>	

<center>
	<div id="infoMessage" class="ui-state-highlight ui-corner-bottom infoMessageBox"> 
    	<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
    		<span id="infoText"></span>
    	</p>
    	<span class="buttonPanel"></span>
	</div>
	<div id="errorMessage" class="ui-state-error ui-corner-bottom errorMessageBox"> 
    	<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
    		<span id="errorText"></span>
    	</p>
	</div>
</center>

<style type="text/css">
	#tabs-wrapper { padding: 5px 5px 5px 5px; }
</style>

</body>

<?php
//**********************************************************************************************
/// Finish the page
// print_footer($course);
?>
