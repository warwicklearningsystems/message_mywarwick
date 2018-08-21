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
 * My Warwick message processor
 *
 * @package   message_mywarwick
 * @copyright 2018 University of Warwick
 */


require_once($CFG->dirroot.'/message/output/lib.php');

class message_output_mywarwick extends message_output {

    /**
     * Processes the message (do nothing).
     * @param object $eventdata the event data submitted by the message sender plus $eventdata->savedmessageid
     */
    function send_message($eventdata) {

        global $CFG, $DB;
        require_once($CFG->libdir . '/filelib.php');

        // Skip any messaging suspended and deleted users.
        if ($eventdata->userto->auth === 'nologin' or
          $eventdata->userto->suspended or
          $eventdata->userto->deleted) {
          return true;
        }

        // If username is empty we try to retrieve it, since it's required to generate the siteid.
        if (empty($eventdata->userto->username)) {
          $eventdata->userto->username = $DB->get_field('user', 'username', array('id' => $eventdata->userto->id));
        }

        // Construct the alert
        $alert = new stdClass;
        $alert->type = $type;
        $alert->title = $title;
        $alert->text = $text;
        $alert->url = $url;
        //$notification->tags = array();
        //$notification->generated_at = now();

        $recipients = new stdClass;
        $recipients->users = $users;
        //$recipients->groups = array();

        $alert->recipients = $recipients;

        $postdata = json_encode($alert);

        // Sending the message to the device.
        $serverurl = $CFG->mywarwickurl;
        $header = array('Accept: application/json',
          'Content-Length: ' . strlen($postdata));

        $curl = new curl;
        $curl->setHeader($header);

        // JSON POST raw body request.
        $resp = $curl->post($serverurl, $postdata);

        $info = $curl->get_info();
        if( $info['http_code'] == '200' ) {
          $jdata = json_decode($resp, false);
        }

        return true;
    }

    /**
     * Creates necessary fields in the messaging config form.
     *
     * @param array $preferences An array of user preferences
     */
    function config_form($preferences){
        return '';
    }

    /**
     * Parses the submitted form data and saves it into preferences array.
     *
     * @param stdClass $form preferences form class
     * @param array $preferences preferences array
     */
    function process_form($form, &$preferences){
    }

    /**
     * Returns the default message output settings for this output
     *
     * @return int The default settings
     */
    public function get_default_messaging_settings() {
        return MESSAGE_PERMITTED;
    }

    /**
     * Loads the config data from database to put on the form during initial form display
     *
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    function load_data(&$preferences, $userid){
    }

    /**
     * Returns true as message can be sent to internal support user.
     *
     * @return bool
     */
    public function can_send_to_any_users() {
        return true;
    }

    public function is_system_configured() {
      return true;
    }
}
