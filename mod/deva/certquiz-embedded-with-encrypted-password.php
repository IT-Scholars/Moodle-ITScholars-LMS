<?php
// $Id: certquiz-embedded.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $

/**
 * This page prints a particular instance of deva for the Certification Test.
 * Users are directed to this page from a quiz that is active as a Certification Test.
 *
 * @package mod/deva
 */
 
$encryptedPassword=$_COOKIE["encrypted_password_4_moodle"];
// echo "<br/>\$encryptedPassword=$encryptedPassword";
$embedded 	= true;

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once("locallib.php");

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a = optional_param('a', 0, PARAM_INT);  // deva instance ID
$cm = optional_param('cm', 0, PARAM_INT);  // deva instance ID

if(isAttemptLeftOpen($cm, $USER->id)){
	$isLeftOpen = 1;
}else{
	$isLeftOpen = 0;
}

$resourcetype = 'CERTIFICATE';

if($cm){
    //$examURL = "/mod/quiz/attempt-embedded.php?id=$cm";
    $examURL = "$CFG->wwwroot/mod/quiz/attempt-embedded.php?id=".$cm."&isincert=".$cm;
	
	$cmobj = get_coursemodule_from_id('quiz', $cm);
	$quiz = get_record("quiz", "id", $cmobj->instance);
}

if ($id) {
    if (!$cm = get_coursemodule_from_id('deva', $id)) {
        error('Course Module ID was incorrect');
    }

    if (!$course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (!$deva = get_record('deva', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }
} else if ($a) {
    if (!$deva = get_record('deva', 'id', $a)) {
        error('Course module is incorrect');
    }
    if (!$course = get_record('course', 'id', $deva->course)) {
        error('Course is misconfigured');
    }
    if (!$cm = get_coursemodule_from_instance('deva', $deva->id, $course->id)) {
        error('Course Module ID was incorrect');
    }
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, "deva", "view", "view-embedded.php?id=$cm->id", "$deva->id");

/// Print the page header
$strdevas = get_string('modulenameplural', 'deva');
$strdeva = get_string('modulename', 'deva');

$navlinks = array();
$navlinks[] = array('name' => $strdevas, 'link' => "index-embedded.php?id=$course->id", 'type' => 'activity');
$navlinks[] = array('name' => format_string($deva->name), 'link' => '', 'type' => 'activityinstance');

// $navigation = build_navigation($navlinks);

// print_header_simple(format_string($deva->name), '', $navigation, '', '', true,
//        update_module_button($cm->id, $course->id, $strdeva), navmenu($course, $cm));

$navigation = array('newnav' => true, 'navlinks' => $navigation); // build_navigation($navlinks);
print_header('','',$navigation);

/// Print the main part of the page
//**********************************************************************************************
// DEVA
       
//29.06.2011 - jam
$courseURL = "$CFG->wwwroot/course/view-embedded.php?id=".$course->id;        
// sms: 7/11/2014 Added to support embedded version
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

//require_js($CFG->wwwroot . '/mod/quiz/quiz.js');

?>

<script language="javascript">

var isControlOnTab = false;

var timerControl = null;


</script>


<!--Libraries-->
<link type="text/css" href="jquery-ui/css/redmond-light/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery-ui/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.4.custom.min.js"></script>

<script type='text/javascript' src='../scheduler/fullcalendar/fullcalendar.js'></script>
<!-- 
<script type='text/javascript' src='jquery-ui/dataTables/media/js/jquery.dataTables.min.js'></script>
<script type='text/javascript' src='jquery-ui/dataTables/examples/examples_support/jquery.jeditable.js'></script>
    
<link type="text/css" href="jquery-ui/css/jquery-ui-timepicker.css" rel="stylesheet" />
<script type="text/javascript" src="jquery-ui/js/jquery.ui.timepicker.js"></script>

<link rel='stylesheet' type='text/css' href='jquery-ui/css/jquery.ptTimeSelect.css' />
<script type='text/javascript' src='jquery-ui/js/jquery.ptTimeSelect.js'></script>
 -->
<script type='text/javascript' src='jquery-ui/js/dateFormat.js'></script>

<script type="text/javascript" src="js/dialogboxes-embedded-with-encrypted-password.js"></script>

<!-- <script type='text/javascript' src="http://code.jquery.com/jquery-1.4.4.js"></script>  -->

<!--Our scripts-->
<script type='text/javascript' src='js/vmcontrols.js'></script>
<script type='text/javascript' src='js/vmcObjs.js'></script>
<script type='text/javascript' src='js/message.js'></script>
<script type='text/javascript' src='js/deva-tabs-embedded-with-encrypted-password.js'></script>
<script type='text/javascript' src='js/jquery.countDown.js'></script>
<script type='text/javascript' src='js/jquery.loading.1.6.4.js'></script>


<!--CSS-->
<link rel="stylesheet" type="text/css" href="css/styles.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.loading.1.6.css" />
<!-- <link rel="stylesheet" type="text/css" href="css/devacss/style.css" /> -->

<style type="text/css">

	#box { background:url(progress-bar-back.gif) right center no-repeat; width:200px; height:20px; float:left; }
	#perc { background:url(progress-bar.gif) right center no-repeat; height:20px; }
	#text { font-family:tahoma, arial, sans-serif; font-size:11px; color:#000; float:left; padding:3px 0 0 10px; }
	.ui-progressbar-value { background-image: url(css/images/pbar-ani.gif); }
	
	#toolbar { padding: 15px 6px; }
/*	#timetools { padding: 15px 6px; }*/
	
	.navbar { padding: 0; }
	.navbar .navbutton {
		margin-top: 0px;
	}
/*#vmControlPanel { padding: 20px 0px 10px 10px; background-color: #DFEFFC; width: 99%; margin: 0px 0px 0px 1px; }*/
	
	
/* Aligns the divs in the header navbar  - class added in vmcontrols.js */
	.timer-navbar { position: relative; height: 35px; }
	.timer-breadcrumb { position: absolute; left: 0; padding: 5px 5px 10px 5px; }
	.timer-navbutton { position: absolute; right: 0; padding: 5px 5px 4px 5px; font-size: 14px; }
	
	/*.timer-navbutton-cert { position: absolute; right: 0; padding: 0px 5px 0px 5px; font-size: 14px; border-width: 0px; }*/
	/*.timer-navbutton-cert { position: absolute; bottom: 0; right: 0; padding: 5px 5px 12px 5px; font-size: 16px; }*/
	#timetools button.minustime,
	#timetools button.cancel {
		display: none;   
	}
	
	.timer-red { color:#FF0000; font-weight: bold; }
	#editapptime { width: 20px; height: 20px; margin-right: 5px; }
	
	/*.certificate-nav { display:none; }*/
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


<!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <link rel="stylesheet" href="css/lt-ie9.css" type="text/css" />
<![endif]-->

<!--  <link rel="stylesheet" href="jquery/jquery.tabs.css" type="text/css" media="print, projection, screen">  -->
<!-- Additional IE/Win specific style sheet (Conditional Comments) -->
<!--[if lte IE 7]->
<link rel="stylesheet" href="jquery.tabs-ie.css" type="text/css" media="projection, screen">
<![endif]-->


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
	
	iframe#header {
		background-color:#FF0099;
	}
	

</style>

<script language="javascript">

var iscerttest = true;
var isScheduled = false;

$(document).ready(function() {
	
	//$("#content").addClass('minWidth');
	
	progressDialogBox(true);
	
	isControlOnTab = false;
	//vmc_init();
	
	timerControl = $("#page .navbar .navbutton");
	
	$("#infoMessage").hide();
	$("#errorMessage").hide();
	
});

</script>

<!-- $CFG, $SESSION, $USER, $COURSE, $SITE, $PAGE, $DB and $THEME -->
<input id ="bottomFrameHeightPercentage" type="hidden" value="" />
<input id ="userid" type="hidden" value="<?php echo $USER->id; ?>" />
<input id ="username" type="hidden" value="<?php echo $USER->username; ?>" />
<input id ="encryptedPassword" type="hidden" value="<?php echo $encryptedPassword; ?>" />
<input id ="role" type="hidden" value="<?php echo $role; ?>" />
<input id ="email" type="hidden" value="<?php echo $USER->email; ?>" />
<input id ="url" type="hidden" value="<?php echo curPageURL(); ?>" />
<input id ="course" type="hidden" value="<?php echo $COURSE->fullname; ?>" />
<!-- sms: 7/11/2014 Changed to support embedded version -->
<!-- <input id ="courseURL" type="hidden" value="< ?php echo $courseURL; ? >" /> -->
<input id ="courseURL" type="hidden" value="<?php echo $kupoweredbyitsURL; ?>" />
<input id ="examURL" type="hidden" value="<?php echo $examURL; ?>" />
<input id ="isLeftOpen" type="hidden" value="<?php echo $isLeftOpen; ?>" />
<input id="resourcetype" type="hidden" value="<?php echo $resourcetype; ?>" />
<input id="embedded" 						type="hidden" value="true" />

<style type="text/css">

	A.devaTabs:link { border: 1px solid #DFEFFC; padding: 2px 5px 2px 5px; background: #dfeffc url(jquery-ui/css/redmond/images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #2e6e9e; font-size:80%;}
	A.devaTabs:visited { border: 1px solid #DFEFFC; padding: 2px 5px 2px 5px; background: #dfeffc url(jquery-ui/css/redmond/images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #2e6e9e; font-size:80%;}
	A.devaTabs:active { border: 1px solid #DFEFFC; padding: 2px 5px 2px 5px; background: #dfeffc url(jquery-ui/css/redmond/images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #2e6e9e; font-size:80%;}
	A.devaTabs:hover {border: 1px solid #DFEFFC; padding: 2px 5px 2px 5px; background: #dfeffc url(jquery-ui/css/redmond/images/ui-bg_glass_85_dfeffc_1x400.png) 50% 50% repeat-x; font-weight: bold; color: #2e6e9e; font-size:80%;}
	
	<!-- Added by SMS: 8/8/2011 -->
	A.quizURL { font-weight: normal; font-size:80%; padding-right:5px;}

</style>
	
<!-- style type="text/css">
A.devaTabs:link {
    background: #86B3D5;
    border: 1px solid #C5DBEC;
    color: #2E6E9E;
    font-weight: bold;
}
A.devaTabs:visited {
    background: #86B3D5;
    border: 1px solid #C5DBEC;
    color: #2E6E9E;
    font-weight: bold;
}
A.devaTabs:active {
    background: #86B3D5;
    border: 1px solid #C5DBEC;
    color: #DFEFFC;
    font-weight: bold;
}
A.devaTabs:hover {
    background: #D0E5F5;
    border: 1px solid #79B7E7;
    color: #1D5987;
    font-weight: bold;
}
</style -->	
<div id="vmcDebug2"></div>
<div id="vmcDebug"></div>

<div id="wrapper">
    <div id="message" title="System message"></div>
    <div id="createitem-form" class="form" title="Item Details"></div>
    <div id="createpackage-form" class="form" title="Package Information"></div>
    <div id="additemtopkg-form" class="form"  title="Package Item details"></div>
    <div id="tabs-wrapper" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header">
    <center><div id="devaTabs"></center></div></div>
    <div id="devaTabContent"></div>
    <div id="examContent"></div>
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

<!--
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-21025813-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
-->
</body>

<?php
//**********************************************************************************************
/// Finish the page
// print_footer($course);


?>
