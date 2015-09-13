<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Redirect the user to the appropriate submission related page
 *
 * @package   mod_streamline
 * @category  grade
 * @copyright 2015 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');	
	global $DB, $CFG;
	$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
	$b  = optional_param('n', 0, PARAM_INT);  // streamline instance ID
	$group  = optional_param('group', 0, PARAM_INT);  // streamline group ID

	if ($id) {
		$cm = get_coursemodule_from_id('streamline', $id, 0, false, MUST_EXIST);
		$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
		$streamline = $DB->get_record('streamline', array('id' => $cm->instance), '*', MUST_EXIST);
	} elseif ($b) {
		$streamline = $DB->get_record('streamline', array('id' => $n), '*', MUST_EXIST);
		$course = $DB->get_record('course', array('id' => $streamline->course), '*', MUST_EXIST);
		$cm = get_coursemodule_from_instance('streamline', $streamline->id, $course->id, false, MUST_EXIST);
	} else {
		print_error('You must specify a course_module ID or an instance ID');
	}
	
	
	
	$dataObj = new stdClass();
	$dataObj->id = $streamline -> id;
	$dataObj -> meetingended = 1;
	$table = 'streamline';
	$DB -> update_record($table,$dataObj );



?>