<?php
/**
 * Ping the BigBlueButton server to see if the meeting is running
 *
 * @package   mod_streamline
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2010-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

$meetingID = required_param('meetingid', PARAM_TEXT);
$callback = required_param('callback', PARAM_TEXT);
$id = optional_param('id', 0, PARAM_INT);

if (!$meetingID) {
    $error = 'You must specify a meetingID';
}

if (!$callback) {
    $error = 'This call must include a javascript callback';
}

header('Content-Type: application/json; charset=utf-8');
if ( !isset($error) ) {

    if (!isloggedin() && $PAGE->course->id == SITEID) {
        $userid = guest_user()->id;
    } else {
        $userid = $USER->id;
    }
    $hascourseaccess = ($PAGE->course->id == SITEID) || can_access_course($PAGE->course, $userid);
    	
    if( !$hascourseaccess ){
        header("HTTP/1.0 401 Unauthorized");
    } else {
        $salt = trim($CFG->BigBlueButtonSaltKey);
        $url = trim(trim($CFG->ServerURLforBigBlueButton),'/').'/';

        try{
            $ismeetingrunning = (streamline_isMeetingRunning( $meetingID, $url, $salt )? 'true': 'false');
            if( $ismeetingrunning === 'true' ) {
                ///log the join event
                if ( $streamline = $DB->get_record('streamline', array('id' => $id), '*', MUST_EXIST) ) {
                    $course = $DB->get_record('course', array('id' => $streamline->course), '*', MUST_EXIST);
                    $cm = get_coursemodule_from_instance('streamline', $streamline->id, $course->id, false, MUST_EXIST);
                    /// Moodle event logger: Create an event for meeting joined
                    if ( $CFG->version < '2014051200' ) {
                        //This is valid before v2.7
                        add_to_log($course->id, 'streamline', 'meeting joined', '', $streamline->name, $cm->id);
                    } else {
                        //This is valid after v2.7
                        $context = context_module::instance($cm->id);
                        $event = \mod_streamline\event\bigbluebuttonbn_meeting_joined::create(
                                array(
                                        'context' => $context,
                                        'objectid' => $streamline->id
                                )
                        );
                        $event->trigger();
                    }
                }
            }
            echo $callback.'({ "status": "'.$ismeetingrunning.'" });';
        }catch(Exception $e){
            header("HTTP/1.0 502 Bad Gateway. ".$e->getMessage());
        }
        
    }

} else {
    header("HTTP/1.0 400 Bad Request. ".$error);
}