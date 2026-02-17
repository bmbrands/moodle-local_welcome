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
 * PHPUnit tests for local_welcome message class.
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
 * Tests for the message template handler.
 *
 * @package    local_welcome
 * @copyright  2017 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_welcome\message
 */
final class message_test extends \advanced_testcase {
    /**
     * Test that the message class can be instantiated.
     */
    public function test_constructor(): void {
        $this->resetAfterTest();

        $message = new message();

        $this->assertIsArray($message->defaultfields);
        $this->assertIsArray($message->welcomefields);
        $this->assertIsArray($message->welcomevalues);
        $this->assertIsArray($message->customfields);
    }

    /**
     * Test that default fields are a subset of core user_get_default_fields.
     */
    public function test_default_fields_from_core(): void {
        $this->resetAfterTest();

        $message = new message();
        $corefields = user_get_default_fields();

        foreach ($message->defaultfields as $field) {
            $this->assertContains(
                $field,
                $corefields,
                "Field '{$field}' should exist in core user_get_default_fields()"
            );
        }
    }

    /**
     * Test that deprecated fields are not present.
     */
    public function test_deprecated_fields_removed(): void {
        $this->resetAfterTest();

        $message = new message();
        $deprecatedfields = ['icq', 'skype', 'yahoo', 'aim', 'msn', 'url'];

        foreach ($deprecatedfields as $field) {
            $this->assertNotContains(
                $field,
                $message->defaultfields,
                "Deprecated field '{$field}' should not be in default fields"
            );
        }
    }

    /**
     * Test that expected core fields are present.
     */
    public function test_expected_fields_present(): void {
        $this->resetAfterTest();

        $message = new message();
        $expectedfields = ['username', 'fullname', 'firstname', 'lastname', 'email', 'city', 'country'];

        foreach ($expectedfields as $field) {
            $this->assertContains(
                $field,
                $message->defaultfields,
                "Expected field '{$field}' should be in default fields"
            );
        }
    }

    /**
     * Test that welcome fields contain expected entries.
     */
    public function test_welcome_fields(): void {
        $this->resetAfterTest();

        $message = new message();

        $this->assertContains('sitelink', $message->welcomefields);
        $this->assertContains('sitename', $message->welcomefields);
        $this->assertContains('resetpasswordlink', $message->welcomefields);
    }

    /**
     * Test that welcome values are populated.
     */
    public function test_welcome_values(): void {
        $this->resetAfterTest();

        $message = new message();

        $this->assertArrayHasKey('sitelink', $message->welcomevalues);
        $this->assertArrayHasKey('sitename', $message->welcomevalues);
        $this->assertArrayHasKey('resetpasswordlink', $message->welcomevalues);

        $this->assertNotEmpty($message->welcomevalues['sitename']);
        $this->assertNotEmpty($message->welcomevalues['resetpasswordlink']);
    }

    /**
     * Test getting default values for a user.
     */
    public function test_get_user_default_values(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'username' => 'testuser',
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'testuser@example.com',
            'city' => 'Amsterdam',
            'country' => 'NL',
        ]);

        $message = new message();
        $values = $message->get_user_default_values($user);

        $this->assertEquals('testuser', $values['username']);
        $this->assertEquals('Test', $values['firstname']);
        $this->assertEquals('User', $values['lastname']);
        $this->assertEquals('testuser@example.com', $values['email']);
        $this->assertEquals('Amsterdam', $values['city']);
        $this->assertEquals('Test User', $values['fullname']);
        // Country should be translated to full name.
        $this->assertEquals(get_string('NL', 'countries'), $values['country']);
    }

    /**
     * Test getting default values for a user with empty fields.
     */
    public function test_get_user_default_values_empty_fields(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'username' => 'minimal',
            'firstname' => 'Min',
            'lastname' => 'Imal',
            'email' => 'minimal@example.com',
        ]);

        $message = new message();
        $values = $message->get_user_default_values($user);

        $this->assertEquals('minimal', $values['username']);
        $this->assertEquals('Min Imal', $values['fullname']);
        // Empty fields should return empty string.
        $this->assertArrayHasKey('city', $values);
    }

    /**
     * Test getting custom profile field values.
     */
    public function test_get_user_custom_values(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $message = new message();
        $values = $message->get_user_custom_values($user);

        // Should return an array (empty if no custom fields defined).
        $this->assertIsArray($values);
    }

    /**
     * Test replacing placeholders in a message.
     */
    public function test_replace_values(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'username' => 'jandevries',
            'firstname' => 'Jan',
            'lastname' => 'de Vries',
            'email' => 'jan@example.com',
            'city' => 'Rotterdam',
        ]);

        $message = new message();

        $template = 'Hello [[fullname]], your username is [[username]] and email is [[email]]. ' .
                    'Welcome to [[sitename]].';

        $result = $message->replace_values($user, $template);

        $this->assertStringContainsString('Jan de Vries', $result);
        $this->assertStringContainsString('jandevries', $result);
        $this->assertStringContainsString('jan@example.com', $result);
        // Should not contain unreplaced placeholders for known fields.
        $this->assertStringNotContainsString('[[fullname]]', $result);
        $this->assertStringNotContainsString('[[username]]', $result);
        $this->assertStringNotContainsString('[[email]]', $result);
        $this->assertStringNotContainsString('[[sitename]]', $result);
    }

    /**
     * Test that unknown placeholders are left untouched.
     */
    public function test_replace_values_unknown_placeholder(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $message = new message();

        $template = 'Hello [[fullname]], [[nonexistentfield]]!';
        $result = $message->replace_values($user, $template);

        // Unknown placeholders should remain in the message.
        $this->assertStringContainsString('[[nonexistentfield]]', $result);
        // Known ones should be replaced.
        $this->assertStringNotContainsString('[[fullname]]', $result);
    }

    /**
     * Test replace_values with an HTML message template.
     */
    public function test_replace_values_html(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'firstname' => 'Piet',
            'lastname' => 'Mondriaan',
            'email' => 'piet@example.com',
        ]);

        $message = new message();

        $template = '<h1>Welcome [[fullname]]</h1><p>Email: [[email]]</p>' .
                    '<p>Visit [[sitelink]]</p>';

        $result = $message->replace_values($user, $template);

        $this->assertStringContainsString('Piet Mondriaan', $result);
        $this->assertStringContainsString('piet@example.com', $result);
        $this->assertStringNotContainsString('[[sitelink]]', $result);
        // The result should still contain valid HTML.
        $this->assertStringContainsString('<h1>', $result);
    }

    /**
     * Test that the reset password link is included in welcome values.
     */
    public function test_reset_password_link(): void {
        $this->resetAfterTest();

        $message = new message();

        $this->assertStringContainsString(
            'forgot_password.php',
            $message->welcomevalues['resetpasswordlink']
        );
    }

    /**
     * Test replace_values with empty message.
     */
    public function test_replace_values_empty_message(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $message = new message();

        $result = $message->replace_values($user, '');

        $this->assertEquals('', $result);
    }

    /**
     * Test replace_values with message containing no placeholders.
     */
    public function test_replace_values_no_placeholders(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $message = new message();

        $template = 'This is a plain message with no placeholders.';
        $result = $message->replace_values($user, $template);

        $this->assertEquals($template, $result);
    }
}
