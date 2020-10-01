<?php

return [
    'page' => [
        'language' => 'en', // two digits as of ISO 639-1
        'title' => '',
        'description' => '',
    ],
    'owner' => [
        'name' => '',
        'e-mail' => 'owner@ask.example.com',
        'recipient-choices' => [
            'enabled' => false,
            'choices' => [
                //'domain1' => [
                //    'ask', // => ask@domain1
                //    'awesome', // => awesome@domain1
                //]
            ]
        ],
        'allow-bad-recipient' => false, // Set to tue if you want to skip PHPMailers E-Mail validation (e.g. name only recipients for local test sendmail setup)
    ],
    'message' => [
        'subject' => 'New Message for you',
        'sender' => '',
        'force-sender' => false, // Always set it to sender even if a senders E-Mail is given
    ],
    'form' => [
        'message' => [
            'label' => 'Your Message for me',
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
    'modules' => [ // Different modules taht are included at differenc parts of the process. Must be present in modules/<name>/ and provide a main.php
        'headers' => [] // Must return an array with all custom headers to be added
    ]
];
