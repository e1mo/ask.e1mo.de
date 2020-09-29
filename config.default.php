<?php

return [
    'page' => [
        'language' => 'en', // two digits as of ISO 639-1
        'title' => '',
        'description' => '',
    ],
    'owner' => [
        'name' => '',
        'e-mail' => '',
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
    'debounce' => true, // TODO: Avoid duplicate sending
    'email' => [ // TODO: Chose between php native mail and SMTP
        'provider' => 'sendmail', // TODO: Sendmail (native mail) or SMTP (PHPMailer)
        'smtp' => [ // TODO: Optional, required for smtp 

        ],
    ],
];
