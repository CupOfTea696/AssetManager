<?php

return [
    'public_path' => dirname(dirname(dirname(dirname(__DIR__)))) . '/public',
    'paths' => ['default' => 'default', 'groups' => ['default' => 'assets']],
    'manifest' => null,
    'manifest_trim_path' => null,
    'css' => 'css',
    'css_partial_regex' => '.*',
    'css_partial_order' => 'DESC',
    'js' => 'js',
    'relative' => true,
    'html' => true,
    'missing' => 'comment',
];
