<?php

/*
 *--------------------------------------------------------------------------
 * Asset Manager Settings
 *--------------------------------------------------------------------------
 *
 */
return [
    /*
     *--------------------------------------------------------------------------
     * Public Path
     *--------------------------------------------------------------------------
     *
     * Path to the public folder.
     * 
     * You do not need to configure this setting if you use Laravel.
     *
     * @default dirname(__DIR__) . '/public'
     */
    'public_path' => dirname(__DIR__) . '/public',
    
    /*
     *--------------------------------------------------------------------------
     * Revision Manifest Path
     *--------------------------------------------------------------------------
     *
     * Path to a revision manifest file.
     * 
     * If the assets is found in the manifest, the Asset Manager will attempt
     * to use the versioned file rather than a hash query string for
     * cache busting. If the versioned asset can't be found, the
     * Asset Manager will fall back to using a query string.
     *
     * @default null
     */
    'manifest' => null,
    
    /*
     *--------------------------------------------------------------------------
     * Path
     *--------------------------------------------------------------------------
     *
     * Assets path within the public folder.
     *
     * @default 'assets'
     */
    'path' => 'assets',
    
    /*
     *--------------------------------------------------------------------------
     * CSS Path
     *--------------------------------------------------------------------------
     *
     * CSS path within the assets folder.
     *
     * @default 'css'
     */
    'css' => 'css',
    
    /*
     *--------------------------------------------------------------------------
     * JS Path
     *--------------------------------------------------------------------------
     *
     * JS path within the assets folder.
     *
     * @default 'js'
     */
    'js' => 'js',
    
    /*
     *--------------------------------------------------------------------------
     * Relative Path
     *--------------------------------------------------------------------------
     *
     * Wether or not the asset function should return the relative or full URL.
     *
     * @default true
     */
    'relative' => true,
    
    /*
     *--------------------------------------------------------------------------
     * Return HTML
     *--------------------------------------------------------------------------
     *
     * Return the full HTML tag for assets rather than the URL.
     *
     * @default true
     */
    'html' => true,
    
    /*
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
