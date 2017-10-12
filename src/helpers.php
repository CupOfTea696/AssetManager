<?php

/**
 * Fallback for non-laravel applications.
 */
function cupoftea_asset_manager_load()
{
    static $loaded = false;
    
    if ($loaded) {
        return;
    }
    
    if (\Illuminate\Support\Facades\Facade::getFacadeApplication() === null) {
        $cupoftea_asset_manager_application = new \Illuminate\Container\Container;
        \Illuminate\Support\Facades\Facade::setFacadeApplication($cupoftea_asset_manager_application);
    }
    
    if (! \Illuminate\Support\Facades\Facade::getFacadeApplication()->bound('CupOfTea\AssetManager\Contracts\Provider')) {
        \Illuminate\Support\Facades\Facade::getFacadeApplication()->singleton('CupOfTea\AssetManager\Contracts\Provider', function ($app) {
            return new \CupOfTea\AssetManager\AssetManager();
        });
    }
    
    if (! class_exists('Asset')) {
        class_alias('\\CupOfTea\\AssetManager\\Facades\\Asset', 'Asset');
    }
    
    $loaded = true;
}

/*
 * Asset Manager helpers.
 */

if (! function_exists('asset')) {
    function asset()
    {
        cupoftea_asset_manager_load();
        
        return Asset::getFacadeRoot();
    }
}

if (! function_exists('asset_from')) {
    function asset_from($group)
    {
        cupoftea_asset_manager_load();
        
        return Asset::from($group);
    }
}

if (! function_exists('asset_exists')) {
    function asset_exists($asset, $type = false)
    {
        cupoftea_asset_manager_load();
        
        return Asset::exists($asset, $type);
    }
}

if (! function_exists('asset_exists_in')) {
    function asset_exists_in($group, $asset, $type = false)
    {
        cupoftea_asset_manager_load();
        
        return Asset::from($group)->exists($asset, $type);
    }
}

if (! function_exists('get_asset')) {
    function get_asset($asset, $type = false)
    {
        cupoftea_asset_manager_load();
        
        return Asset::get($asset, $type);
    }
}

if (! function_exists('get_asset_from')) {
    function get_asset_from($group, $asset, $type = false)
    {
        cupoftea_asset_manager_load();
        
        return Asset::from($group)->get($asset, $type);
    }
}

if (! function_exists('get_asset_regex')) {
    function get_asset_regex($regex, $dir, $type = false)
    {
        cupoftea_asset_manager_load();
        
        return Asset::get($regex, $dir, $type);
    }
}

if (! function_exists('get_asset_from_regex')) {
    function get_asset_from_regex($group, $regex, $dir, $type = false)
    {
        cupoftea_asset_manager_load();
        
        return Asset::from($group)->get($regex, $dir, $type);
    }
}

if (! function_exists('css')) {
    function css($asset, $split = false, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::css($asset, $split, $html);
    }
}

if (! function_exists('css_from')) {
    function css_from($group, $asset, $split = false, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::from($group)->css($asset, $split, $html);
    }
}

if (! function_exists('js')) {
    function js($asset, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::js($asset, $html);
    }
}

if (! function_exists('js_from')) {
    function js_from($group, $asset, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::from($group)->js($asset, $html);
    }
}

if (! function_exists('cdn')) {
    function cdn($cdn, $fallback)
    {
        cupoftea_asset_manager_load();
        
        return Asset::cdn($cdn, $fallback);
    }
}

if (! function_exists('cdn_css')) {
    function cdn_css($cdn, $fallback, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::cdn_css($cdn, $fallback, $html);
    }
}

if (! function_exists('cdn_js')) {
    function cdn_js($cdn, $fallback, $html = null)
    {
        cupoftea_asset_manager_load();
        
        return Asset::cdn_js($cdn, $fallback, $html);
    }
}
