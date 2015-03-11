<?php  // $Id: attempt.php,v 1.131.2.16 2009/03/23 01:59:18 tjhunt Exp $
/**
 * This page prints a particular instance of quiz
 *
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once("../../config.php");
    require_once("locallib.php");
	
	// 29.06.2011 - jam
	///require_once($CFG->dirroot.'/scheduler/fullcalendar/calendar.php');

    // remember the current time as the time any responses were submitted
    // (so as to make sure students don't get penalized for slow processing on this page)
    $timestamp = time();

    // Get submitted parameters.
    $id = optional_param('id', 0, PARAM_INT);               // Course Module ID
    $q = optional_param('q', 0, PARAM_INT);                 // or quiz ID
    $page = optional_param('page', 0, PARAM_INT);
    $questionids = optional_param('questionids', '');
    $finishattempt = optional_param('finishattempt', 0, PARAM_BOOL);
    $timeup = optional_param('timeup', 0, PARAM_BOOL); // True if form was submitted by timer.
    $forcenew = optional_param('forcenew', false, PARAM_BOOL); // Teacher has requested new preview

    $qcaid = optional_param('qcaid', 0, PARAM_INT); // To ensure the correct QCA record is being updated.
    $isincert = optional_param('isincert', 0, PARAM_BOOL);	// 24.06.2011 - jam

	// SMS: 7/22/2014 Changed to support embedded version
	$isinnewwindow = optional_param('isinnewwindow', 0, PARAM_BOOL);

	// 24.06.2011 - jam
    if(!$isincert && !$finishattempt){
	
		if($id){
			if(isCertificateTest($id)){
			
				if(courseHasVirtualLabs($id)){
					$newcmid = getFirstVirtualLabId($id);
					//$cm = get_record('course_modules','id',$id);
					if(!$q){
						redirect($CFG->wwwroot . '/mod/deva/certquiz-embedded-with-encrypted-password.php?id='.$newcmid.'&cm='.$id);
					}
				}
			}
		}
    }

    if ($id) {
        if (! $cm = get_coursemodule_from_id('quiz', $id)) {
            error("There is no coursemodule with id $id");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("The quiz with id $cm->instance corresponding to this coursemodule $id is missing");
        }
    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("There is no quiz with id $q");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("The course with id $quiz->course that the quiz with id $q belongs to is missing");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("The course module for the quiz with id $q is missing");
        }
    }

    // We treat automatically closed attempts just like normally closed attempts
    if ($timeup) {
        $finishattempt = 1;
    }

    require_login($course->id, false, $cm);

    $coursecontext = get_context_instance(CONTEXT_COURSE, $cm->course); // course context
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $ispreviewing = has_capability('mod/quiz:preview', $context);

    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and has_capability('mod/quiz:manage', $context)) {
        redirect($CFG->wwwroot . '/mod/quiz/edit.php?cmid=' . $cm->id);
    }

    if (!$ispreviewing) {
        require_capability('mod/quiz:attempt', $context);
    }

/// Get number for the next or unfinished attempt
    if(!$attemptnumber = (int)get_field_sql('SELECT MAX(attempt)+1 FROM ' .
            "{$CFG->prefix}quiz_attempts WHERE quiz = '{$quiz->id}' AND " .
            "userid = '{$USER->id}' AND timefinish > 0 AND preview != 1")) {
        $attemptnumber = 1;
    }

    $strattemptnum = get_string('attempt', 'quiz', $attemptnumber);
    $strquizzes = get_string("modulenameplural", "quiz");
    $popup = $quiz->popup && !$ispreviewing; // Controls whether this is shown in a javascript-protected window.

/// We intentionally do not check open and close times here. Instead we do it lower down.
/// This is to deal with what happens when someone submits close to the exact moment when the quiz closes.

/// Check number of attempts
    $numberofpreviousattempts = count_records_select('quiz_attempts', "quiz = '{$quiz->id}' AND " .
        "userid = '{$USER->id}' AND timefinish > 0 AND preview != 1");
    if (!empty($quiz->attempts) and $numberofpreviousattempts >= $quiz->attempts) {
        print_error('nomoreattempts', 'quiz', "view-embedded.php?id={$cm->id}");
    }

/// Check subnet access
    if (!$ispreviewing && !empty($quiz->subnet) && !address_in_subnet(getremoteaddr(), $quiz->subnet)) {
        print_error("subneterror", "quiz", "view-embedded.php?id=$cm->id");
    }

/// Check password access
    if ($ispreviewing && $forcenew) {
        unset($SESSION->passwordcheckedquizzes[$quiz->id]);
    }

    if (!empty($quiz->password) and empty($SESSION->passwordcheckedquizzes[$quiz->id])) {
        $enteredpassword = optional_param('quizpassword', '', PARAM_RAW);
        if (optional_param('cancelpassword', false)) {
            // User clicked cancel in the password form.
            redirect($CFG->wwwroot . '/mod/quiz/view-embedded.php?q=' . $quiz->id);
        } else if (strcmp($quiz->password, $enteredpassword) === 0) {
            // User entered the correct password.
            $SESSION->passwordcheckedquizzes[$quiz->id] = true;
        } else {
            // User entered the wrong password, or has not entered one yet.
            $url = $CFG->wwwroot . '/mod/quiz/attempt-embedded.php?q=' . $quiz->id;

            if (empty($popup)) {
                print_header('', '', '', 'quizpassword');
            }

            if (trim(strip_tags($quiz->intro))) {
                $formatoptions->noclean = true;
                print_box(format_text($quiz->intro, FORMAT_MOODLE, $formatoptions), 'generalbox', 'intro');
            }
            print_box_start('generalbox', 'passwordbox');
            if (!empty($enteredpassword)) {
                echo '<p class="notifyproblem">', get_string('passworderror', 'quiz'), '</p>';
            }
?>
<p><?php print_string('requirepasswordmessage', 'quiz'); ?></p>
<form id="passwordform" method="post" action="<?php echo $url; ?>" onclick="this.autocomplete='off'">
    <div>
         <label for="quizpassword"><?php print_string('password'); ?></label>
         <input name="quizpassword" id="quizpassword" type="password" value=""/>
         <input type="submit" value="<?php print_string('ok'); ?>" />
         <input type="submit" name="cancelpassword" value="<?php print_string('cancel'); ?>" />
    </div>
</form>
<?php
            print_box_end();
            if (empty($popup)) {
				print_footer();
            }
            exit;
        }
    }

    if (!empty($quiz->delay1) or !empty($quiz->delay2)) {
        //quiz enforced time delay
        if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
            $numattempts = count($attempts);
        } else {
            $numattempts = 0;
        }
        $timenow = time();
        $lastattempt_obj = get_record_select('quiz_attempts', "quiz = $quiz->id AND attempt = $numattempts AND userid = $USER->id", 'timefinish');
        if ($lastattempt_obj) {
            $lastattempt = $lastattempt_obj->timefinish;
        }
        if ($numattempts == 1 && !empty($quiz->delay1)) {
            if ($timenow - $quiz->delay1 < $lastattempt) {
                print_error('timedelay', 'quiz', 'view-embedded.php?q='.$quiz->id);
            }
        } else if($numattempts > 1 && !empty($quiz->delay2)) {
            if ($timenow - $quiz->delay2 < $lastattempt) {
                print_error('timedelay', 'quiz', 'view-embedded.php?q='.$quiz->id);
            }
        }
    }

/// Load attempt or create a new attempt if there is no unfinished one

    if ($ispreviewing and $forcenew) { // teacher wants a new preview
        // so we set a finish time on the current attempt (if any).
        // It will then automatically be deleted below
        set_field('quiz_attempts', 'timefinish', $timestamp, 'quiz', $quiz->id, 'userid', $USER->id);
    }

    $attempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id);

/// Added: Custom Exams
    //$qca = get_record('quiz_course_activation','attemptid',$attempt->id);
    $qca = get_record('quiz_course_activation','id',$qcaid,'attemptid',$attempt->id);

    $exam_length = $quiz->timelimit;
    if($exam_length == 0){
        $exam_length = 30;
    }

    if (!empty($qca)) {
        $starttime = $qca->starttime + ($exam_length * 60);
        //$currentTime = date("U",mktime(1,27,0,9,13,2009));
        $currentTime = time();
        //if($currentTime >= $starttime && $currentTime <= $endtime){
        if($currentTime >= $starttime){
        //if(($currentTime - $starttime) >= 0){
            $finishattempt = 1;
        }
    }

    $newattempt = false;
    if (!$attempt) {
        // Delete any previous preview attempts belonging to this user.
        if ($oldattempts = get_records_select('quiz_attempts', "quiz = '$quiz->id'
                AND userid = '$USER->id' AND preview = 1")) {
            foreach ($oldattempts as $oldattempt) {
                quiz_delete_attempt($oldattempt, $quiz);
            }
        }
        $newattempt = true;
        // Start a new attempt and initialize the question sessions
        $attempt = quiz_create_attempt($quiz, $attemptnumber);
        // If this is an attempt by a teacher mark it as a preview
        if ($ispreviewing) {
            $attempt->preview = 1;
        }
        // Save the attempt
        if (!$attempt->id = insert_record('quiz_attempts', $attempt)) {
            error('Could not create new attempt');
        }
        // make log entries
        if ($ispreviewing) {
            add_to_log($course->id, 'quiz', 'preview',
                           "attempt-embedded.php?id=$cm->id",
                           "$quiz->id", $cm->id);
        } else {
            add_to_log($course->id, 'quiz', 'attempt',
                           "review-embedded.php?attempt=$attempt->id",
                           "$quiz->id", $cm->id);
        }
    } else {
         add_to_log($course->id, 'quiz', 'continue attemp', // this action used to be called 'continue attempt' but the database field has only 15 characters
                       'review-embedded.php?attempt=' . $attempt->id, $quiz->id, $cm->id);
    }
    if (!$attempt->timestart) { // shouldn't really happen, just for robustness
        debugging('timestart was not set for this attempt. That should be impossible.', DEBUG_DEVELOPER);
        $attempt->timestart = $timestamp - 1;
    }

/// Update QCA Object -- New
//$forcenew,$finishattempt,$timeup
if(!empty($forcenew) && !empty($qcaid)){
    //$record = get_record('quiz_course_activation','username',$USER->username,'attemptid',NULL);
    $record = get_record('quiz_course_activation','id',$qcaid);
    if(!empty($record)){
        $record->attemptid = $attempt->id;
        $record->status = "Concepts";   // Update exam status

        update_record('quiz_course_activation',$record);
    }
}
/// Load all the questions and states needed by this script

    // list of questions needed by page
    $pagelist = quiz_questions_on_page($attempt->layout, $page);
    
    if ($newattempt) {
        $questionlist = quiz_questions_in_quiz($attempt->layout);
    } else {
        $questionlist = $pagelist;
    }

    // add all questions that are on the submitted form
    if ($questionids) {
        $questionlist .= ','.$questionids;
    }

    if (!$questionlist) {
        print_error('noquestionsfound', 'quiz', 'view-embedded.php?q='.$quiz->id);
    }

    $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
           "  FROM {$CFG->prefix}question q,".
           "       {$CFG->prefix}quiz_question_instances i".
           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
           "   AND q.id IN ($questionlist)";

    // Load the questions
    if (!$questions = get_records_sql($sql)) {
        print_error('noquestionsfound', 'quiz', 'view-embedded.php?q='.$quiz->id);
    }

    // Load the question type specific information
    if (!get_question_options($questions)) {
        error('Could not load question options');
    }

    // If the new attempt is to be based on a previous attempt find its id
    $lastattemptid = false;
    if ($newattempt and $attempt->attempt > 1 and $quiz->attemptonlast and !$attempt->preview) {
        // Find the previous attempt
        if (!$lastattemptid = get_field('quiz_attempts', 'uniqueid', 'quiz', $attempt->quiz, 'userid', $attempt->userid, 'attempt', $attempt->attempt-1)) {
            error('Could not find previous attempt to build on');
        }
    }

    // Restore the question sessions to their most recent states
    // creating new sessions where required
    if (!$states = get_question_states($questions, $quiz, $attempt, $lastattemptid)) {
        error('Could not restore question sessions');
    }

    // Save all the newly created states
    if ($newattempt) {
        foreach ($questions as $i => $question) {
            save_question_session($questions[$i], $states[$i]);
        }
    }

/// Process form data /////////////////////////////////////////////////

    if ($responses = data_submitted() and empty($responses->quizpassword)) {

        // set the default event. This can be overruled by individual buttons.
        $event = (array_key_exists('markall', $responses)) ? QUESTION_EVENTSUBMIT :
         ($finishattempt ? QUESTION_EVENTCLOSE : QUESTION_EVENTSAVE);

        // Unset any variables we know are not responses
        unset($responses->id);
        unset($responses->q);
        unset($responses->oldpage);
        unset($responses->newpage);
        unset($responses->review);
        unset($responses->questionids);
        unset($responses->saveattempt); // responses get saved anway
        unset($responses->finishattempt); // same as $finishattempt
        unset($responses->markall);
        unset($responses->forcenewattempt);

        // extract responses
        // $actions is an array indexed by the questions ids
        $actions = question_extract_responses($questions, $responses, $event);

        // Process each question in turn

        $questionidarray = explode(',', $questionids);
        $success = true;
        foreach($questionidarray as $i) {
            if (!isset($actions[$i])) {
                $actions[$i]->responses = array('' => '');
                $actions[$i]->event = QUESTION_EVENTOPEN;
            }
            $actions[$i]->timestamp = $timestamp;
            if (question_process_responses($questions[$i], $states[$i], $actions[$i], $quiz, $attempt)) {
                save_question_session($questions[$i], $states[$i]);
            } else {
                $success = false;
            }
        }

        if (!$success) {
            $pagebit = '';
            if ($page) {
                $pagebit = '&amp;page=' . $page;
            }
            print_error('errorprocessingresponses', 'question',
                    $CFG->wwwroot . '/mod/quiz/attempt-embedded.php?q=' . $quiz->id . $pagebit);
        }

        $attempt->timemodified = $timestamp;

    // We have now finished processing form data
    }


/// Finish attempt if requested
    if ($finishattempt) {
	
        // Set the attempt to be finished
        $attempt->timefinish = $timestamp;

        // load all the questions
        $closequestionlist = quiz_questions_in_quiz($attempt->layout);
        $sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
               "  FROM {$CFG->prefix}question q,".
               "       {$CFG->prefix}quiz_question_instances i".
               " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
               "   AND q.id IN ($closequestionlist)";
        if (!$closequestions = get_records_sql($sql)) {
            error('Questions missing');
        }

        // Load the question type specific information
        if (!get_question_options($closequestions)) {
            error('Could not load question options');
        }

        // Restore the question sessions
        if (!$closestates = get_question_states($closequestions, $quiz, $attempt)) {
            error('Could not restore question sessions');
        }

        $success = true;
        foreach($closequestions as $key => $question) {
            $action->event = QUESTION_EVENTCLOSE;
            $action->responses = $closestates[$key]->responses;
            $action->timestamp = $closestates[$key]->timestamp;
            
            if (question_process_responses($question, $closestates[$key], $action, $quiz, $attempt)) {
                save_question_session($question, $closestates[$key]);
            } else {
                $success = false;
            }
        }

        if (!$success) {
            $pagebit = '';
            if ($page) {
                $pagebit = '&amp;page=' . $page;
            }
            print_error('errorprocessingresponses', 'question',
                    $CFG->wwwroot . '/mod/quiz/attempt-embedded.php?q=' . $quiz->id . $pagebit);
        }

        add_to_log($course->id, 'quiz', 'close attempt', 'review-embedded.php?attempt=' . $attempt->id, $quiz->id, $cm->id);
    }

/// Update the quiz attempt and the overall grade for the quiz
    if ($responses || $finishattempt) {
        if (!update_record('quiz_attempts', $attempt)) {
            error('Failed to save the current quiz attempt!');
        }
        if (($attempt->attempt > 1 || $attempt->timefinish > 0) and !$attempt->preview) {
            quiz_save_best_grade($quiz);

            /// Update QCA - Assign Grade1
            $qca = get_record('quiz_course_activation','attemptid',$attempt->id);
            if (!empty($qca)) {
                $qca->grade1 = $attempt->sumgrades;
                if (!update_record('quiz_course_activation', $qca)) {
                    error('Failed to save the current quiz attempt!');
                }
            }
        }
    }

/// Send emails to those who have the capability set
    if ($finishattempt && !$attempt->preview) {
        quiz_send_notification_emails($course, $quiz, $attempt, $context, $cm);
    }

    if ($finishattempt) {
        if (!empty($SESSION->passwordcheckedquizzes[$quiz->id])) {
            unset($SESSION->passwordcheckedquizzes[$quiz->id]);
        }
		// 29.06.2011 - jam
		if(isCertificateTest($id) || isCertificateTest($q,true)){
			redirect($CFG->wwwroot . '/mod/deva/closecertattempt.php');
		}else{
        	redirect($CFG->wwwroot . '/mod/quiz/review-embedded.php?attempt='.$attempt->id, 0);
        	//redirect("$CFG->wwwroot/schedule.php");									Added: JAM - to redirect after the test.
		}
    }

// Now is the right time to check the open and close times.
    if (!$ispreviewing && ($timestamp < $quiz->timeopen || ($quiz->timeclose && $timestamp > $quiz->timeclose))) {
        print_error('notavailable', 'quiz', "view-embedded.php?id={$cm->id}");
    }

/// Print the quiz page ////////////////////////////////////////////////////////

    // Print the page header


    require_js($CFG->wwwroot . '/mod/quiz/quiz.js');
    $pagequestions = explode(',', $pagelist);
    $headtags = get_html_head_contributions($pagequestions, $questions, $states);
    if (!empty($popup)) {
        define('MESSAGE_WINDOW', true);  // This prevents the message window coming up
        print_header($course->shortname.': '.format_string($quiz->name), '', '', '', $headtags, false, '', '', false, ' class="securewindow"');
        include('protect_js.php');
    } else {
        $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
                    ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
                    : "";
        $navigation = build_navigation($strattemptnum, $cm);
        print_header_simple(format_string($quiz->name), "", $navigation, "", $headtags, true, $strupdatemodule);
	
    }
?>
<!--Libraries-->
<link type="text/css" href="../deva/jquery-ui/css/redmond-light/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../deva/jquery-ui/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../deva/jquery-ui/js/jquery-ui-1.8.4.custom.min.js"></script>

<script language="javascript">
var pageLoaded = false;

$("#header").hide();
$("#footer").hide();
$(".navbar").hide(); 

$(document).ready(function() {
	    $("#header").hide();
	    $("#footer").hide();
	    $(".navbar").hide(); 
	
	    $("#pageLoaded").html('true');
	    pageLoaded = true;
});

function show_confirm(message){
	    var r=confirm(message);
	    if (r==true)
	       parent.progressDialogBox(true);
	       
	    return r;
}
</script>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<div id="pageLoaded" style="display: none;"></div>
<?php
/*
if(isCertificateTest($id) || isCertificateTest($q,true)){	// 28.06.2011 - jam

	echo '<script language="javascript">';
	echo '$(document).ready(function() {';
	echo '$("#header").hide();';
	echo '$("#footer").hide();';
	echo '$(".navbar").hide();'; 
	
	echo '$(parent.examscreenid).load(function(){';
	echo 'parent.progressDialogBox(false);';      // 14.07.2011 - jam : to stop progress bar
	echo '});';
	//echo '$(\'input[name^="finishattempt"]\').click(function(){ });';
	
	echo '});';
	echo 'function show_confirm(message){';
	echo 'var r=confirm(message);';
	echo 'if (r==true){ parent.progressDialogBox(true); }';
	echo 'return r;';
	echo '}';
	echo '</script>';

}
    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib
*/
    // Print the quiz name heading and tabs for teacher, etc.
    if ($ispreviewing) {
        $currenttab = 'preview';
        include('tabs.php');

        print_heading(get_string('previewquiz', 'quiz', format_string($quiz->name)));
        unset($buttonoptions);
        $buttonoptions['q'] = $quiz->id;
        $buttonoptions['forcenew'] = true;
        echo '<div class="controls">';
        print_single_button($CFG->wwwroot.'/mod/quiz/attempt-embedded.php', $buttonoptions, get_string('startagain', 'quiz'));
        echo '</div>';
    /// Notices about restrictions that would affect students.

        if ($quiz->popup) {
            notify(get_string('popupnotice', 'quiz'));
        }
        if ($timestamp < $quiz->timeopen || ($quiz->timeclose && $timestamp > $quiz->timeclose)) {
            notify(get_string('notavailabletostudents', 'quiz'));
        }
        if ($quiz->subnet && !address_in_subnet(getremoteaddr(), $quiz->subnet)) {
            notify(get_string('subnetnotice', 'quiz'));
        }
    } else {
        if ($quiz->attempts != 1) {
            print_heading(format_string($quiz->name).' - '.$strattemptnum);
        } else {
            print_heading(format_string($quiz->name));
        }
    }

    // Start the form
    $quiz->thispageurl = $CFG->wwwroot . '/mod/quiz/attempt-embedded.php?q=' . s($quiz->id) . '&amp;page=' . s($page);
    $quiz->cmid = $cm->id;
    echo '<form id="responseform" method="post" action="', $quiz->thispageurl . '" enctype="multipart/form-data"' .
            ' onclick="this.autocomplete=\'off\'" onkeypress="return check_enter(event);" accept-charset="utf-8">', "\n";
    if($quiz->timelimit > 0) {
        // Make sure javascript is enabled for time limited quizzes
        ?>

        <script type="text/javascript">
            // Do nothing, but you have to have a script tag before a noscript tag.
        </script>
        <noscript>
        <div>
        <?php print_heading(get_string('noscript', 'quiz')); ?>
        </div>
        </noscript>
        <?php
    }
    // For Timer
    //echo "<SCRIPT language='JavaScript' SRC='$CFG->wwwroot/count/countdown.php?countto=1250744400'></ SCRIPT>";

    echo '<div>';

/// Print the navigation panel if required
    $numpages = quiz_number_of_pages($attempt->layout);
    if ($numpages > 1) {
        quiz_print_navigation_panel($page, $numpages);
    }

/// Print all the questions
    $number = quiz_first_questionnumber($attempt->layout, $pagelist);
    foreach ($pagequestions as $i) {
        $options = quiz_get_renderoptions($quiz->review, $states[$i]);
        // Print the question
        print_question($questions[$i], $states[$i], $number, $quiz, $options);
        
        // Added to track Actual Question ID
        //$wrappedquestion = $states[$i]->options->question;
        //$wrappedquestion->randomquestionid = $questions[$i]->id;
        //saveQCAQuestions($questions[$i]->id,$wrappedquestion->id);

        save_question_session($questions[$i], $states[$i]);
        $number += $questions[$i]->length;
        
    }

/// Print the submit buttons
	// SMS: 7/22/2014 Changed to support embedded version
	/*
	if(isCertificateTest($id) || isCertificateTest($q,true)){	// 28.06.2011 -jam
		$strconfirmattempt = addslashes(get_string("confirmcertclose", "quiz"));
		$onclick = "return show_confirm('$strconfirmattempt')";	// 14.07.2011 - jam
	}else{
		$strconfirmattempt = addslashes(get_string("confirmclose", "quiz"));
		$onclick = "return confirm('$strconfirmattempt')"; // 14.07.2011 - jam
	}
	//$onclick = "return confirm('$strconfirmattempt')";
	echo "<div class=\"submitbtns mdl-align\">\n";
	
	echo '<input type="hidden" name="id" id="id" value="'.$id.'" />'; // 29.06.2011 - jam
	
	echo "<input type=\"submit\" name=\"saveattempt\" value=\"".get_string("savenosubmit", "quiz")."\" />\n";
	if ($quiz->optionflags & QUESTION_ADAPTIVE) {
		echo "<input type=\"submit\" name=\"markall\" value=\"".get_string("markall", "quiz")."\" />\n";
	}
	echo "<input type=\"submit\" name=\"finishattempt\" value=\"".get_string("finishattempt", "quiz")."\" onclick=\"$onclick\" />\n";

	echo "</div>";
	*/
	if(!$isinnewwindow) {
		if(isCertificateTest($id) || isCertificateTest($q,true)){	// 28.06.2011 -jam
			$strconfirmattempt = addslashes(get_string("confirmcertclose", "quiz"));
			$onclick = "return show_confirm('$strconfirmattempt')";	// 14.07.2011 - jam
		}else{
			$strconfirmattempt = addslashes(get_string("confirmclose", "quiz"));
			$onclick = "return confirm('$strconfirmattempt')"; // 14.07.2011 - jam
		}
		//$onclick = "return confirm('$strconfirmattempt')";
		echo "<div class=\"submitbtns mdl-align\">\n";
		
		echo '<input type="hidden" name="id" id="id" value="'.$id.'" />'; // 29.06.2011 - jam
		
		echo "<input type=\"submit\" name=\"saveattempt\" value=\"".get_string("savenosubmit", "quiz")."\" />\n";
		if ($quiz->optionflags & QUESTION_ADAPTIVE) {
			echo "<input type=\"submit\" name=\"markall\" value=\"".get_string("markall", "quiz")."\" />\n";
		}
		echo "<input type=\"submit\" name=\"finishattempt\" value=\"".get_string("finishattempt", "quiz")."\" onclick=\"$onclick\" />\n";
	
		echo "</div>";
	} else {
		if(isCertificateTest($id) || isCertificateTest($q,true)){	// 28.06.2011 -jam
			$strconfirmattempt = addslashes(get_string("confirmcertclose", "quiz"));
			$onclick = "return show_confirm('$strconfirmattempt')";	// 14.07.2011 - jam
		}else{
			$strconfirmattempt = addslashes(get_string("confirmclose", "quiz"));
			$onclick = "return confirm('$strconfirmattempt')"; // 14.07.2011 - jam
		}
		//$onclick = "return confirm('$strconfirmattempt')";
		echo "<div class=\"submitbtns mdl-align\">\n";
		
		echo '<input type="hidden" name="id" id="id" value="'.$id.'" />'; // 29.06.2011 - jam
		
		echo "<input type=\"submit\" name=\"saveattempt\" value=\"".get_string("savenosubmit", "quiz")."\" />\n";
		if ($quiz->optionflags & QUESTION_ADAPTIVE) {
			echo "<input type=\"submit\" name=\"markall\" value=\"".get_string("markall", "quiz")."\" />\n";
		}
		// echo "<input type=\"submit\" name=\"finishattempt\" value=\"".get_string("finishattempt", "quiz")."\" onclick=\"$onclick\" />\n";
	
		echo "<br />";
		echo "To finish or close this attempt, you must first close this window, then go back to the main certificate test page, and then use the 'Submit all and finish' button at the bottom of the Exam Questions tab.\n";
		echo "</div>";
	}
	// SMS End Change

    // Print the navigation panel if required
    if ($numpages > 1) {
        quiz_print_navigation_panel($page, $numpages);
    }

    // Finish the form
    echo '</div>';
    echo '<input type="hidden" name="timeup" id="timeup" value="0" />';

    // Add a hidden field with questionids. Do this at the end of the form, so 
    // if you navigate before the form has finished loading, it does not wipe all
    // the student's answers.
    echo '<input type="hidden" name="questionids" value="'.$pagelist."\" />\n";

    echo "</form>\n";

    /// Set timer from QCA
    $qca = get_record('quiz_course_activation','attemptid',$attempt->id);
    if (!empty($qca)) {
        $starttime = $qca->starttime + ($exam_length * 60);
        //$currentTime = date("U",mktime(1,27,0,9,13,2009));
        $currentTime = time();
        if(($currentTime - $starttime) < 0){
            $dateDiff = $starttime - $currentTime;
            //$fullDays    = floor($dateDiff/(60*60*24));
            //$fullHours   = floor(($dateDiff-($fullDays*60*60*24))/(60*60));
            //$fullMinutes = floor(($dateDiff-($fullDays*60*60*24)-($fullHours*60*60))/60);

            //$days = floor($dateDiff/60/60/24);
            $hours = $dateDiff/60/60%24;
            $mins = $dateDiff/60%60;
            $secs = $dateDiff%60;
            //echo "H:$hours M:$mins S:$secs ";
            $fullMinutes = ((($hours*60) + $mins + ($secs/60)));

            $timeleft = $fullMinutes;
        }else{
            $timeleft = 0;
        }
    }
 

/// Edited Timer - for QCA
    // If the quiz has a time limit, or if we are close to the close time, include a floating timer.
    $showtimer = false;
    $timerstartvalue = 999999999999;
    if ($quiz->timeclose) {
        $timerstartvalue = min($timerstartvalue, $quiz->timeclose - time());
        $showtimer = $timerstartvalue < 60*60; // Show the timer if we are less than 60 mins from the deadline.
    }
    if ($quiz->timelimit > 0 && !has_capability('mod/quiz:ignoretimelimits', $context, NULL, false)) {
        if (!empty($qca)) {
            //$timerstartvalue = min($timerstartvalue, ($attempt->timestart + ($timeleft*60))- time());
            $timerstartvalue = min($timeleft*60, ($attempt->timestart + $quiz->timelimit*60)- time());
            //echo "LEFT:".$timeleft." ";
            //echo "TIMERSTR:".$timerstartvalue." ATMP:".(($attempt->timestart + $quiz->timelimit*60)- time()/60);

        }else{
            $timerstartvalue = min($timerstartvalue, $attempt->timestart + $quiz->timelimit*60- time());    ////HERE
            //echo "RIGHT:".min($timerstartvalue, $attempt->timestart + $quiz->timelimit*60- time());
        }
        $showtimer = true;
    }
    if ($showtimer && (!$ispreviewing || $timerstartvalue > 0)) {
        $timerstartvalue = max($timerstartvalue, 1); // Make sure it starts just above zero.
        require('jstimer.php');
    }

    // Finish the page
    if (empty($popup)) {
    	print_footer($course);
    }
?>



<?php


/// Functions: Format Context for Questions
//function replace_keywords($quizid,$str){
function replace_keywords($qcaid,$str){
    
    //$attempt = quiz_get_user_attempt_unfinished($quizid, $USER->id);

    //$qca = get_record('quiz_course_activation','attemptid',$attempt);
    $qca = get_record('quiz_course_activation','id',$qcaid);
    if(!empty($qca)){
        $eqc = get_record('exam_quiz_context','id',$qca->exam_context);
        if(!empty($eqc)){
            $group = $eqc->contextid;
            $course = $eqc->courseid;

            // << [a-zA-Z0-9]+ >>
            $matched = preg_match_all("/<< [a-zA-Z0-9]+ >>/", $str, $matches);
            if($matched){
                foreach ($matches as $match) {
                    foreach ($match as $val) {
                        $key = get_keyword($group,substr($val,3,strpos($val," >>")-2),$course);
                        $str = str_ireplace($val,$key,$str,$count);
                    }
                }
            }
            // &lt;&lt; [a-zA-Z0-9]+ &gt;&gt;
            $matched = preg_match_all("/&lt;&lt; [a-zA-Z0-9]+ &gt;&gt;/", $str, $matches);
            if($matched){

                foreach ($matches as $match) {
                    foreach ($match as $val) {
                        $key = get_keyword($group,substr($val,9,strpos($val," >>")-8),$course);
                        $str = str_ireplace($val,$key,$str,$count);
                    }
                }
            }
        }
    }
    return $str;
}

function get_keyword($group,$keyword,$course){
    $key = get_record('quiz_context_keys','key_code',$keyword,'courseid',$course);

    $record = get_record('context_key_words','key_group',$group,'key_id',$key->key_id,'courseid',$course);

    if (!empty($record)) {
        return $record->keyword;
    }else{
        return "";
    }
}

?>