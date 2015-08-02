<?php

/**
 * Fallback for non-laravel applications.
 */
function cupoftea_asset_manager_load(){
    static $loaded = false;
    
    if ($loaded) {
        return;
    }
    
    if (\Illuminate\Support\Facades\Facade::getFacadeApplication() === null) {
        $cupoftea_asset_manager_application = new \Illuminate\Container\Container;
        \Illuminate\Support\Facades\Facade::setFacadeApplication($cupoftea_asset_manager_application);
    }
    
    if (! \Illuminate\Support\Facades\Facade::getFacadeApplication()->bound('CupOfTea\AssetManager\Contracts\Provider')) {
        \Illuminate\Support\Facades\Facade::getFacadeApplication()->bindShared('CupOfTea\AssetManager\Contracts\Provider', function($app) {
            return new \CupOfTea\AssetManager\AssetManager();
        });
    }
    
    if (!class_exists('Asset')) {
        class_alias('\\CupOfTea\\AssetManager\\Facades\\Asset', 'Asset');
    }
    
    $loaded = true;
}

/**
 * Asset Manager helpers.
 */

if (!function_exists('asset_exists')) {
    function asset_exists($asset, $type = false)
    {
        cupoftea_asset_manager_load();
        
        return Asset::exists($asset, $type);
    }
}

if (!function_exists('get_asset')) {
    function get_asset($asset, $type = false)
    {
        cupoftea_asset_manager_load();
        
        return Asset::get($asset, $type);
    }
}

if (!function_exists('css')) {
    function css($asset, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::css($asset, $html);
    }
}

if (!function_exists('js')) {
    function js($asset, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::js($asset, $html);
    }
}

if (!function_exists('cdn')) {
    function cdn($cdn, $fallback)
    {
        cupoftea_asset_manager_load();
        
        return Asset::cdn($cdn, $fallback);
    }
}

if (!function_exists('cdn_css')) {
    function cdn_css($cdn, $fallback, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::cdn_css($cdn, $fallback, $html);
    }
}

if (!function_exists('cdn_js')) {
    function cdn_js($cdn, $fallback, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::cdn_js($cdn, $fallback, $html);
    }
}
