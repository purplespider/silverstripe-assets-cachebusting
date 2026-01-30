# SilverStripe Assets Cache Busting

Automatic cache busting for SilverStripe assets by appending file content hash to URLs.

## Overview

This module adds automatic cache busting to all asset URLs in SilverStripe by appending a unique parameter based on the file's content hash. This allows you to set aggressive browser cache headers (long expiry times) while ensuring browsers and CDNs (like Cloudflare) fetch updated files when they change.

**Solves a common problem**: When a CMS user replaces an asset with a new file using the same filename, CDNs and browsers can serve the old cached version for hours or days. This module ensures the URL changes automatically when the file content changes, forcing immediate updates.

**Before:**
```
/assets/images/logo.png
```

**After:**
```
/assets/images/logo.png?m=a1b2c3d4e5
```

When the file content changes, the hash automatically updates, forcing browsers to fetch the new version.

## Requirements

- SilverStripe ^5.0 or ^6.0
- SilverStripe Assets ^2.0 or ^3.0

## Installation

Install via Composer:

```bash
composer require purplespider/silverstripe-assets-cachebusting
```

Then run dev/build:

```bash
vendor/bin/sake dev/build flush=1
```

## Configuration

Cache busting is enabled by default. To disable it, add this to your config:

```yaml
SilverStripe\Assets\File:
  enable_cache_busting: false
```

## How It Works

The module extends `SilverStripe\Assets\Storage\DBFile` and hooks into the `updateURL` extension point. This ensures cache busting works automatically for:

- Regular file URLs: `$file->URL()`
- Absolute URLs: `$file->AbsoluteURL()`
- Links: `$file->Link()`
- Resized images: `$image->ScaleWidth(600)`
- Image manipulations and variants
- Images and files in WYSIWYG/TinyMCE content
- Images and files referenced in templates
- Background images in CSS
- Srcset attributes

### Cache Buster Value

The cache buster uses:

1. **File content hash** (first 10 characters) - Changes when file content changes
2. **Variant hash** (for image manipulations) - Different for each size/manipulation
3. **Nothing** - If neither is available (rare for valid files)

The content hash is ideal because:
- It changes only when content changes
- It's consistent across environments
- It's already calculated by SilverStripe's asset system
- Identical files have the same hash (efficient caching)

## Browser Caching Headers

For maximum benefit, configure your web server to set long cache expiry headers for assets.

### Apache (.htaccess)

```apache
<FilesMatch "\.(jpg|jpeg|png|gif|webp|svg|css|js|woff|woff2|ttf|eot|pdf)$">
    Header set Cache-Control "max-age=31536000, public"
</FilesMatch>
```

### Nginx

```nginx
location ~* \.(jpg|jpeg|png|gif|webp|svg|css|js|woff|woff2|ttf|eot|pdf)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

## Testing

To verify cache busting is working:

1. View your site's source code
2. Find any asset URL (image, file, etc.)
3. Confirm it has the `?m=` parameter appended

Example:
```html
<img src="/assets/photos/image.jpg?m=abc123def4" alt="Photo">
```

## Credits

Based on [SilverStripe Framework PR #2402](https://github.com/silverstripe/silverstripe-framework/pull/2402) by [@patbolo](https://github.com/patbolo).

## License

BSD-3-Clause. See [LICENSE](LICENSE) for details.

## Contributing

Contributions are welcome! Please open an issue or pull request on [GitHub](https://github.com/purplespider/silverstripe-assets-cachebusting).

## Support

For issues or questions:
- Open an issue on [GitHub](https://github.com/purplespider/silverstripe-assets-cachebusting/issues)
- Contact: [hello@purplespider.com](mailto:hello@purplespider.com)
