<?php
// This file is part of the Local welcome plugin
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
 * @copyright  2014 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function send_welcome($user) {
    global $CFG, $SITE;

    $moderator = get_admin();
    $sender = get_admin();

    if (!empty($user->email)) {

        $config = get_config('local_welcome');

        if (!empty($config->auth_plugins)) {
            $auths = explode(',', $config->auth_plugins);
            if (!in_array($user->auth, $auths)) {
                return '';
            }
        } else {
            return '';
        }

        $moderator->email = $config->moderator_email;

        $sender->email = $config->sender_email;
        $sender->firstname = $config->sender_firstname;
        $sender->lastname = $config->sender_lastname;

        $message_user_enabled = $config->message_user_enabled;
        $message_user = $config->message_user;
        $message_user_subject = $config->message_user_subject;

        $message_moderator_enabled = $config->message_moderator_enabled;
        $message_moderator = $config->message_moderator;
        $message_moderator_subject = $config->message_moderator_subject;


        if (!empty($user->country)) {
            $user->country = get_string($user->country, 'countries');
        } else {
            $user->country = '';
        }

        $sitelink = html_writer::link(new moodle_url('/'), $SITE->fullname);
        $resetpasswordlink = html_writer::link(new moodle_url('/login/forgot_password.php'), get_string('resetpass', 'local_welcome'));

        $fields = array(
        	'[[fullname]]',
        	'[[username]]',
        	'[[firstname]]',
        	'[[lastname]]',
        	'[[email]]',
        	'[[city]]',
            '[[country]]',
            '[[sitelink]]',
            '[[sitename]]',
            '[[resetpasswordlink]]');
        $values = array(
            fullname($user),
            $user->username,
            $user->firstname,
            $user->lastname,
            $user->email,
            $user->city,
            $user->country,
            $sitelink,
            $SITE->fullname,
            $resetpasswordlink);

        $message_user = str_replace($fields, $values, $message_user);
        $message_user_subject = str_replace($fields, $values, $message_user_subject);
        $message_moderator = str_replace($fields, $values, $message_moderator);
        $message_moderator_subject = str_replace($fields, $values, $message_moderator_subject);

        if (!empty($message_user) && !empty($sender->email) && $message_user_enabled) {
            email_to_user($user, $sender, $message_user_subject, html_to_text($message_user), $message_user);
        }

        if (!empty($message_moderator) && !empty($sender->email) && $message_moderator_enabled) {
            email_to_user($moderator, $sender, $message_moderator_subject, html_to_text($message_moderator), $message_moderator);
        }

    }
}