<?php namespace CupOfTea\AssetManager;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use CupOfTea\Package\Package;
use InvalidArgumentException;
use CupOfTea\AssetManager\Contracts\Provider as ProviderContract;

class AssetManager implements ProviderContract
{
    use Package;
    
    /**
     * Package Name.
     *
     * @const string
     */
    const PACKAGE = 'CupOfTea/AssetManager';
    
    /**
     * Package Version.
     *
     * @const string
     */
    const VERSION = '1.10.0';
    
    /**
     * Asset Manager configuration.
     *
     * @var string
     */
    protected $cfg;
    
    /**
     * Loaded manifest files.
     *
     * @var array
     */
    protected $manifests = [];
    
    /**
     * Asset Group to get assets from.
     *
     * @var string
     */
    protected $assetGroup;
    
    /**
     * Create a new AssetManager instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->cfg = array_merge(include __DIR__ . '/../config/defaults.php', $config);
        
        $this->assetGroup = $this->config('paths.default');
    }
    
    /**
     * Set a configuration key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function configure($key, $value = null)
    {
        if (is_array($key)) {
            foreach (Arr::dot($this->cfg) as $cfg_key => $currentValue) {
                if ($value = Arr::get($key, $cfg_key)) {
                    $this->configure($cfg_key, $value);
                }
            }
        } elseif (Arr::has($this->cfg, $key)) {
            Arr::set($this->cfg, $key, $value);
        }
    }
    
    /**
     * Get a config value.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return Arr::get($this->cfg, $key, $default);
    }
    
    /**
     * {@inheritdoc}
     */
    public function from($group = null)
    {
        if (! $group) {
            $group = $this->config('paths.default', 'default');
        }
        
        $this->assetGroup = $group;
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function exists($asset, $type = false)
    {
        $asset_files = $this->files($asset, $type);
        $production = function_exists('app') ? app()->environment('production') : true;
        
        if (file_exists($this->public_path($asset_files[$production ? 'min_busted' : 'full_busted']))) {
            return $asset_files[$production ? 'min_busted' : 'full_busted'];
        }
        
        if (file_exists($this->public_path($asset_files[$production ? 'full_busted' : 'min_busted']))) {
            return $asset_files[$production ? 'full_busted' : 'min_busted'];
        }
        
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
        
        // Reset assetGroup to default.
        $this->from();
        
        if (! $asset) {
            $msg = 'Asset ' . $asset_files['full'] . ' and ' . $asset_files['min'] . ' could not be found.';
            
            if ($this->config('missing') == 'warn') {
                trigger_error($msg, E_USER_WARNING);
                
                return false;
            }
            
            if ($this->config('missing', 'comment') == 'comment') {
                return '<!-- ' . $msg . ' -->';
            }
            
            return false;
        }
        
        return $this->config('relative', true) ? $asset : (function_exists('url') ? url($asset) : $root_url . $asset);
    }
    
    public function getRegex($regex, $dir, $type = false)
    {
        $asset_groups = $this->regexFiles($regex, $dir, $type);
        $production = function_exists('app') ? app()->environment('production') : true;
        $root_url = (! empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        
        if (! count($asset_groups)) {
            $msg = 'No assets found matching ' . $regex . ' inside ' . $dir . '.';
            
            if ($this->config('missing') == 'warn') {
                trigger_error($msg, E_USER_WARNING);
                
                return false;
            }
            
            if ($this->config('missing', 'comment') == 'comment') {
                return ['<!-- ' . $msg . ' -->'];
            }
            
            return [];
        }
        
        $src = function ($asset) {
            return $this->config('relative', true) ? $asset : (function_exists('url') ? url($asset) : $root_url . $asset);
        };
        
        return array_map(function ($asset_files) use ($src, $production) {
            if (! empty($asset_files[$production ? 'min_busted' : 'full_busted'])) {
                return $src($asset_files[$production ? 'min_busted' : 'full_busted']);
            }
            
            if (! empty($asset_files[$production ? 'full_busted' : 'min_busted'])) {
                return $src($asset_files[$production ? 'full_busted' : 'min_busted']);
            }
            
            if (! empty($asset_files[$production ? 'min' : 'full'])) {
                return $src($asset_files[$production ? 'min' : 'full'] . '?v=' . md5_file($this->public_path($asset_files[$production ? 'min' : 'full'])));
            }
            
            return $src($asset_files[$production ? 'full' : 'min'] . '?v=' . md5_file($this->public_path($asset_files[$production ? 'full' : 'min'])));
        }, $asset_groups);
    }
    
    /**
     * {@inheritdoc}
     */
    public function css($asset, $split = false, $html = null)
    {
        $html = $html !== null ? $html : $this->config('html', true);
        
        if ($split) {
            $regex = '/' . preg_quote($asset) . '(' . $this->config('css_partial_regex', '.*') . ')' . '/';
            $dir = 'css' . (($dirname = dirname($asset)) == '.' ? '' : '/' . $dirname);
            
            try {
                $assets = array_map(function ($asset) use ($html, $regex) {
                    if (! $asset || Str::startsWith($asset, '<!--')) {
                        return [
                            'asset' => $asset,
                            'order' => null,
                        ];
                    }
                    
                    $asset = '/' . ltrim($asset, '/');
                    
                    preg_match($regex, $asset, $matches);
                    
                    $asset = [
                        'asset' => $asset,
                        'orderby' => $matches[1],
                    ];
                    
                    if ($html) {
                        $asset['asset'] = '<link rel="stylesheet" href="' . $asset['asset'] . '">';
                    }
                    
                    return $asset;
                }, $this->getRegex($regex, $dir, 'css'));
            } catch (InvalidArgumentException $e) {
                if ($this->config('missing') == 'warn') {
                    trigger_error($e->getMessage(), E_USER_WARNING);
                    
                    return false;
                }
                
                if ($this->config('missing', 'comment') == 'comment') {
                    return ['<!-- ' . $e->getMessage() . ' -->'];
                }
                
                return [];
            }
            
            usort($assets, function ($a, $b) {
                if (strtolower($this->config('css_partial_order', 'desc')) == 'desc') {
                    return strcmp($b['orderby'], $a['orderby']);
                }
                
                return strcmp($a['orderby'], $b['orderby']);
            });
            
            $assets = array_map(function ($asset) {
                return $asset['asset'];
            }, $assets);
            
            return $assets;
        }
        
        $asset = $this->get($asset, 'css');
        
        if (! $asset || Str::startsWith($asset, '<!--')) {
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
        
        if (! $asset || Str::startsWith($asset, '<!--')) {
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
            if (Str::contains($header, '200 OK')) {
                $cdn_available = true;
                break;
            }
        }
        
        if ($cdn_available) {
            return $cdn;
        }
        
        return value($fallback);
    }
    
    /**
     * Get a CSS Asset from a CDN.
     *
     * @param  string  $cdn
     * @param  string|Closure  $fallback
     * @param  bool|null  $html
     * @return mixed
     */
    public function cdn_css($cdn, $fallback, $html = null)
    {
        $html = $html !== null ? $html : $this->config('html', true);
        $asset = $this->cdn($cdn, $fallback);
        
        if (! $asset || Str::startsWith($asset, '<!--')) {
            return $asset;
        }
        
        if ($html) {
            return '<link rel="stylesheet" href="' . $asset . '">';
        }
        
        return $asset;
    }
    
    /**
     * Get a JS Asset from a CDN.
     *
     * @param  string  $cdn
     * @param  string|Closure  $fallback
     * @param  bool|null  $html
     * @return mixed
     */
    public function cdn_js($cdn, $fallback, $html = null)
    {
        $html = $html !== null ? $html : $this->config('html', true);
        $asset = $this->cdn($cdn, $fallback);
        
        if (! $asset || Str::startsWith($asset, '<!--')) {
            return $asset;
        }
        
        if ($html) {
            return '<script type="text/javascript" src="' . $asset . '"></script>';
        }
        
        return $asset;
    }
    
    /**
     * Get the configured revision manifest.
     *
     * @return array|bool
     */
    protected function getManifest()
    {
        $manifestFile = $this->config('manifest');
        
        if (! empty($this->manifest[$manifestFile])) {
            return $this->manifest[$manifestFile];
        }
        
        $realFile = $this->public_path($manifestFile);
        
        if ($manifestFile && file_exists($realFile)) {
            return $this->manifest[$manifestFile] = json_decode(file_get_contents($realFile), true);
        }
        
        return false;
    }
    
    /**
     * Get possible file names for an asset.
     *
     * @param  string  $asset
     * @param  string|false  $type
     * @return array
     */
    protected function files($asset, $type = false)
    {
        $asset_path = trim(Str::startsWith($asset, '/') ? '' : $this->config('paths.groups.' . $this->assetGroup, 'assets'), '/');
        $asset = $type ? $this->config($type, $type) . '/' . trim($asset, '/') . '.' . $type : trim($asset, '/');
        $asset = ($asset_path ? '/' . $asset_path : '') . '/' . $asset;
        
        $files = [
            'full_busted' => '__NO_HARD_CACHE_BUST__',
            'full' => $asset,
            'min_busted' => '__NO_HARD_CACHE_BUST__',
            'min' => preg_replace('/(.*)(\..+)/', '$1.min$2', $asset),
        ];
        
        if ($manifest = $this->getManifest()) {
            $trimPath = trim($this->config('manifest_trim_path'), '/');
            
            $assetFull = trim($files['full'], '/');
            $assetFullNoPath = trim(str_replace($trimPath, '', $assetFull), '/');
            
            if (! empty($manifest[$assetFullNoPath])) {
                $files['full_busted'] = ($trimPath ? '/' . $trimPath : '') . '/' . trim($manifest[$assetFullNoPath], '/');
            } elseif (! empty($manifest[$assetFull])) {
                $files['full_busted'] = '/' . trim($manifest[$assetFull], '/');
            }
            
            $assetMin = trim($files['min'], '/');
            $assetMinNoPath = trim(str_replace($trimPath, '', $assetMin), '/');
            
            if (! empty($manifest[$assetMinNoPath])) {
                $files['min_busted'] = ($trimPath ? '/' . $trimPath : '') . '/' . trim($manifest[$assetMinNoPath], '/');
            } elseif (! empty($manifest[$assetMin])) {
                $files['min_busted'] = '/' . trim($manifest[$assetMin], '/');
            }
        }
        
        return $files;
    }
    
    protected function regexFiles($regex, $dir, $type = false)
    {
        $asset_path = trim(Str::startsWith($dir, '/') ? '' : $this->config('paths.groups.' . $this->assetGroup, 'assets'), '/');
        $manifest = $this->getManifest();
        $trimPath = trim($this->config('manifest_trim_path'), '/');
        $dir = $asset_path . '/' . trim($dir, '/');
        $file_groups = [];
        
        if (! file_exists($dir)) {
            throw new InvalidArgumentException('The path ' . $dir . ' could not be found.');
        }
        
        if (! is_dir($dir)) {
            throw new InvalidArgumentException('The path ' . $dir . ' is not a directory.');
        }
        
        if ($type) {
            $regex = substr_replace($regex, '(?:\\.min)?\\.' . $type, strlen($regex) - 1, 0);
        }
        
        if ($manifest) {
            foreach (preg_grep($regex, array_keys($manifest)) as $key) {
                $asset = $trimPath . '/' . $manifest[$key];

                if (preg_match('/\\.min\\./', $asset)) {
                    $file_groups[str_replace('.min', '', $key)]['min_busted'] = $asset;
                } else {
                    $file_groups[$key]['full_busted'] = $asset;
                }
            }
        }
        
        $dir_files = scandir($dir);
        
        foreach ($dir_files as $dir_file) {
            $asset = $dir . '/' . $dir_file;
            
            if (is_file($this->public_path($asset)) && preg_match($regex, $dir_file)) {
                if (preg_match('/\\.min\\./', $asset)) {
                    $file_groups[str_replace('.min', '', $asset)]['min'] = $asset;
                } else {
                    $file_groups[$asset]['full'] = $asset;
                }
            }
        }
        
        return $file_groups;
    }
    
    /**
     * Get the path to the public folder.
     *
     * @param  string  $path
     * @return string
     */
    protected function public_path($path = '')
    {
        $path = trim($path, '/');
        
        if (function_exists('public_path')) {
            return public_path($path);
        }
        
        return DIRECTORY_SEPARATOR . trim($this->config('public_path'), '/') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
