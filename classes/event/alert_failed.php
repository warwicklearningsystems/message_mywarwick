<?php

namespace message_mywarwick\event;

use core\event\base;
defined('MOODLE_INTERNAL') || die();

class alert_failed extends base {

    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('alertfailed', 'message_mywarwick');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "Failed to send My Warwick alert to user '$this->userid'.".( !empty( $this->other['errortext'] ) ? ' '.$this->other['errortext'].'.' : '' );
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['title'])) {
            throw new \coding_exception('The \'title\' value must be set in other.');
        }
        if (!isset($this->other['text'])) {
            throw new \coding_exception('The \'text\' value must be set in other.');
        }
        if (!isset($this->other['errortext'])) {
            throw new \coding_exception('The \'errortext\' value must be set in other.');
        }
    }

    public static function get_other_mapping() {
        return false;
    }
}
