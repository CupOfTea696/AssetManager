<?php namespace CupOfTea\AssetManager\Contracts;

interface Provider
{
    /**
     * Select an Asset Group to get assets from.
     * 
     * @param  string|null  $group
     * @return \CupOfTea\AssetManager\AssetManager
     */
    public function from($group = null);
    
    /**
     * Check if an Asset exists.
     *
     * @param string $asset
     * @return bool
     */
    public function exists($asset);
    
    /**
     * Get an Asset if it exists.
     *
     * @param  string  $asset
     * @param  string|false  $type
     * @return string|bool
     */
    public function get($asset, $type = false);
    
    /**
     * Get Assets matching regex if they exist.
     *
     * @param  string  $regex
     * @param  string  $dir
     * @param  string|false  $type
     * @return array|bool
     */
    public function getRegex($regex, $dir, $type = false);
    
    /**
     * Get a CSS Asset if it exists.
     *
     * @param  string  $asset
     * @param  bool  $split
     * @param  bool|null  $html
     * @return string|array|bool
     */
    public function css($asset, $split = false, $html = null);
    
    /**
     * Get a JS Asset if it exists.
     *
     * @param  string  $asset
     * @param  bool|null  $html
     * @return string|bool
     */
    public function js($asset, $html = null);
    
    /**
     * Return a CDN asset with local fallback.
     *
     * @param  string  $cdn
     * @param  string|Closure  $fallback
     * @return mixed
     */
    public function cdn($cdn, $fallback);
}
