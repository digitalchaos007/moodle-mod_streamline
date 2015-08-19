<?php
/**
 * Library calls for Moodle and BigBlueButton.
 * 
 * @package   mod_streamline
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2010-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/calendar/lib.php');

function streamline_supports($feature) {
    switch($feature) {
        case FEATURE_IDNUMBER:                return true;
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        // case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:          return true;

        default: return null;
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $streamline An object from the form in mod_form.php
 * @return int The id of the newly inserted streamline record
 */
function streamline_add_instance($streamline) {
    global $DB;

    $streamline->timecreated = time();

    $streamline->moderatorpass = bigbluebuttonbn_rand_string();
    $streamline->viewerpass = bigbluebuttonbn_rand_string();
    $streamline->meetingid = bigbluebuttonbn_rand_string();

    if (! isset($streamline->newwindow))     $streamline->newwindow = 0;
    if (! isset($streamline->wait))          $streamline->wait = 0;
    if (! isset($streamline->record))        $streamline->record = 0;
    if (! isset($streamline->allmoderators)) $streamline->allmoderators = 0;

    $returnid = $DB->insert_record('streamline', $streamline);
    
    if (isset($streamline->timeavailable) && $streamline->timeavailable ){
        $event = new stdClass();
        $event->name        = $streamline->name;
        $event->courseid    = $streamline->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'streamline';
        $event->instance    = $returnid;
        $event->timestart   = $streamline->timeavailable;

        if ( $streamline->timedue ){
            $event->timeduration = $streamline->timedue - $streamline->timeavailable;
        } else {
            $event->timeduration = 0;
        }
        
        calendar_event::create($event);
    }
    
    return $returnid;
    
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $streamline An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function streamline_update_instance($streamline) {
    global $DB;

    $streamline->timemodified = time();
    $streamline->id = $streamline->instance;

    if (! isset($streamline->newwindow))     $streamline->newwindow = 0;
    if (! isset($streamline->wait))          $streamline->wait = 0;
    if (! isset($streamline->record))        $streamline->record = 0;
    if (! isset($streamline->allmoderators)) $streamline->allmoderators = 0;

    $returnid = $DB->update_record('streamline', $streamline);
    
    if (isset($streamline->timeavailable) && $streamline->timeavailable ){
        $event = new stdClass();
        $event->name        = $streamline->name;
        $event->courseid    = $streamline->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'streamline';
        $event->instance    = $streamline->id;
        $event->timestart   = $streamline->timeavailable;

        if ( $streamline->timedue ){
            $event->timeduration = $streamline->timedue - $streamline->timeavailable;
            
        } else {
            $event->timeduration = 0;
            
        }

        if ($event->id = $DB->get_field('event', 'id', array('modulename'=>'streamline', 'instance'=>$streamline->id))) {
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event);
            
        } else {
            calendar_event::create($event);
            
        }
        
    } else {
        $DB->delete_records('event', array('modulename'=>'streamline', 'instance'=>$streamline->id));
        
    }
    
    return $returnid;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function streamline_delete_instance($id) {
    global $CFG, $DB;

    if (! $streamline = $DB->get_record('streamline', array('id' => $id))) {
        return false;
    }

    $result = true;

    //
    // End the session associated with this instance (if it's running)
    //
    $meetingID = $streamline->meetingid.'-'.$streamline->course.'-'.$streamline->id;
    
    $modPW = $streamline->moderatorpass;
    $url = trim(trim($CFG->streamlineServerURL),'/').'/';
    $salt = trim($CFG->streamlineSecuritySalt);

    //if( streamline_isMeetingRunning($meetingID, $url, $salt) )
    //    $getArray = streamline_doEndMeeting( $meetingID, $modPW, $url, $salt );
	
    if (! $DB->delete_records('streamline', array('id' => $streamline->id))) {
        $result = false;
    }

    if (! $DB->delete_records('event', array('modulename'=>'streamline', 'instance'=>$streamline->id))) {
        $result = false;
    }
    
    
    
    return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 */
function streamline_user_outline($course, $user, $mod, $streamline) {
    return true;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 */
function streamline_user_complete($course, $user, $mod, $streamline) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in streamline activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function streamline_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Returns all activity in streamline since a given time
 *
 * @param array $activities sequentially indexed array of objects
 * @param int $index
 * @param int $timestart
 * @param int $courseid
 * @param int $cmid
 * @param int $userid defaults to 0
 * @param int $groupid defaults to 0
 * @return void adds items into $activities and increases $index
 */
function streamline_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see recordingsbn_get_recent_mod_activity()}

 * @return void
 */
function streamline_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 **/
function streamline_cron () {
    return true;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of streamline. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $streamlineid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function streamline_get_participants($streamlineid) {
    return false;
}

/**
 * Returns all other caps used in module
 * @return array
 */
function streamline_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * This function returns if a scale is being used by one streamline
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $streamlineid ID of an instance of this module
 * @return mixed
 */
function streamline_scale_used($streamlineid, $scaleid) {
    $return = false;

    return $return;
}

/**
 * Checks if scale is being used by any instance of streamline.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any streamline
 */
function streamline_scale_used_anywhere($scaleid) {
    $return = false;

    return $return;
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function streamline_reset_userdata($data) {
    return array();
}

/**
 * List of view style log actions
 * @return array
 */
function streamline_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List of update style log actions
 * @return array
 */
function streamline_get_post_actions() {
    return array('update', 'add');
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return object|null
 */
function streamline_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;

    if (! $streamline = $DB->get_record('streamline', array('id'=>$coursemodule->instance), 'id, name, newwindow')) {
        return NULL;
    }
    
    $info = new cached_cm_info();
    $info->name = $streamline->name;
    
    if ( $streamline->newwindow == 1 ){
        $fullurl = "$CFG->wwwroot/mod/streamline/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->onclick = "window.open('$fullurl'); return false;";
    }
    
    return $info;

}