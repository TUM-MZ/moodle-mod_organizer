<?php
// This file is part of mod_organizer for Moodle - http://moodle.org/
//
// It is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * view_action_form_remind_all.php
 *
 * @package       mod_organizer
 * @author        Andreas Hruska (andreas.hruska@tuwien.ac.at)
 * @author        Katarzyna Potocka (katarzyna.potocka@tuwien.ac.at)
 * @author        Andreas Windbichler
 * @author        Ivan Šakić
 * @copyright     2014 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Required for the form rendering.
require_once("$CFG->libdir/formslib.php");
require_once(dirname(__FILE__) . '/slotlib.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/view_lib.php');

class organizer_remind_all_form extends moodleform {

    protected function definition() {
        global $DB;

        $mform = $this->_form;
        $data = $this->_customdata;

        $recipients = $data['recipients'];
        $recipientscount = count($recipients);
        $recipient = $data['recipient'];

        $mform->addElement('hidden', 'id', $data['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'mode', $data['mode']);
        $mform->setType('mode', PARAM_INT);
        $mform->addElement('hidden', 'action', 'remindall');
        $mform->setType('action', PARAM_ACTION);
        $mform->addElement('hidden', 'recipient', $recipient);
        $mform->setType('recipient', PARAM_INT);

        list($cm, $course, $organizer, $context) = organizer_get_course_module_data();

        $buttonarray = array();
        if ($recipientscount > 0) {
            $a = new stdClass();
            $a->count = $recipientscount;
            if ($recipientscount == 1) {
                $mform->addElement('static', '', '', get_string('organizer_remind_all_recepients_sg', 'organizer', $a));
            } else {
                $mform->addElement('static', '', '', get_string('organizer_remind_all_recepients_pl', 'organizer', $a));
            }
            foreach ($recipients as $recepient) {
                $mform->addElement('static', '', '',
                        organizer_get_name_link($recepient->id) . ($recepient->idnumber ? " ({$recepient->idnumber})" : ''));
            }
            $buttonarray[] = &$mform->createElement('submit', 'confirm', get_string('confirm_organizer_remind_all', 'organizer'));
        } else {
            $mform->addElement('static', '', '', get_string('organizer_remind_all_no_recepients', 'organizer'));
            $buttonarray[] = &$mform->createElement('submit', 'confirm', get_string('confirm_organizer_remind_all', 'organizer'),
                    array('disabled'));
        }
        $buttonarray[] = &$mform->createElement('cancel');

        $strautomessage = "register_reminder:";
        $strautomessage .= ($organizer->isgrouporganizer == 0) ? "student" : "group";
        $strautomessage .= ":fullmessage";

        $a = new stdClass();
        $a->receivername = get_string('recipientname', 'organizer');
        $a->courseid = ($course->idnumber == "") ? "" : $course->idnumber . ' ';
        $a->coursefullname = $course->fullname;
        $a->custommessage = "";

        $mform->addElement('static', 'message_autogenerated', get_string('message_autogenerated2', 'organizer'),
                nl2br(str_replace("\n\n\n", "\n", get_string($strautomessage, 'organizer', $a))));

        $mform->addElement('editor', 'message_custommessage', get_string('message_custommessage', 'organizer'));
        $mform->addHelpButton('message_custommessage', 'message_custommessage', 'organizer');

        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }
}