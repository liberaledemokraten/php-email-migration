# PHP E-Mail Migration

## General Infos
This project is an email migration tool written in PHP utilizing [imapsync](https://github.com/imapsync/imapsync). It is a proof of concept you may edit and use as you please under the terms granted by the license.


## Requirements

* A Webserver + PHP
* [imapsync](https://github.com/imapsync/imapsync)
* [sed](https://en.wikipedia.org/wiki/Sed) Linux/Unix command line tool

Tested with PHP 7.4 and imapsync 1.977.

## Installation
Assuming you already have a webserver that can execute PHP scripts up and running, you only have to copy the files in this repository. Thenafter edit `define_vars.php`:

### `$TARGETHOST`
Define the target mail server here.

Example:

```php
$TARGETHOST = "mail.example.org";
```

### `$TRUSTEDDOMAINS`
Define all trusted domain names here. Only email addresses under trusted domains can be migrated.

Example:

```php
$TRUSTEDDOMAINS = [
    "example.org",
    "example.com"
];
```

### `$HOSTS`
Define which mailserver shall be contacted for a specified domain name or provider name. The mapping of domain names to a mailserver is required for the "Auto Detect" option, and the provider for the selectable providers. Also see the included HTML form for more on this.

Example:

```php
$HOSTS = array(
    'example.org' => 'mail.example.org',
    'example.com' => 'imap.example.com',
    'examplemail' => 'imap.example.net',
);
```

### `$USERISEMAIL`
Define whether the username equals to the email address for the specified domain name. If not, the `user` will be used as the username for the email address `user@example.com`.

Example:

```php
$USERISEMAIL = array(
    'example.org' => true,
	'example.com' => true
);
```

### `$BLOCKEDVALUES`
You may want to block some values from being used. Enter them into this array.

Example:

```php
$BLOCKEDVALUES = array(
    '\s',
    '\n',
    ' ',
    'sudo ',
    'su ',
    'rm ',
    '|'
);
```

## Usage
### Request
The usage is pretty simple. Just send a `POST` or `GET` request to the PHP script containing following data:

* `email1` - The source email address
* `email2` - The target email address
* `password1` - The password of the source email address
* `password2` - The password of the target email address
* `host1` - The source email provider (must be predefined), or `false` if it shall be auto detected
* `actionid` - The Action ID a running process can be identified with

Note: Theoretically, you could also send the data in form of cookies as the PHP script is using `$_REQUEST` and not looking for a specific method. But you shouldn't provide such data within cookies.

### Response
Once a request has been sent, of-course an HTTP response will be delivered. Each response code has a different meaning. The remark "(initial)" means that this is a response to the initial request with the aim of starting a process. If that remark is missing, it means that the response is to a specific process identified cia the provided Action ID.

#### HTTP 202 Accepted (initial)
This response means that the request has been accepted and the processing has started. The `Refresh` header as well as the added `url` header specifiy the URL under which you can request updates on the started process. That URL contains the Action ID parameter.

#### HTTP 400 Bad Request (initial)
There was something wrong with the provided data. You may find more details on this within the response body.

#### HTTP 102 Processing
An Action ID was provided, but the process with the delivered Action ID has not been finished yet. The current progress will be displayed and a reload of the site via `Refresh` will be triggered.

#### HTTP 400 Bad Request
The request could not be finished as expected. The entered host or provider may not exist.

#### HTTP 401 Unauthorized
An Action ID was provided and the requested action has been finished. However, the credentials entered, meaning the source email address and password or target email address and password, do not match. The authentication failed with either the source or target host.

#### HTTP 404 Not Found
An Action ID was provided and the status of a started process has been requested, but no such process was found.

#### HTTP 500 Internal Server Error
Well, this could be about anything. It could be that there's something wrong with the PHP script, with your server or some error occured while processing the request. Look into the log entry for the Action ID, if any provided. Also look into your server logs, if the script's log did not help.

#### HTTP 503 Service Unavailable
The maintenance mode is enabled.

## Change Language
You may want to offer the migration in a different language. To do so, you can simply translate the strings into your language and modify `headers.php` accordingly.

## Maintenance Mode
You might have to turn off the service or put it into a maintenance mode. That is possible by uncommenting the following line in `migrate.php`:

```php
// include 'maintenance.php';
```

You should consider editing the content in `maintenance.php` before enabling the maintenance mode.

## Authors
The PHP script and HTML form were written by [Kasim Dönmez](https://github.com/mkasimd) on behalf of the Liberale Demokraten - Die Sozialliberalen.

## Copyright
Copyright © 2021 - present Liberale Demokraten - Die Sozialliberalen. Free use of this project's contents is granted under the terms of the GNU GPLv3 license. For the full text of the license, see the [LICENSE](LICENSE) file. The HTML file providing a sample form however is licensed under the Public Domain license such that you can use, modify and distribute it without any limitations of any sorts.

The use of the resources provided by this project shall be done in a way that your modifications to the code, distribution thereof or use of the provided files does not imply any endorsement by the authors or copyright holders of this project. You shall not use this project or contents thereof in the name of the authors or copyright holders unless explicitly permitted.
