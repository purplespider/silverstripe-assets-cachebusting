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

The module automatically appends a unique hash parameter to all asset URLs. The hash is based on the file's content, so it changes whenever a file is replaced - even if the filename stays the same.

This works everywhere: file links, images in WYSIWYG content, template references, resized images, and more. When a CMS user replaces an asset, Cloudflare and other CDNs immediately serve the new version.

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
