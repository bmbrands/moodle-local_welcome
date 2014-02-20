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
 * This plugin sends users a welcome message after logging in
 * and notify a moderator a new user has been added
 * it has a settings page that allow you to configure the messages
 * send.
 *
 * @package    local
 * @subpackage welcome
 * @copyright  2013 Bas Brands, Basbrands.nl
 * @author     Bas Brands bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function send_welcome($user) {
    global $CFG;

    $moderator = get_admin();
    $sender = get_admin();

    if (!empty($user->email)) {

        $moderator->email = get_config('local_welcome', 'moderator_email');

        $sender->email = get_config('local_welcome', 'sender_email');
        $sender->firstname = get_config('local_welcome', 'sender_firstname');
        $sender->lastname = get_config('local_welcome', 'sender_lastname');

        $message_user_enabled = get_config('local_welcome', 'message_user_enabled');
        $message_user = get_config('local_welcome', 'message_user');
        $message_user_subject = get_config('local_welcome', 'message_user_subject');

        $message_moderator_enabled = get_config('local_welcome', 'message_moderator_enabled');
        $message_moderator = get_config('local_welcome', 'message_moderator');
        $message_moderator_subject = get_config('local_welcome', 'message_moderator_subject');

        $fields = array(
        	'[[fullname]]',
        	'[[username]]',
        	'[[firstname]]',
        	'[[lastname]]',
        	'[[email]]',
        	'[[city]]',
            '[[country]]');
        $values = array(
            fullname($user),
            $user->username,
            $user->firstname,
            $user->lastname,
            $user->email,
            $user->city,
            get_string($user->country, 'countries'));

        $message_user = str_replace($fields, $values, $message_user);
        $message_user_subject = str_replace($fields, $values, $message_user_subject);
        $message_moderator = str_replace($fields, $values, $message_moderator);
        $message_moderator_subject = str_replace($fields, $values, $message_moderator_subject);

        if (!empty($message_user) && !empty($sender->email) && $message_user_enabled) {
            email_to_user($user, $sender, $message_user_subject, $message_user, $message_user);
        }

        if (!empty($message_moderator) && !empty($sender->email) && $message_moderator_enabled) {
            email_to_user($moderator, $sender, $message_moderator_subject, $message_moderator, $message_moderator);
        }

    }
}