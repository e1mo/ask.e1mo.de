<?php

return [
    'page' => [
        'language' => 'en', // two digits as of ISO 639-1
        'title' => 'Ask e1mo something random',
        'description' => 'On this page, you can ask and tell e1mo everything you\'ve always wanted.<br>' .
        'They will get an E-Mail and reply to it in one or another form trough known channels.',
    ],
    'owner' => [
        'name' => 'e1mo',
        'e-mail' => 'ask@e1mo.de',
    ],
    'message' => [
        'subject' => '[ask.e1mo.de] New Message',
        'sender' => 'ask@e1mo.de',
        'force-sender' => false, // Always set it to sender even if a senders E-Mail is given
    ],
    'form' => [
        'message' => [
            'label' => 'Your Message for e1mo:',
            'placeholder' => 'I\'ve always wanted to tell you...',
        ],
        'submit' => [
            'label' => 'Send it!',
        ],
        'sender' => [
            'label' => 'All of these are optional. If you want, e1mo can contact you back if you provide some means of contacting you.',
            'email' => [
                'label' => 'Your E-Mail (optional)',
                'palceholder' => 'person@example.com',
            ],
            'name' => [
                'label' => 'Your Name (optional)',
                'placeholder' => 'Lucky Luke',
            ]
        ]
    ]
];