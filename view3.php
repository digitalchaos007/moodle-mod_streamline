<?php
/* // This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>. */

/**
 * Prints a particular instance of streamline
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_streamline
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//THESE TWO ARE CAUSING PROBLEMS
//require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');



 //Creates the join call 
$ipaddress = "104.155.215.138";
$salt = "44736ac800eb3cd0f10f8fca5e2a812a";
$meetingID = "5";
$password = "cC8kGYjL";
$userName = "admin";
//$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or 
$id = optional_param('id', 0, PARAM_INT);
echo $id; // NOTE THE STREAMLINE ID IS DIFFERENT TO COURSE ID

$clientURL = "http://192.168.160.1/moodle/mod/streamline/view2.php?id=". $id; //CHANGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$params = "meetingID=" . $meetingID . "&password=" . $password . "&fullName=" . $userName . "&redirect=true&clientURL=" . $clientURL;

$Checksum = sha1("join" . $params . $salt);

$url = "http://" . $ipaddress . "/bigbluebutton/api/join?" . $params . "&checksum=" . $Checksum;
// Prints join call
print $url;

$bbbsession['username'] = get_string('fullnamedisplay', 'moodle', $USER);
echo $bbbsession['username'];

//Redirects the BBB client within moodle
//header('Location: '.$url);


?>


