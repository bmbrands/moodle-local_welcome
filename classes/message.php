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
 * Message template handler for local_welcome.
 *
 * Manages template field replacement for welcome emails sent to new users
 * and moderators. Supports default user fields, custom profile fields,
 * and site-level welcome fields.
 *
 * @package    local_welcome
 * @copyright  2017 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_welcome;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Message template handler class.
 *
 * @package    local_welcome
 * @copyright  2017 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message {

    /** @var array Default user profile field names. */
    public $defaultfields;

    /** @var array Welcome template field names (site-level). */
    public $welcomefields;

    /** @var array Welcome template field values. */
    public $welcomevalues;

    /** @var array Custom profile field shortnames. */
    public $customfields;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->defaultfields = $this->get_default_fields();
        $this->welcomefields = $this->get_welcome_fields();
        $this->welcomevalues = $this->get_welcome_values();
        $this->customfields = $this->get_custom_fields();
    }

    /**
     * Get the list of default user fields available for template replacement.
     *
     * Uses user_get_default_fields() from core and filters to fields that
     * are meaningful for welcome email templates.
     *
     * @return array List of default user field names.
     */
    private function get_default_fields(): array {
        // Get fields from core and filter to those useful in welcome messages.
        $corefields = user_get_default_fields();

        // Fields from core that make sense in a welcome email template.
        $templatefields = [
            'username', 'fullname', 'firstname', 'lastname', 'email',
            'address', 'phone1', 'phone2', 'department',
            'institution', 'interests', 'idnumber', 'lang', 'timezone',
            'description', 'city', 'country',
        ];

        return array_values(array_intersect($templatefields, $corefields));
    }

    /**
     * Get the list of welcome-specific template fields.
     *
     * These are site-level fields not tied to a user profile.
     *
     * @return array List of welcome field names.
     */
    private function get_welcome_fields(): array {
        return ['sitelink', 'sitename', 'resetpasswordlink'];
    }

    /**
     * Get the list of custom profile field shortnames.
     *
     * @return array List of custom profile field shortnames.
     */
    private function get_custom_fields(): array {
        $customfields = profile_get_custom_fields(true);
        $returnfields = array();
        foreach ($customfields as $field) {
            $returnfields[] = $field->shortname;
        }
        return $returnfields;
    }

    /**
     * Get default profile field values for a user.
     *
     * @param \stdClass $user The user object.
     * @return array Associative array of field name => value.
     */
    public function get_user_default_values($user): array {
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

    /**
     * Get custom profile field values for a user.
     *
     * @param \stdClass $user The user object.
     * @return array Associative array of field shortname => value.
     */
    public function get_user_custom_values($user): array {
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

    /**
     * Get values for welcome-specific template fields.
     *
     * @return array Associative array of field name => rendered value.
     */
    public function get_welcome_values(): array {
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

    /**
     * Replace all template placeholders in a message string.
     *
     * Replaces [[fieldname]] placeholders with actual values from the user
     * profile, custom profile fields, and welcome fields.
     *
     * @param \stdClass $user The user object.
     * @param string $message The message template containing [[field]] placeholders.
     * @return string The message with all placeholders replaced.
     */
    public function replace_values($user, $message): string {
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
