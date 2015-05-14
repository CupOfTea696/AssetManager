<?php

if (!function_exists('asset_files')) {
    function asset_files($asset, $type = false)
    {
        $asset_path = trim(config('assets.path', 'assets'), '/');
        $asset = $type ? config('assets.' . $type, $type) . '/' . trim($asset, '/') . '.' . $type;
        $asset = $asset_path . '/' . $asset;
        
        return [
            'full' => $asset,
            'min' => preg_replace('/(.*)(\..+)/', '$1.min$2', $asset_full),
        ];
    }
}

if (!function_exists('asset_exists')) {
    function asset_exists($asset)
    {
        $asset_files = asset_files($asset);
        $production = app()->environment('production');
        
        if (file_exists(public_path($asset_files[$production ? 'min' : 'full'])))
            return $asset_files[$production ? 'min' : 'full'] . '?v=' . md5_file(public_path($asset_files[$production ? 'min' : 'full']));
        
        if (file_exists($asset_files[$production ? 'full' : 'min']))
            return $asset_files[$production ? 'full' : 'min'] . '?v=' . md5_file(public_path($asset_files[$production ? 'full' : 'min']));
        
        return false;
    }
}

if (!function_exists('get_asset')) {
    function get_asset($asset, $type = false)
    {
        $asset_files = asset_files($asset, $type);
        $asset = asset_exists($asset);
        
        if (!$asset) {
            $msg = 'Asset ' . $asset_files['full'] . ' and ' . $asset_files['min'] . ' could not be found.';
            
            if (config('assets.missing') == 'warn') {
                trigger_error($msg, E_USER_WARNING);
                return false;
            } elseif (config('assets.missing', 'comment') == 'comment') {
                return '<!-- ' . $msg . ' -->';
            } else {
                return false;
            }
        }
        
        return config('assets.relative', true) ? $asset : asset($asset);
    }
}

if (!function_exists('css')) {
    function css($asset)
    {
        return get_asset($asset, 'css');
    }
}

if (!function_exists('js')) {
    function js($asset)
    {
        return get_asset($asset, 'js');
    }
}
