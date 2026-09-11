<?php

return [
    'ssr' => [
        'enabled' => false,
    ],
    'testing' => [
        'ensure_pages_exist' => false,
        'page_paths' => [
            resource_path('ts/pages'),
            resource_path('js/Pages'),
            resource_path('js/pages'),
        ],
        'page_extensions' => [
            'js',
            'jsx',
            'svelte',
            'ts',
            'tsx',
            'vue',
        ],
    ],
];
