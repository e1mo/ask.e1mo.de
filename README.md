# ask.e1mo.de

A more or less simple php application to take in anonymous questions from people you know. It's a bit like [Tellonym](https://en.wikipedia.org/wiki/Tellonym) but hosted on your own platform and based on open and established protocols. If there is a new message for you, you will get an E-Mail. There is no data stored by the application itself. Therefore, it does not require any form of database or writable storage. In future there could even be some sort of automatic PGP encryption for enhanced privacy & security. On the clientside, only CSS is required. There is no JavaScript to make the page unresponsive and intrusive.

It is capable of sending E-Mail either trough SMTP or sendmail.

## Requirements

- PHP, tested with Version 7.4 but should be fine with at least 7.\*
- Webserver (nginx example is provided)
- Some sort of Mail Transfer Agent (MTA)
	- SMTP Server
	- sendmail
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) by [Marcus Bointonis](https://github.com/Synchro) is used to send E-Mails (included as a git submodule)

## Installation

The current stable source code is available at <https://github.com/e1mo/ask.e1mo.de> in the `main` branch. You just need to clone it to a location of your choosing, initialize all git submodules and point your webserver at it.

The following commands show an example installation process to `/var/www/ask.e1mo.de/`:

```bash
$ cd /var/www
$ git clone https://github.com/e1mo/ask.e1mo.de.git
Cloning into 'ask.e1mo.de'...
[...]
$ git submodule init
Submodule 'PHPMailer' (https://github.com/e1mo/PHPMailer.git) registered for path 'PHPMailer'
$ git submodule update
Cloning into '/var/www/ask.e1mo.de/PHPMailer'...
Submodule path 'PHPMailer': checked out 'e9a56bb317c649705956edd71e91a7f895aa9b71'
$ cp config.default.php config.php
$ vim config.php
# You can change vim for any of your favourite editors such as vim, emacs, ed, ...
# A listing of all configuration parameters is further down in the README.md
```

That's it for the basic setup, now you need to configure your webserver. If you want to use the provided [nginx](https://www.nginx.com/) configuration (located at [`res/nginx-vhost.conf`](res/nginx-vhost.conf) simply copy it to your sites directory. Usually it is `/etc/nginx/sites-enabled/`.

```
$ sudo cp res/nginx-vhost.conf /etc/nginx/sites-enabled/ask.e1mo.de.conf
# sudo can be ommited if you are already root
# replace ask.e1mo.de with your domain, same goes for the commands down below
$ sudo vim /etc/nginx/sites-enabled/ask.e1mo.de.conf
# You can change vim for any of your favourite editors such as vim, emacs, ed, ...
$ sudo nginx -t
# Check if everything is correct
# If it throws erros don't proceed but fix the config file
$ sudo systemctl restart nginx
```

If you are using the [apache httpd web server](https://httpd.apache.org/) any basic configuration should do. Only note, that you must manually create a `.htaccess` or something comparable to deny access to the `config.php`! Theoretically it should not display anything, but in case PHP is misconfigured it may leak your SMTP Credentials.

## Configuration

All configuration parameters are read from `config.php`. Values not defined in there will be taken from `config.default.php`. This is a fail-safe, do not edit the defaults file, there is a good chance of it causing errors. It will also be updated with newer releases to avoid errors.

The configuration is divided into these sections, they're explain in more detail down below:

Section | Description
--- | ---
page | Basic information about the page
owner | To whom should the mail go
message | A few parameters about the message being sent
form | Placeholders and labels for the form fields
email | Configure the sending of mails

As a note: The subsection headings define the top-level array key, the keys in the table reference the direct key within the n-th Level array.

For Example, if the heading reads `page` and in the table `lang` the corresponding (trimmed) config looks like this:

```php
<?php

return [
	'page' => [
		'lang' => 'en',
		[...]
	],
	[...]
];
```

If you have deeper level keys, such under `email` the subheading `smtp` and the key `host` it will look like this and so on:

```php
<?php

return [
	'email' => [
		'smtp' => [
			'host' => 'smtp.myprovider.info',
		],
	]
];
```

### page

This section basic parameters of the page. These preferences are located under the `page` key.

key | description | default | type
--- | ----------- | ------- | ----
language | Two digit language code (as of [ISO 639-1](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes)) of your site, only used in the HTML header | `'en'` | string
title | Title tag and heading on the page | `'Ask me something!'` | string
description | HTML Meta description and printed below the heading | `'Feel  free to ask me anything. Please just be reasonably polite.'` | string
debug | Enable PHPs printing of exceptions, ... | `false` | boolean

### owner

This section controls to whom the mail should go.

key | description | default | type
--- | ----------- | ------- | ----
name | The owners name, used in the mail to section | `''` (empty) | string
e-mail | The default E-Mail to send mail to, is the fallback if [recipient-choices](#recipient-choices) are not enabled or invalid | `'owner@ask.example.com'` | string
recipient-choices | Allow users to chose an E-Mail to whom the mail should be delivered, it's a nice gimmick. Full configuration is described down below | `['enabled' => false, 'choices' => []]` | array
allow-bad-recipient | Force a bad recipient address to be accepted (e.g. having no domain part) | `false` | boolean

#### recipient-choices

key | description | default | type
--- | ----------- | ------- | ----
enabled | Enable dropdown(s) below the form to choose a custom recipient | `false` | boolean
choices | The available recipients. Can be either a list with adresses or domain names as keys for another array with the user part listed in there | `[]` | array

Some explanation and examples for the `choices` key:

The simplest configuration looks like this:

Please note that these the layout for this section of the configuration **may be subject to change**. But we'll try to maintain a level of backward compatibility.

```php
'choices' => [
	'me@mysite.org',
	'someone@also.mysite.org',
	'awesome-you@mysite.org'
]
```

In this case **one** drop-down will be present above the submit button. All addresses will be available as separate entries.

```php
'choices' => [
	'domains' => [
		'mysite.org',
		'awesome-you',
		'ask.me'
	],
	'users' => [
		'me',
		'awesome-you',
		'info'
	]
]
```

In this case **two** drop-downs will be present above the submit button. One for the user (the part before the @-sign) and one for the domain. It happens when domain-keys are given, and the values are equal across all domains.

### message

key | description | default | type
--- | ----------- | ------- | ----
subject | Subject of the E-Mail sent to you | `'[ask.e1mo.de] New message for you'` | string
sender | The E-Mail for the from E-Mail header | `'ask@example.com'` | string
force-sender | Always set the from E-Mail to the value of `sender`. If a user provides their E-Mail it will be used as the reply to value. | 'false' | boolean

### form

Every entry, except the submit button, consists of a label and a placeholder, both of them are strings. Labels will be printed above the field, placeholders are present when there is nothing inside an input field.

key | description | default | type
--- | ----------- | ------- | ----
message | The main textarea at the top of the page | `['label' => 'Your message to me', 'placeholder' => 'I\'ve always wanted to tell you...']` | array with strings
submit | The label of the submit button | `['label' => 'Send it!']` | array with strings
sender | Array with label and placeholder for the optional senders name and email | See [sender](#sender) | array
recipient | The label / description printed aboce the recipient choices if enabled | `['label' => 'Whom to send the message to?']` | array with string

#### sender

key | description | default | type
--- | ----------- | ------- | ----
label | Will be printed above the input fields | `'This is optional.'` | string
email | Input field for the senders email, consists of label and placeholder | `['label' => 'Your E-Mail (optional)', 'placeholder' => 'person@example.com']` | array with strings
name | Input field for the senders email, consists of label and placeholder | `['label' => 'Your Name (optional)', 'placeholder' => 'Lucky Luke']` | array with strings

### email

key | description | default | type
--- | ----------- | ------- | ----
provider | How to deliver the E-Mail, available: `sendmail` and `smtp` | `'sendmail'` | string
smtp | Configuration (host, user, password, port, crypt) when using SMTP. See [smtp](#smtp) | `['host' => '', 'port' => 465, 'crypt' => 'smtps', 'user' => '', pass => '']` | array

#### smtp

key | description | default | type
--- | ----------- | ------- | ----
host | IP-Address or FQDN of the outgoing mail server | `''` | string
port | Port for SMTP on the host | `465` | integer
crypt | Encryption when communicating with the smtp-host. Can be `smtps` or `starttls`  | `'smtps'` | string
user | User for authenticating against the smtp-host | `''` | string
pass | Password for authenticating against the smtp-host | `''` | string

### modules

key | description | default | type
--- | ----------- | ------- | ----
headers | Modules for providing custom headers to the E-Mail, add one module name per array item | `[]` | array

See [modules](#modules-1) for further explanation of the different functions a module can support.

## Modules

Modules are still very much WIP. Currently, modules only can add custom headers to the E-Mails sent out. Their layout is very simple. Header-Modules must be listed in the configuration under `['modules']['headers']` to be enabled. It must be listed with the name the module (located in `/modules/`) folder has. For every supported function, the module must provide a corresponding PHP file returning a certain datatype.

In this table are all supported functions with their filenames, return types, and an examples listed:

Function | Explanation | Filename | Data-Type | Example
-------- | ----------- | -------- | --------- | -------
Headers  | Add custom headers to outgoing mail | `headers.php` | Array, keys are the header names and values the values | <https://github.com/e1mo/git-version-headers/blob/main/headers.php>


## LICENSE

This software is released under the bsd 3-clause by [Moritz 'e1mo' Fromm](https://github.com/e1mo)
