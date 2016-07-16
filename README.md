# Authentication

Authentication package of the CodeCollab project

[![Build Status](https://travis-ci.org/CodeCollab/Authentication.svg?branch=master)](https://travis-ci.org/CodeCollab/Authentication) [![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)](mit) [![Latest Stable Version](https://poser.pugx.org/codecollab/authentication/v/stable)](https://packagist.org/packages/codecollab/authentication) [![Total Downloads](https://poser.pugx.org/codecollab/authentication/downloads)](https://packagist.org/packages/codecollab/authentication) [![Latest Unstable Version](https://poser.pugx.org/codecollab/authentication/v/unstable)](https://packagist.org/packages/codecollab/authentication)

## Requirements

PHP7+

## Installation

Include the library in your project using composer:

```
{
    "require-dev": {
        "codecollab/authentication": "^1"
    }
}
```

## Usage

Creating an instance of ```User``` requires an instance of ```\CodeCollab\Http\Session\Session``` from the [Http Library][http]

### User Authentication

The ```logIn``` function takes as arguments the password from the form, the passowrd hash from the database and the user's information (to be persisted in Session).
```
$user = new User($session);
if ($user->logIn($password_from_form, $hash_from_db, $user_info_from_db)) {
    /** login successful **/
} else {
    /** login failed */
}
```
Assuming there's a "remember me" feature implemented a user can simply be logged in without comparing password hashes.
```
if ($user->logInRememberMe($user_info_from_db)) {
    /** login successful **/
} else {
    /** login failed */
}
```
After a successful login, the user's information can be retrieved depending on what ```$user_info_from_db``` (in above snippet) contained:
```
$user_name = $session->get('user_name');
$user_id = $session->get('user_id');
```

### Login Status

```
if ($user->isLoggedIn() {
    /** User is logged in **/
}
```

### Password Rehash

To check for and rehash (when needed) a logged in user's password:
```
if ($user->needsRehash($hash_from_db)) {
    $new_hash = $user->rehash($password_from_form);
    //save $new_hash to database
}
```

### Logout
```
$user->logOut();
```

## Contributing

[How to contribute][contributing]

## License

[MIT][mit]

[http]: https://github.com/CodeCollab/Http
[contributing]: https://github.com/CodeCollab/Authentication/blob/master/CONTRIBUTING.md
[mit]: http://spdx.org/licenses/MIT
