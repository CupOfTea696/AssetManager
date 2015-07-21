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
    const VERSION = '1.2.4';
    
    /**
	 * {@inheritdoc}
	 */
    public function exists($asset, $type = false)
    {
        return asset_exists($asset, $type);
    }
    
    /**
	 * {@inheritdoc}
	 */
    public function get($asset, $type = false)
    {
        return get_asset($asset, $type);
    }
    
    /**
	 * {@inheritdoc}
	 */
    public function css($asset, $html = null)
    {
        return css($asset, $html);
    }
    
    /**
	 * {@inheritdoc}
	 */
    public function js($asset, $html = null)
    {
        return js($asset, $html)
    }
    
    /**
	 * {@inheritdoc}
	 */
    function cdn($cdn, $fallback)
    {
        return cdn($cdn, $fallback);
    }
}
