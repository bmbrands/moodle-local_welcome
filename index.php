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
 * @copyright  2017 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$context = context_system::instance();

require_login();
if (!is_siteadmin()) {
    return '';
}
$welcome = new \local_welcome\message();

$PAGE->set_context($context);
$PAGE->set_url('/local/welcome/index.php.php');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'local_welcome'));
$PAGE->navbar->add(get_string('pluginname', 'local_welcome'));

$tableheader = array(
    get_string('fieldname', 'local_welcome'),
    get_string('yourvalue', 'local_welcome'));

$customfields = $welcome->customfields;
$customvalues = $welcome->get_user_custom_values($USER);

// Custom profile Fields.
$tablecustom = new html_table();
$tablecustom->head = $tableheader;

foreach ($customfields as $field) {
    $tablecustom->data[] = array('[['.$field.']]', $customvalues[$field]);
}

// Moodle welcome template Fields.
$tablewelcome = new html_table();
$tablecustom->head = $tableheader;

foreach ($welcome->welcomefields as $field) {
    $tablewelcome->data[] = array('[['.$field.']]', $welcome->welcomevalues[$field]);
}

// Moodle default user template Fields.
$tabledefault = new html_table();
$tabledefault->head = $tableheader;
$userdefaultvalues = $welcome->get_user_default_values($USER);

foreach ($welcome->defaultfields as $field) {
    $tabledefault->data[] = array('[['.$field.']]', $userdefaultvalues[$field]);
}

$editurl = new moodle_url('/admin/settings.php', array('section' => 'local_welcome'));

echo $OUTPUT->header();

echo html_writer::tag('h2', get_string('pluginname', 'local_welcome'));
echo html_writer::tag('p', get_string('globalhelp', 'local_welcome'));
echo $OUTPUT->single_button($editurl, get_string('configure', 'local_welcome'));

echo html_writer::tag('h2', get_string('customprofilefields', 'local_welcome'));
echo html_writer::table($tablecustom);

echo html_writer::tag('h2', get_string('welcomefields', 'local_welcome'));
echo html_writer::table($tablewelcome);

echo html_writer::tag('h2', get_string('defaultprofilefields', 'local_welcome'));
echo html_writer::table($tabledefault);
echo $OUTPUT->footer();