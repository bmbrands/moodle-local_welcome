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
 * Event observer for local_welcome.
 *
 * Handles the user_created event to send welcome and moderator
 * notification emails.
 *
 * @package    local_welcome
 * @copyright  2017 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_welcome;

defined('MOODLE_INTERNAL') || die();

/**
 * Observer class for local_welcome events.
 *
 * @package    local_welcome
 * @copyright  2017 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * Send welcome email to a newly created user and optionally notify a moderator.
     *
     * @param \core\event\user_created $event The user_created event.
     * @return void
     */
    public static function send_welcome(\core\event\user_created $event): void {
        global $CFG, $SITE;

        $eventdata = $event->get_data();

        $user = \core_user::get_user($eventdata['objectid']);

        $sender = get_admin();

        // Sender can be false when unit tests are running.
        if ($sender === false) {
            return;
        }

        if (empty($user->email)) {
            return;
        }

        $config = get_config('local_welcome');

        $moderator = clone($sender);

        if (!empty($config->auth_plugins)) {
            $auths = explode(',', $config->auth_plugins);
            if (!in_array($user->auth, $auths)) {
                return;
            }
        } else {
            return;
        }

        $moderator->email = $config->moderator_email;

        $sender->email = $config->sender_email;
        $sender->firstname = $config->sender_firstname;
        $sender->lastname = $config->sender_lastname;

        $messageuserenabled = $config->message_user_enabled;
        $messageuser = $config->message_user;
        $messageusersubject = $config->message_user_subject;

        $messagemoderatorenabled = $config->message_moderator_enabled;
        $messagemoderator = $config->message_moderator;
        $messagemoderatorsubject = $config->message_moderator_subject;

        $welcome = new \local_welcome\message();

        $messageuser = $welcome->replace_values($user, $messageuser);
        $messageusersubject = $welcome->replace_values($user, $messageusersubject);
        $messagemoderator = $welcome->replace_values($user, $messagemoderator);
        $messagemoderatorsubject = $welcome->replace_values($user, $messagemoderatorsubject);

        if (!empty($messageuser) && !empty($sender->email) && $messageuserenabled) {
            email_to_user($user, $sender, $messageusersubject, html_to_text($messageuser), $messageuser);
        }

        if (!empty($messagemoderator) && !empty($sender->email) && $messagemoderatorenabled) {
            email_to_user($moderator, $sender, $messagemoderatorsubject,
                html_to_text($messagemoderator), $messagemoderator);
        }
    }

}
