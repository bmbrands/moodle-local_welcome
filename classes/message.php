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
 * @package    local_welcome
 * @copyright  2017 Bas Brands
 * @author     Bas Brands, basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_welcome;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');

class message {

    public $defaultfields;
    public $welcomefields;
    public $welcomevalues;
    public $customfields;

    public function __construct() {
        $this->defaultfields = $this->get_default_fields();
        $this->welcomefields = $this->get_welcome_fields();
        $this->welcomevalues = $this->get_welcome_values();
        $this->customfields = $this->get_custom_fields();
    }


    private function get_default_fields() {
        $defaultfields = array('username', 'fullname', 'firstname', 'lastname', 'email',
            'address', 'phone1', 'phone2', 'icq', 'skype', 'yahoo', 'aim', 'msn', 'department',
            'institution', 'interests', 'idnumber', 'lang', 'timezone', 'description',
            'city', 'url', 'country'
        );

        return $defaultfields;
    }

    private function get_welcome_fields() {
        $welcomefields = array('sitelink', 'sitename', 'resetpasswordlink');

        return $welcomefields;
    }

    private function get_custom_fields() {
        $customfields = profile_get_custom_fields(true);
        $returnfields = array();
        foreach ($customfields as $field) {
            $returnfields[] = $field->shortname;
        }
        return $returnfields;
    }

    public function get_user_default_values($user) {
        $values = array();
        foreach ($this->defaultfields as $field) {
            if (isset($user->$field)) {
                $values[$field] = $user->$field;
            } else {
                $values[$field] = '';
            }
            if ($field == 'fullname') {
                $values[$field] = fullname($user);
            }
            if (!empty($user->$field) && $field == 'country') {
                $values[$field]  = get_string($user->country, 'countries');
            }
        }
        return $values;
    }

    public function get_user_custom_values($user) {
        $userinfo = profile_user_record($user->id);
        $values = array();
        foreach ($this->customfields as $field) {
            $fieldname = $field;
            if (isset($userinfo->$fieldname)) {
                $values[$field] = $userinfo->$fieldname;
            } else {
                $values[$field] = '';
            }
        }
        return $values;
    }

    public function get_welcome_values() {
        global $SITE;

        $values = array();
        $sitelink = \html_writer::link(new \moodle_url('/'), $SITE->fullname);
        $sitename = $SITE->fullname;
        $resetpasswordlink = \html_writer::link(
            new \moodle_url('/login/forgot_password.php'), get_string('resetpass', 'local_welcome'));
        foreach ($this->welcomefields as $field) {
            $values[$field] = $$field;
        }
        return $values;
    }

    public function replace_values($user, $message) {
        $cususervars = $this->get_user_custom_values($user);
        $defuservars = $this->get_user_default_values($user);

        foreach ($this->defaultfields as $field) {
            $message = str_replace('[['.$field.']]', $defuservars[$field], $message);
        }

        foreach ($this->customfields as $field) {
            $message = str_replace('[['.$field.']]', $cususervars[$field], $message);
        }

        foreach ($this->welcomefields as $field) {
            $message = str_replace('[['.$field.']]', $this->welcomevalues[$field], $message);
        }
        return $message;

    }
}
