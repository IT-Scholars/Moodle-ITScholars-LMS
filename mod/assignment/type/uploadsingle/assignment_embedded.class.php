<?php // $Id: assignment-embedded.class.php,v 1.33.2.5 2008/04/08 03:02:49 scyrma Exp $

require ("$CFG->dirroot/mod/assignment/type/uploadsingle/assignment.class.php");

/**
 * Extend the base assignment_uploadsingle class for assignments where you upload a single file for embedded version
 *
 */
class assignment_embedded_uploadsingle extends assignment_uploadsingle {

    function view() {

        global $USER;

        $context = get_context_instance(CONTEXT_MODULE,$this->cm->id);
        require_capability('mod/assignment:view', $context);

        add_to_log($this->course->id, "assignment-embedded", "view", "view-embedded.php?id={$this->cm->id}", $this->assignment->id, $this->cm->id);

        // $this->view_header();

		echo "<center>";
        $this->view_intro();
		echo "Hands-on exams will be graded within 10 business days. Results will be provided via email. If you do not receive your results within 10 business days of submitting your answer file, please open a ticket with Kaseya University at helpdesk.kaseya.com.";
		echo "</center>";
        
        $this->view_dates();

        $filecount = $this->count_user_files($USER->id);

        if ($submission = $this->get_submission()) {
            if ($submission->timemarked) {
                $this->view_feedback();
            }
            if ($filecount) {
                print_simple_box($this->print_user_files($USER->id, true), 'center');
            }
        }

        if (has_capability('mod/assignment:submit', $context)  && $this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            $this->view_upload_form();
        }

        // $this->view_footer();
    }

    function view_upload_form() {
        global $CFG;
        $struploadafile = get_string("uploadafile");

        $maxbytes = $this->assignment->maxbytes == 0 ? $this->course->maxbytes : $this->assignment->maxbytes;
        $strmaxsize = get_string('maxsize', '', display_size($maxbytes));

        echo '<div style="text-align:center">';
        echo '<form enctype="multipart/form-data" method="post" '.
             "action=\"$CFG->wwwroot/mod/assignment/upload-embedded.php\">";
        echo '<fieldset class="invisiblefieldset">';
        echo "<p>$struploadafile ($strmaxsize)</p>";
        echo '<input type="hidden" name="id" value="'.$this->cm->id.'" />';
        require_once($CFG->libdir.'/uploadlib.php');
        upload_print_form_fragment(1,array('newfile'),false,null,0,$this->assignment->maxbytes,false);
        echo '<input type="submit" name="save" value="'.get_string('uploadthisfile').'" />';
        echo '</fieldset>';
        echo '</form>';
        echo '</div>';
    }


    function upload() {

        global $CFG, $USER;

        require_capability('mod/assignment:submit', get_context_instance(CONTEXT_MODULE, $this->cm->id));

        // $this->view_header(get_string('upload'));

        echo "<center>";
        
		$filecount = $this->count_user_files($USER->id);
        $submission = $this->get_submission($USER->id);
        if ($this->isopen() && (!$filecount || $this->assignment->resubmit || !$submission->timemarked)) {
            if ($submission = $this->get_submission($USER->id)) {
                //TODO: change later to ">= 0", to prevent resubmission when graded 0
                if (($submission->grade > 0) and !$this->assignment->resubmit) {
                    notify(get_string('alreadygraded', 'assignment'));
                }
            }

            $dir = $this->file_area_name($USER->id);

            require_once($CFG->dirroot.'/lib/uploadlib.php');
            $um = new upload_manager('newfile',true,false,$this->course,false,$this->assignment->maxbytes);
            if ($um->process_file_uploads($dir)) {
                $newfile_name = $um->get_new_filename();
                if ($submission) {
                    $submission->timemodified = time();
                    $submission->numfiles     = 1;
                    $submission->submissioncomment = addslashes($submission->submissioncomment);
                    unset($submission->data1);  // Don't need to update this.
                    unset($submission->data2);  // Don't need to update this.
                    if (update_record("assignment_submissions", $submission)) {
                        add_to_log($this->course->id, 'assignment', 'upload',
                                'view-embedded.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        $submission = $this->get_submission($USER->id);
                        $this->update_grade($submission);
                        $this->email_teachers($submission);
        echo "Hands-on exams will be graded within 10 business days. Results will be provided via email. If you do not receive your results within 10 business days of submitting your answer file, please open a ticket with Kaseya University at helpdesk.kaseya.com.";
                        print_heading(get_string('uploadedfile'));
                    } else {
                        notify(get_string("uploadfailnoupdate", "assignment"));
                    }
                } else {
                    $newsubmission = $this->prepare_new_submission($USER->id);
                    $newsubmission->timemodified = time();
                    $newsubmission->numfiles = 1;
                    if (insert_record('assignment_submissions', $newsubmission)) {
                        add_to_log($this->course->id, 'assignment', 'upload',
                                'view-embedded.php?a='.$this->assignment->id, $this->assignment->id, $this->cm->id);
                        $submission = $this->get_submission($USER->id);
                        $this->update_grade($submission);
                        $this->email_teachers($newsubmission);
                        print_heading(get_string('uploadedfile'));
                    } else {
                        notify(get_string("uploadnotregistered", "assignment", $newfile_name) );
                    }
                }
            }
        } else {
            notify(get_string("uploaderror", "assignment")); //submitting not allowed!
        }

        print_continue('view-embedded.php?id='.$this->cm->id);
		
		echo "</center>";
        
        // $this->view_footer();
    }

}

?>
