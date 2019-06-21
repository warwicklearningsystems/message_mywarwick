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

        $users = array();

        // If username is empty we try to retrieve it, since it's required to generate the siteid.
        if (empty($eventdata->userto->username)) {
          $eventdata->userto->username = $DB->get_field('user', 'username', array('id' => $eventdata->userto->id));
        }

        $users[] = $eventdata->userto->username;

        // Build recipients list
        $recipients = new stdClass;
        $recipients->users = $users;
        //$recipients->groups = array();

        // Construct the alert
        $alert = new stdClass;
        $alert->type = $eventdata->component . " " . $eventdata->name;
        $alert->title = $eventdata->subject;
        $alert->text = $eventdata->smallmessage;

        $alert->url = !empty( $eventdata->contexturl ) ? ( is_object( $eventdata->contexturl ) ? $eventdata->contexturl->out() : $eventdata->contexturl ) : '';
        //$alert->tags = array();
        //$alert->generated_at = now();
        $alert->recipients = $recipients;

        // Encode alert and construct message to send
        $postdata = json_encode($alert);
        $header = array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($postdata));

        $serverurl = $CFG->mywarwickurl;
        $curl = new curl;
        $curl->setHeader($header);

        $curl->setopt( array(
          'CURLOPT_USERPWD' => $CFG->mywarwickusername . ":" . $CFG->mywarwickuserpassword)
        );

        $resp = $curl->post($serverurl, $postdata);

        $info = $curl->get_info();
        if( !in_array( $info[ 'http_code' ], [ '200', '201', '202' ] ) ){
            $errorText = $info[ 'http_code' ] ? 'Server returned HTTP Status: '.$info[ 'http_code' ] : '';
            debugging( 'FAILED TO MY WARWICK. '.$errorText, DEBUG_DEVELOPER);

            $event = \message_mywarwick\event\alert_failed::create(array(
                'context' => context_system::instance(),
                'userid' => $eventdata->userto->id,
                'other' => array(
                    'title' => $alert->title,
                    'text' => $alert->text,
                    'errortext' => $errorText
                )
            ));

            $event->trigger();
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
