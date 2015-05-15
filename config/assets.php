<?php

/**
 *--------------------------------------------------------------------------
 * Asset Manager Settings
 *--------------------------------------------------------------------------
 *
 */

return [
    
    /**
     *--------------------------------------------------------------------------
     * Path
     *--------------------------------------------------------------------------
     *
     * Assets path within the public folder.
     *
     * @default 'assets'
     */
    'path' => 'assets',
    
    /**
     *--------------------------------------------------------------------------
     * CSS Path
     *--------------------------------------------------------------------------
     *
     * CSS path within the assets folder.
     *
     * @default 'css'
     */
    'css' => 'css',
    
    /**
     *--------------------------------------------------------------------------
     * JS Path
     *--------------------------------------------------------------------------
     *
     * JS path within the assets folder.
     *
     * @default 'js'
     */
    'js' => 'js',
    
    /**
     *--------------------------------------------------------------------------
     * Relative Path
     *--------------------------------------------------------------------------
     *
     * Wether or not the asset function should return the relative or full URL.
     * Useful when using the <base> tag in your <head>
     *
     * @default true
     */
    'relative' => true,
    
    /**
     *--------------------------------------------------------------------------
     * Return HTML
     *--------------------------------------------------------------------------
     *
     * Return the full HTML tag for assets rather than the URL.
     *
     * @default true
     */
    'html' => true,
    
    /**
     *--------------------------------------------------------------------------
     * Missing Asset Action
     *--------------------------------------------------------------------------
     *
     * The action taken when the asset couldn't be found.
     *
     * @default 'comment'
     * @supported:
     *    - 'warn' A warning is thrown. Recommended in development
     *    - 'comment' A HTML comment is returned with an error message.
     *    - 'none' The function just returns false
     */
    'missing' => 'comment',
    
];
