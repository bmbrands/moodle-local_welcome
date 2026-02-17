copyright  2017 Bas Brands, Basbrands.nl
author     Bas Brands bas@sonsbeekmedia.nl
license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

CHANGES

version 2026021701 (Release 3.1):

- Added separate welcome messages per authentication method, allowing different
  email templates for each auth plugin. Thanks to Andrei Bautu!
- Added composer.json for Composer-based installations. Thanks to Andrei Bautu!
- Added GitHub Actions CI workflow (moodle-plugin-ci) for automated code
  checking and testing. Thanks to Andrei Bautu!
- Added GitHub Actions workflow for automatic plugin release to the Moodle
  Plugins directory when a tag is pushed.

version 2026021700 (Release 3.0):

- Updated for Moodle 4.5 compatibility (requires Moodle 4.5+).
- Removed deprecated user fields (icq, skype, yahoo, aim, msn, url) that were
  removed from Moodle core in Moodle 4.x.
- Default user fields now sourced from core user_get_default_fields() instead
  of a hardcoded list, ensuring future compatibility.
- Fixed PHPDoc tags: replaced deprecated @package local / @subpackage welcome
  with correct @package local_welcome across all files.
- Fixed privacy provider class description (was referencing block_enrol_duration).
- Fixed typo in index.php URL (/local/welcome/index.php.php).
- Replaced is_siteadmin() check in index.php with require_capability() for
  proper access control.
- Removed return '' statements from void observer method.
- Flattened nested if-block in observer for better readability.
- Updated events.php to use modern short array syntax.
- Added return type declarations to all methods.
- Added comprehensive PHPDoc documentation to all classes and methods.
- Added PHPUnit test coverage for the message class.

version 2021082600:

Privacy provider implementation by Cameron Ball.

version 2017010400:

Updated to API event handlers version 2 for Moodle 3.1 and newer. Thanks to Nadav Kavalerchik!

version 2015100300:

All profile fields / custom profile fields are now available for usage. To see
the available fields navigate to /local/welcome/index.php

version 2015042900:

Added an option to select the authentication methods that will trigger a
welcome message. This way you can use the welcome plugin for manual authentication
and disable it for (for example) email based self registration.

ABOUT

This plugin for Moodle sends a configurable welcome message to new users.

The plugin uses the event system in Moodle and will be triggered when a new
user is created, no matter if this was a manually created account or an
account created using self registration.

REQUIREMENTS

- Moodle 4.5 or later.

SETTINGS

This local plugin allows you to configure:

The email message / subject to the new user
The email message / subject for the moderator / admin
The email address for the moderator / admin
The firstname / lastname of the admin
The authentication plugins that trigger the welcome message


SAMPLE MESSAGE

    Welcome [[fullname]]

    Your Moodle account has been created and you're ready to go! Your account has
    been created with the following details:

    Name: [[fullname]],
    Username: [[username]],
    Firstname: [[firstname]],
    Lastname: [[lastname]],
    Email: [[email]],
    City: [[city]],
    Country: [[country]]

    Feel free to reach out to us at any time through our email.

    Cheers,


AVAILABLE TEMPLATE FIELDS

Default profile fields (sourced from core):
    [[username]], [[fullname]], [[firstname]], [[lastname]], [[email]],
    [[address]], [[phone1]], [[phone2]], [[department]], [[institution]],
    [[interests]], [[idnumber]], [[lang]], [[timezone]], [[description]],
    [[city]], [[country]]

Site fields:
    [[sitelink]], [[sitename]], [[resetpasswordlink]]

Custom profile fields:
    Any custom profile fields defined in your Moodle site are also available
    using [[shortname]] syntax. Visit /local/welcome/index.php to see all
    available fields and their current values.


TESTING

PHPUnit tests are included in the tests/ directory:

    vendor/bin/phpunit local/welcome/tests/message_test.php


INSTALLATION

Just place the welcome directory inside your Moodle's local directory.
Install the plugin and browse to:

Site Administration -> Plugins -> Local plugins -> Moodle welcome
