copyright  2014 Bas Brands, Basbrands.nl
author     Bas Brands bas@sonsbeekmedia.nl
license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

CHANGES
version 2015042900:

Added an option to select the authentication methods that will trigger a 
welcome message. This way you can use the welcome plugin for manual authentication
an disable it for (for example) email based self registration.

ABOUT

This plugin for Moodle sends a configurable welcome message to new users.

The plugin uses the event system in Moodle and will be triggerd when a new
user is created, no matter if this was a manually created account or an
account created using self registration.

SETTINGS

This local plugin allows you to configure:

The email message / subject to the new user
The email message / subject for the moderator / admin
The email address for the moderator / admin
The firstname / lastname of the admin


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

    Feel free to reach out to us at any time through our email, on twitter or google+

    Cheers,


INSTALLATION

Just place the welcome directory inside your Moodle's local directory.
Install the plugin and browse to:

Site Administration->Plugins->Local plugins->Moodle welcome
