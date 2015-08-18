<?php
/**
 * Settings for Streamline
 *
 * @package   mod_Streamline
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2010-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add( new admin_setting_configtext( 'ServerURLforBigBlueButton', get_string( 'streamlineBBBURL', 'streamline' ), get_string( 'streamlineBBBURLcomment', 'streamline' ), 'http://104.155.215.138/bigbluebutton/' ) );
    $settings->add( new admin_setting_configtext( 'BigBlueButtonSaltKey', get_string( 'streamlineBBBSalt', 'streamline' ), get_string( 'streamlineBBBURLcomment', 'streamline' ), '44736ac800eb3cd0f10f8fca5e2a812a' ) );
}

?>
