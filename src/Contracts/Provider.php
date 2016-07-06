<?php namespace CupOfTea\AssetManager\Contracts;

interface Provider
{
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
     * Get a CSS Asset if it exists.
     *
     * @param  string  $asset
     * @param  bool|null  $html
     * @return string|bool
     */
    public function css($asset, $html = null);
    
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
