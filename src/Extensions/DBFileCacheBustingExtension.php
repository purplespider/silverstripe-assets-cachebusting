<?php

namespace PurpleSpider\CacheBusting\Extensions;

use SilverStripe\Assets\File;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;

/**
 * Extension for DBFile to add cache busting to file URLs.
 *
 * Appends ?m=<hash> to file URLs to enable aggressive browser caching
 * while ensuring updated files are fetched when modified.
 *
 * Based on: https://github.com/silverstripe/silverstripe-framework/pull/2402
 */
class DBFileCacheBustingExtension extends Extension
{
    /**
     * Update the URL to include cache buster parameter
     * This hook is called by DBFile::getURL()
     *
     * @param string $url
     */
    public function updateURL(&$url)
    {
        // Check if cache busting is enabled
        if (!Config::inst()->get(File::class, 'enable_cache_busting')) {
            return;
        }

        // Only add cache buster if we have a valid URL
        if (!$url) {
            return;
        }

        // Get cache buster parameter
        $cacheBuster = $this->getCacheBusterParam();
        if ($cacheBuster) {
            // Check if URL already has query parameters
            $separator = (strpos($url, '?') !== false) ? '&' : '?';
            $url .= $separator . 'm=' . $cacheBuster;
        }
    }

    /**
     * Get the cache buster value for this file
     *
     * @return string|null
     */
    protected function getCacheBusterParam()
    {
        $field = $this->owner;

        // Get hash from the DBFile - this is the file content hash
        // and will change whenever the file content changes
        $hash = $field->getHash();
        if ($hash) {
            // Use first 10 chars of hash for a shorter cache buster
            return substr($hash, 0, 10);
        }

        // Fallback to using variant (for image manipulations like resizing)
        // The variant includes the manipulation parameters, so different sizes
        // will have different cache busters
        $variant = $field->getVariant();
        if ($variant) {
            return substr(md5($variant), 0, 10);
        }

        // If we can't get a hash or variant, don't add a cache buster
        // This should rarely happen for valid files in the asset system
        return null;
    }
}
