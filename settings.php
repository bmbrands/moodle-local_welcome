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

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    $moderator = get_admin();
    $site = get_site();

    $settings = new admin_settingpage('local_welcome', get_string('pluginname', 'local_welcome'));
    $ADMIN->add('localplugins', $settings);

    $name = 'local_welcome/message_user_enabled';
    $title = get_string('message_user_enabled', 'local_welcome');
    $description = get_string('message_user_enabled_desc', 'local_welcome');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $settings->add($setting);

    $name = 'local_welcome/message_user_subject';
    $default = get_string('default_user_email_subject', 'local_welcome', $site->fullname);
    $title = get_string('message_user_subject', 'local_welcome');
    $description = get_string('message_user_subject_desc', 'local_welcome');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $default = get_string('default_user_email', 'local_welcome', $site->fullname);
    $name = 'local_welcome/message_user';
    $title = get_string('message_user', 'local_welcome');
    $description = get_string('message_user_desc', 'local_welcome');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'local_welcome/message_moderator_enabled';
    $title = get_string('message_moderator_enabled', 'local_welcome');
    $description = get_string('message_moderator_enabled_desc', 'local_welcome');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $settings->add($setting);

    $default = get_string('default_user_email_subject', 'local_welcome', $site->fullname);
    $name = 'local_welcome/message_moderator_subject';
    $title = get_string('message_moderator_subject', 'local_welcome');
    $description = get_string('message_moderator_subject_desc', 'local_welcome');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    $default = get_string('default_moderator_email', 'local_welcome', $site->fullname);
    $name = 'local_welcome/message_moderator';
    $title = get_string('message_moderator', 'local_welcome');
    $description = get_string('message_moderator_desc', 'local_welcome');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);

    $name = 'local_welcome/moderator_email';
    $title = get_string('moderator_email', 'local_welcome');
    $description = get_string('moderator_email_desc', 'local_welcome');
    $setting = new admin_setting_configtext($name, $title, $description, $moderator->email);
    $settings->add($setting);

    $name = 'local_welcome/sender_email';
    $title = get_string('sender_email', 'local_welcome');
    $description = get_string('sender_email_desc', 'local_welcome');
    $setting = new admin_setting_configtext($name, $title, $description, $moderator->email);
    $settings->add($setting);

    $name = 'local_welcome/sender_firstname';
    $title = get_string('sender_firstname', 'local_welcome');
    $description = get_string('sender_firstname_desc', 'local_welcome');
    $setting = new admin_setting_configtext($name, $title, $description, $moderator->firstname);
    $settings->add($setting);

    $name = 'local_welcome/sender_lastname';
    $title = get_string('sender_lastname', 'local_welcome');
    $description = get_string('sender_lastname_desc', 'local_welcome');
    $setting = new admin_setting_configtext($name, $title, $description, $moderator->lastname);
    $settings->add($setting);
}

