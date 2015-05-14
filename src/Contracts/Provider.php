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
     * @param string $asset
     * @param string $type Optional.
	 * @return string|bool
	 */
	public function get($asset, $type = false);
    
	/**
	 * Get a CSS Asset if it exists.
	 *
     * @param string $asset
	 * @return string|bool
	 */
	public function css($asset);
    
	/**
	 * Get a JS Asset if it exists.
	 *
     * @param string $asset
	 * @return string|bool
	 */
	public function js($asset);
    
}
