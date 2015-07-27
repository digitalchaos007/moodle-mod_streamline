<?php

/**
 * Definition of log events
 *
 * @package   mod_streamline
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2010-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module'=>'streamline', 'action'=>'add', 'mtable'=>'streamline', 'field'=>'name'),
    array('module'=>'streamline', 'action'=>'update', 'mtable'=>'streamline', 'field'=>'name'),
    array('module'=>'streamline', 'action'=>'view', 'mtable'=>'streamline', 'field'=>'name'),
    array('module'=>'streamline', 'action'=>'view all', 'mtable'=>'streamline', 'field'=>'name')
);