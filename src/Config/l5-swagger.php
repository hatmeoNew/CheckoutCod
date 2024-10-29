<?php
return [
    'checkoutcod' => [
        'api' => [
            'title' => 'CheckoutCod API Documentation',
        ],

        'routes' => [
            /*
                * Route for accessing api documentation interface
            */
            'api'             => 'api/checkoutcod/documentation',
            'docs'            => storage_path('api-docs/checkoutcod'),
            'oauth2_callback' => 'api/checkoutcod/oauth2-callback',
        ],
        'paths' => [
            /*
                * Edit to include full URL in ui for assets
            */
            'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

            /*
                * File name of the generated json documentation file
            */
            'docs_json' => 'api-checkoutcod-docs.json',

            /*
                * File name of the generated YAML documentation file
            */
            'docs_yaml' => 'api-checkoutcod-docs.yaml',

            /*
            * Set this to `json` or `yaml` to determine which documentation file to use in UI
            */
            'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

            /*
                * Absolute paths to directory containing the swagger annotations are stored.
            */
            'annotations' => [
                base_path('vendor/nexa-merchant/checkoutcod/src/Docs/V1/CheckoutCod'),

            ],

        ],
    ]
];