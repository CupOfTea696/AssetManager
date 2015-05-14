<?php namespace CupOfTea\AssetManager;

use CupOfTea\Package\Package;
use CupOfTea\AssetManager\Contracts\Provider as ProviderContract;

class AssetManager implements ProviderContract
{
    
    use Package;
    
    /**
     * Package Info
     *
     * @const string PACKAGE
     * @const string VERSION
     */
    const PACKAGE = 'CupOfTea/AssetManager';
    const VERSION = '0.0.0';
    
    /**
     * Get an array with uncompressed and minified files for an asset.
     *
     * @param  string   $asset
     * @param  string   $type Optional.
     * @return array
     */
    protected function files($asset, $type = false)
    {
        $asset_path = trim(config('assets.path', 'assets'), '/');
        $asset = $type ? config('assets.' . $type, $type) . '/' . trim($asset, '/') . '.' . $type;
        $asset = $asset_path . '/' . $asset;
        
        return [
            'full' => $asset,
            'min' => preg_replace('/(.*)(\..+)/', '$1.min$2', $asset_full),
        ];
    }
    
    /**
	 * {@inheritdoc}
	 */
    public function exists($asset)
    {
        $asset_files = $this->files($asset);
        $production = app()->environment('production');
        
        if (file_exists(public_path($asset_files[$production ? 'min' : 'full'])))
            return $asset_files[$production ? 'min' : 'full'] . '?v=' . md5_file(public_path($asset_files[$production ? 'min' : 'full']));
        
        if (file_exists($asset_files[$production ? 'full' : 'min']))
            return $asset_files[$production ? 'full' : 'min'] . '?v=' . md5_file(public_path($asset_files[$production ? 'full' : 'min']));
        
        return false;
    }
    
    /**
	 * {@inheritdoc}
	 */
    public function get($asset, $type = false)
    {
        $asset_files = $this->files($asset, $type);
        $asset = $this->exists($asset);
        
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
    
    /**
	 * {@inheritdoc}
	 */
    public function css($asset)
    {
        return $this->get($asset, 'css');
    }
    
    /**
	 * {@inheritdoc}
	 */
    public function js($asset)
    {
        return $this->get($asset, 'js');
    }
}
