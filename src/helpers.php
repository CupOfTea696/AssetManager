<?php

/**
 * Fallback Laravel helper functions.
 */

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        static $cfg_file = include '../config/assets.php';
        
        $key = str_replace('assets.', '', $key);
        
        return isset($cfg_file[$key]) ? $cfg_file[$key] : $default;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

/**
 * Asset Manager functions.
 */

if (!function_exists('asset_files')) {
    function asset_files($asset, $type = false)
    {
        $asset_path = trim(config('assets.path', 'assets'), '/');
        $asset = $type ? config('assets.' . $type, $type) . '/' . trim($asset, '/') . '.' . $type : $asset;
        $asset = $asset_path . '/' . $asset;
        
        return [
            'full' => $asset,
            'min' => preg_replace('/(.*)(\..+)/', '$1.min$2', $asset),
        ];
    }
}

if (!function_exists('asset_exists')) {
    function asset_exists($asset, $type = false)
    {
        $asset_files = asset_files($asset, $type);
        $production = function_exists('app') ? app()->environment('production') : true;
        
        if (function_exists('public_path') && file_exists(public_path($asset_files[$production ? 'min' : 'full']))) {
            return $asset_files[$production ? 'min' : 'full'] . '?v=' . md5_file(public_path($asset_files[$production ? 'min' : 'full']));
        }
        
        if (file_exists($asset_files[$production ? 'full' : 'min'])) {
            return $asset_files[$production ? 'full' : 'min'] . '?v=' . md5_file($asset_files[$production ? 'full' : 'min']);
        }
        
        return false;
    }
}

if (!function_exists('get_asset')) {
    function get_asset($asset, $type = false)
    {
        $asset_files = asset_files($asset, $type);
        $asset = asset_exists($asset, $type);
        
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
    function css($asset, $html = null)
    {
        $html = $html !== null ? $html : config('assets.html', true);
        $asset = get_asset($asset, 'css');
        
        if (! $asset || starts_with($asset, '<!--')) {
            return $asset;
        }
        
        if ($html) {
            return '<link rel="stylesheet" href="' . $asset . '">';
        }
        
        return $asset;
    }
}

if (!function_exists('js')) {
    function js($asset, $html = null)
    {
        $html = $html !== null ? $html : config('assets.html', true);
        $asset = get_asset($asset, 'js');
        
        if (! $asset || starts_with($asset, '<!--')) {
            return $asset;
        }
        
        if ($html) {
            return '<script src="' . $asset . '"></script>';
        }
        
        return $asset;
    }
}

if (!function_exists('cdn')) {
    function cdn($cdn, $fallback)
    {
        if (preg_match('/^\/\//')) {
            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
            $headers = get_headers($protocol . ':' . $cdn);
        } else {
            $headers = get_headers($cdn);
        }
        
        $cdn_available = false;
        foreach ($headers as $header) {
            if (str_contains($header, '200 OK')) {
                $cdn_available = true;
                break;
            }
        }
        
        if ($cdn_available){
            return $cdn;
        }
        
        return value($fallback);
    }
}
