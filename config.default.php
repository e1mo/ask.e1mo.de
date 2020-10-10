<?php

return [
  'page' => [
    'language' => 'en', // two digits as of ISO 639-1
    'title' => 'Ask me something random!',
    'description' => 'Feel free to ask me anything. Please just be reasonably polite.',
		'debug' => false,
  ],
  'owner' => [
    'name' => '',
    'e-mail' => 'owner@ask.example.com',
    'recipient-choices' => [
      'enabled' => false,
      'choices' => [
        //'domain' => [
        //  'myname.info',
        //  'my.org',
        //],
        //'user' => [
        //  'hello',
        //  'ask',
        //  'awesome'
        //]
      ]
    ],
    'allow-bad-recipient' => false, // Set to true if you want to skip PHPMailers E-Mail validation (e.g. name only recipients for local test sendmail setup)
  ],
  'message' => [
    'subject' => 'New message for you',
    'sender' => 'ask@example.com',
    'force-sender' => false, // Always set it to sender even if a senders E-Mail is given
  ],
  'form' => [
    'message' => [
      'label' => 'Your Message to me',
      'placeholder' => 'I\'ve always wanted to tell you...',
    ],
    'submit' => [
      'label' => 'Send it!',
    ],
    'sender' => [
      'label' => 'This is optional.',
      'email' => [
        'label' => 'Your E-Mail (optional)',
        'palceholder' => 'person@example.com',
      ],
      'name' => [
        'label' => 'Your Name (optional)',
        'placeholder' => 'Lucky Luke',
      ]
    ],
		'recipient' => [
			'label' => 'Whom to send the message to?'
		]
  ],
  'email' => [
    'provider' => 'sendmail',
    'smtp' => [
      'host' => '',
      'port' => 465,
      'crypt' => 'smtps',
      'user' => '',
      'pass' => ''
    ],
  ],
  'modules' => [ // Different modules that are included at different parts of the process. Must be present in modules/<name>/ and provide a main.php
    'headers' => [] // Must return an array with all custom headers to be added
  ]
];
