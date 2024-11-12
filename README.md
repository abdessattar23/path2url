<div align="center">
<img src="https://raw.githubusercontent.com/abdessattar23/path2url/main/art/banner.png" alt="Path2URL Banner">

# Path2URL
> 🚀 A robust PHP library for converting relative paths to absolute URLs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abdessattar23/path2url.svg?style=flat-square)](https://packagist.org/packages/abdessattar23/path2url)
[![Total Downloads](https://img.shields.io/packagist/dt/abdessattar23/path2url.svg?style=flat-square)](https://packagist.org/packages/abdessattar23/path2url)
[![License](https://img.shields.io/github/license/abdessattar23/path2url.svg?style=flat-square)](LICENSE.md)
[![PHP Version](https://img.shields.io/packagist/php-v/abdessattar23/path2url.svg?style=flat-square)](composer.json)
[![GitHub Stars](https://img.shields.io/github/stars/abdessattar23/path2url?style=social)](https://github.com/abdessattar23/path2url/stargazers)
[![Follow on GitHub](https://img.shields.io/github/followers/abdessattar23?label=Follow&style=social)](https://github.com/abdessattar23)

</div>

## 📖 About Path2URL

🛠️ Path2URL is a powerful PHP library designed to automatically convert relative file paths to absolute URLs in HTML, CSS, and JavaScript files. Perfect for migrating websites, setting up CDNs, or managing content across different environments.

> ⏰ Last Updated: 2024-11-12 14:53:36 UTC

## ✨ Features

🌟 **Key Features:**
- 🔄 Converts relative paths to absolute URLs
- 📁 Supports HTML, CSS, and JavaScript files
- 💾 Automatic backup creation before modifications
- 📝 Comprehensive logging system
- ⚙️ Configurable file extensions
- 🔒 Type-safe with PHP 7.4+ features

## ⚡ Installation

📦 Install the package via Composer:

```bash
composer require abdessattar23/path2url
```

## 🚀 Basic Usage

```php
use Path2URL\Path2URL;

// Initialize the converter
$converter = new Path2URL(
    '/path/to/your/files',
    'https://your-domain.com'
);

// Process all files
$stats = $converter->process();
```

## 🎯 Example Transformations

### 📄 HTML Files
```html
<!-- Before -->
<img src="./images/logo.png">
<a href="../docs/guide.pdf">

<!-- After -->
<img src="https://your-domain.com/images/logo.png">
<a href="https://your-domain.com/docs/guide.pdf">
```

### 🎨 CSS Files
```css
/* Before */
background-image: url('./images/bg.jpg');
background: url('../assets/pattern.png');

/* After */
background-image: url('https://your-domain.com/images/bg.jpg');
background: url('https://your-domain.com/assets/pattern.png');
```

## ⚙️ Advanced Configuration

```php
// Custom configuration
$converter = new Path2URL(
    '/path/to/your/files',
    'https://your-domain.com',
    ['html', 'css', 'js', 'xml'],  // Custom file extensions
    'custom_log.log'               // Custom log file
);
```

## 📊 Logging Example

```log
[2024-11-12 13:46:07] [INFO] Starting URL conversion process
[2024-11-12 13:46:07] [INFO] Processing file: /path/to/file.html
[2024-11-12 13:46:07] [INFO] Created backup: file.html.1731417583.bak
[2024-11-12 13:46:07] [INFO] Successfully processed file
```

## 📋 Requirements

- 💻 PHP 7.4 or higher
- 📦 Composer for dependency management

## 🧪 Testing

Run the test suite:

```bash
composer test
```

## 🔐 Security

🛡️ Found a security issue? Please email [abdessattar23](https://github.com/abdessattar23) instead of using the issue tracker.

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING](https://docs.github.com/en/contributing) for details.

## 📜 License

⚖️ The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🗺️ Roadmap

- [ ] 🖥️ CLI interface implementation
- [ ] 📁 Additional file type support
- [ ] ⚙️ Custom URL transformation rules
- [ ] 🔌 Framework integrations
- [ ] ⚡ Real-time processing option

## 👥 Credits

- 👨‍💻 Author: [abdessattar23](https://github.com/abdessattar23)
- 🌟 [All Contributors](../../contributors)

## 🌐 Social

[![Twitter Follow](https://img.shields.io/twitter/follow/abdessattar23?style=social)](https://twitter.com/abdessattar23)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-Connect-blue?style=social&logo=linkedin)](https://linkedin.com/in/abdessattar23)

## 💝 Support

If you found this package helpful, please consider:
- ⭐ Starring the repository
- 🐛 [Reporting issues](https://github.com/abdessattar23/path2url/issues)
- 🤝 Contributing to the code
- ☕ [Buy me a coffee](https://buymeacoffee.com/abdessattar23)

## 📊 Project Stats

<div align="center">
  <img src="https://github-readme-stats.vercel.app/api/pin/?username=abdessattar23&repo=path2url&theme=dark" alt="Repo Stats">
</div>

---

<div align="center">

🔥 Created and maintained by [abdessattar23](https://github.com/abdessattar23)

<br>

<img src="https://raw.githubusercontent.com/abdessattar23/path2url/main/art/footer.png" alt="Footer" width="200">

<br>

If this package helps your project, please consider giving it a ⭐

</div>

<div align="center">
  <sub>Built with ❤️ by the PHP community</sub>
</div>
