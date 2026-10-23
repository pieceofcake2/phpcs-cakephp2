# PHPCS-CakePHP2

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/phpcs-cakephp2?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/phpcs-cakephp2?label=Packagist)](https://packagist.org/packages/pieceofcake2/phpcs-cakephp2)
[![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/phpcs-cakephp2/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)](https://packagist.org/packages/pieceofcake2/phpcs-cakephp2)
[![CI](https://img.shields.io/github/actions/workflow/status/pieceofcake2/phpcs-cakephp2/CI.yml?label=CI)](https://github.com/pieceofcake2/phpcs-cakephp2/actions/workflows/CI.yml)

A [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) standard for CakePHP 2
applications and plugins running on [pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp).
It is the `CakePHP` standard of
[cakephp/cakephp-codesniffer](https://github.com/cakephp/cakephp-codesniffer) 5.x, with the
exceptions that CakePHP 2 code needs: no namespaces, `App::uses()`, underscored controller
actions, `DATABASE_CONFIG` and no native types in signatures. The CakePHP rules are used as
they are, not copied, so their updates arrive with cakephp/cakephp-codesniffer.

The package has two standards:

- `CakePHP2` for code with global classes, the way CakePHP 2 has always worked
  (`App::uses()`, no namespaces).
- `CakePHP2Namespace` for code whose classes are in namespaces, as CakePHP 2.13 allows
  (`namespace App\Controller;`, `use` statements, PSR-4). It is `CakePHP2` with the namespace
  rules turned back on, and with class names in annotations imported like in the code.

## Documentation

- Website: https://pieceofcake2.github.io/phpcs-cakephp2/ (日本語: https://pieceofcake2.github.io/phpcs-cakephp2/ja/)
- Markdown: [docs/README.md](docs/README.md) (日本語: [docs/ja/README.md](docs/ja/README.md)) (for AI tools and offline reading)

The Markdown documentation is also included in the Composer package, at `vendor/pieceofcake2/phpcs-cakephp2/docs/README.md`. Install the package with `--dev` if you only need it for development.

## Requirements

- PHP 8.1+ to run PHP_CodeSniffer (installable on PHP 8.0)
- cakephp/cakephp-codesniffer 5.x

## Installation

```bash
composer require --dev pieceofcake2/phpcs-cakephp2
```

Allow the `dealerdirect/phpcodesniffer-composer-installer` plugin when Composer asks. Then use
the standard in your `phpcs.xml`:

```xml
<?xml version="1.0"?>
<ruleset>
    <file>./src</file>
    <file>./tests</file>

    <rule ref="CakePHP2"/>
</ruleset>
```

If your classes are in namespaces, use `<rule ref="CakePHP2Namespace"/>` instead.

## Contributing

See the [contribution guidelines](https://github.com/pieceofcake2/.github/blob/master/CONTRIBUTING.md).

## License

Licensed under the MIT License. See [LICENSE](LICENSE).
