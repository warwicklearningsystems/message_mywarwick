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
 * My Warwick message processor configuration page
 *
 * @package   message_mywarwick
 * @copyright 2018 University of Warwick
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
  $settings->add(new admin_setting_configtext('mywarwickurl',
    get_string('mywarwickurl', 'message_mywarwick'),
    get_string('configmywarwickurl', 'message_mywarwick'), 'https://my.warwick.ac.uk', PARAM_URL));
  $settings->add(new admin_setting_configtext('mywarwickusername',
    get_string('mywarwickusername', 'message_mywarwick'),
    get_string('mywarwickusername_config', 'message_mywarwick'), '', PARAM_TEXT));
  $settings->add(new admin_setting_configtext('mywarwickuserpassword',
    get_string('mywarwickpassword', 'message_mywarwick'),
    get_string('mywarwickpassword_config', 'message_mywarwick'), '', PARAM_ALPHANUMEXT));
}
