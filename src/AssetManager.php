<?php namespace CupOfTea\AssetManager;

use Closure;
use CupOfTea\Package\Package;
use CupOfTea\AssetManager\Contracts\Provider as ProviderContract;

class AssetManager implements ProviderContract
{
    use Package;
    
    /**
     * Package Info.
     *
     * @const string PACKAGE
     * @const string VERSION
     */
    const PACKAGE = 'CupOfTea/AssetManager';
    const VERSION = '1.3.3';
    
    protected $cfg;
    
    public function __construct($config = null)
    {
        if ($config) {
            $this->cfg = $config;
        } else {
            $this->cfg = include __DIR__ . '/../config/defaults.php';
        }
    }
    
    public function configure($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($this->cfg as $cfg_key => $value) {
                if (isset($key[$cfg_key])) {
                    $this->cfg[$cfg_key] = $key[$cfg_key];
                }
            }
        } elseif (isset($this->cfg[$key])) {
            $this->cfg[$key] = $value;
        }
    }
    
    protected function config($key, $default = null)
    {
        return isset($this->cfg[$key]) ? $this->cfg[$key] : $default;
    }
    
    protected function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
    
    protected function public_path($path = '')
    {
        if (function_exists('public_path')) {
            return public_path($path);
        }
        
        return DIRECTORY_SEPARATOR . trim($this->config('public_path')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
    
    protected function files($asset, $type = false)
    {
        $asset_path = trim($this->config('path', 'assets'), '/');
        $asset = $type ? $this->config($type, $type) . '/' . trim($asset, '/') . '.' . $type : $asset;
        $asset = $asset_path . '/' . $asset;
        
        return [
            'full' => $asset,
            'min' => preg_replace('/(.*)(\..+)/', '$1.min$2', $asset),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function exists($asset, $type = false)
    {
        $asset_files = $this->files($asset, $type);
        $production = function_exists('app') ? app()->environment('production') : true;
        
        if (file_exists($this->public_path($asset_files[$production ? 'min' : 'full']))) {
            return $asset_files[$production ? 'min' : 'full'] . '?v=' . md5_file($this->public_path($asset_files[$production ? 'min' : 'full']));
        }
        
        if (file_exists($this->public_path($asset_files[$production ? 'full' : 'min']))) {
            return $asset_files[$production ? 'full' : 'min'] . '?v=' . md5_file($this->public_path($asset_files[$production ? 'full' : 'min']));
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($asset, $type = false)
    {
        $asset_files = $this->files($asset, $type);
        $asset = $this->exists($asset, $type);
        $root_url = (! empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        
        if (! $asset) {
            $msg = 'Asset ' . $asset_files['full'] . ' and ' . $asset_files['min'] . ' could not be found.';
            
            if ($this->config('missing') == 'warn') {
                trigger_error($msg, E_USER_WARNING);
                
                return false;
            } elseif ($this->config('missing', 'comment') == 'comment') {
                return '<!-- ' . $msg . ' -->';
            } else {
                return false;
            }
        }
        
        return $this->config('relative', true) ? $asset : (function_exists('url') ? url($asset) : $root_url . $asset);
    }
    
    /**
     * {@inheritdoc}
     */
    public function css($asset, $html = null)
    {
        $html = $html !== null ? $html : $this->config('html', true);
        $asset = $this->get($asset, 'css');
        
        if (! $asset || starts_with($asset, '<!--')) {
            return $asset;
        }
        
        if ($html) {
            return '<link rel="stylesheet" href="' . $asset . '">';
        }
        
        return $asset;
    }
    
    /**
     * {@inheritdoc}
     */
    public function js($asset, $html = null)
    {
        $html = $html !== null ? $html : $this->config('html', true);
        $asset = $this->get($asset, 'js');
        
        if (! $asset || starts_with($asset, '<!--')) {
            return $asset;
        }
        
        if ($html) {
            return '<script type="text/javascript" src="' . $asset . '"></script>';
        }
        
        return $asset;
    }
    
    /**
     * {@inheritdoc}
     */
    public function cdn($cdn, $fallback)
    {
        if (preg_match('/^\/\//')) {
            $protocol = ((! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
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
        
        if ($cdn_available) {
            return $cdn;
        }
        
        return $this->value($fallback);
    }
    
    public function cdn_css($cdn, $fallback, $html = null)
    {
        $asset = $this->cdn($cdn, $fallback);
        
        if (! $asset || starts_with($asset, '<!--')) {
            return $asset;
        }
        
        if ($html) {
            return '<link rel="stylesheet" href="' . $asset . '">';
        }
        
        return $asset;
    }
    
    public function cdn_js($cdn, $fallback, $html = null)
    {
        $asset = $this->cdn($cdn, $fallback);
        
        if (! $asset || starts_with($asset, '<!--')) {
            return $asset;
        }
        
        if ($html) {
            return '<script type="text/javascript" src="' . $asset . '"></script>';
        }
        
        return $asset;
    }
}
