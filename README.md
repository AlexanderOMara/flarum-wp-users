# flarum-wp-users

Flarum WP Users: Use WordPress for user accounts and authentication


# Overview

This is an extension which uses a WordPress install to replace the user account authentication system, keeping the same usernames and emails.

It works by inserting an authentication middleware into Flarum which validates WordPress session cookies, and logs into a Flarum user (creating user if necessary).

Validating WordPress cookies is done by parsing the cookie and connecting to the WordPress database to validate the session; no WordPress code or plugins needed.

Because there is only one session cookie, when the user logs out they are logged out on both WordPress and Flarum.

Also want to use the same avatars that WordPress does? Check out my [flarum-gravatar](https://github.com/AlexanderOMara/flarum-gravatar) extension.


# Installation

1.  Install WordPress.
2.  Install Flarum in a subdirectory of your WordPress install.
    -   If WordPress is installed at `example.com` you might install Flarum at `example.com/forum`.
    -   Check Flarum install documentation for instructions to remove the `public` directory.
    -   Use the same email address for the admin account as you did for WordPress, so they can merge.
3.  Install this Flarum extension.
```
composer require AlexanderOMara/flarum-wp-users
```
4.  Configure the extension with all the values it needs from the WordPress install.


# Notes

This extension prevents regular Flarum users from changing their username, email, or setting a password if one has not been set (users created by this extension do not have passwords set). Only an admin can set these values on the Flarum users (this should be avoided).

If a WordPress user's username or email changes, it will update the values on the Flarum user next time they authenticate with Flarum. If there are any conflits, so long as it is with a user that this extension manages, the conflicting user will be modified to a unique value; otherwise the user will not authenticate.

In a pinch you can bypass WordPress and login to the local account directly by adding `#localuser` to the URL bar (possible for Flarum accounts with a password set). This password will not update when the WordPress user changes their password.


# Bugs

If you find a bug or have compatibility issues, please open a ticket under issues section for this repository.


# License

Copyright (c) 2020-2021 Alexander O'Mara

Licensed under the Mozilla Public License, v. 2.0.

If this license does not work for you, feel free to contact me.
