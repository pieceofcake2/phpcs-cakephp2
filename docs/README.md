# PHPCS-CakePHP2 Documentation

pieceofcake2/phpcs-cakephp2 is a
[PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) standard, `CakePHP2`, for
CakePHP 2 applications and plugins running on
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp). It builds on the `CakePHP`
standard of [cakephp/cakephp-codesniffer](https://github.com/cakephp/cakephp-codesniffer) 5.x,
the coding standard of current CakePHP, and only adds the exceptions that CakePHP 2 code needs.

The package does not fork cakephp/cakephp-codesniffer. It depends on it and references its
`CakePHP` standard, so new and fixed sniffs arrive with a normal `composer update`.

- [Installation](#installation)
- [Configuration](#configuration)
- [Rules](#rules)
- [Adding Exceptions](#adding-exceptions)
- [Migrating from a CakePHP-Based phpcs.xml](#migrating-from-a-cakephp-based-phpcsxml)
- [Development](#development)

## Installation

Install the standard with [Composer](https://getcomposer.org/) as a development dependency:

```bash
composer require --dev pieceofcake2/phpcs-cakephp2
```

It installs PHP_CodeSniffer, cakephp/cakephp-codesniffer and the
[dealerdirect/phpcodesniffer-composer-installer](https://github.com/PHPCSStandards/composer-installer)
plugin, which registers the `CakePHP2` and `CakePHP` standards with PHP_CodeSniffer. Composer
asks whether to allow the plugin. Allow it, or add it to `composer.json` yourself:

```json
{
    "config": {
        "allow-plugins": {
            "dealerdirect/phpcodesniffer-composer-installer": true
        }
    }
}
```

Check that the standard is registered:

```bash
vendor/bin/phpcs -i
# The installed coding standards are ..., CakePHP2, CakePHP and SlevomatCodingStandard
```

Run PHP_CodeSniffer on PHP 8.1 or later. The package can be installed on PHP 8.0 too, so that
`composer install` works in the PHP 8.0 test jobs of a project, but there Composer falls back
to cakephp/cakephp-codesniffer 5.1.0, whose `CakePHP` standard does not load with the current
slevomat/coding-standard. Set `php_version` in `phpcs.xml` to the oldest PHP version your code
supports (see below).

The [pieceofcake2/app](https://github.com/pieceofcake2/app) skeleton is already set up with the
standard.

## Configuration

Add a `phpcs.xml` file to the root of your project. PHP_CodeSniffer reads it automatically, so
`vendor/bin/phpcs` and `vendor/bin/phpcbf` need no arguments.

For an application with the directory layout of pieceofcake2/app (`config/`, `src/`, `tests/`):

```xml
<?xml version="1.0"?>
<ruleset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/squizlabs/php_codesniffer/phpcs.xsd">
    <config name="php_version" value="80000"/>

    <arg value="ps"/>
    <arg name="colors"/>
    <arg name="parallel" value="16"/>
    <arg name="extensions" value="php,php.default"/>

    <file>./config</file>
    <file>./src</file>
    <file>./tests</file>

    <rule ref="CakePHP2"/>
</ruleset>
```

For an application with the original layout, list the directories under `app/` instead, for
example `./app/Config`, `./app/Controller`, `./app/Model`, `./app/View` and `./app/Test`. A
plugin lists its own directories (`Controller`, `Model`, `Lib`, `tests`, ...).

The patterns in the standard match these directories wherever they are, so `Controller/`
means `src/Controller/`, `app/Controller/` and `Controller/` of a plugin alike.

## Rules

`CakePHP2` is the `CakePHP` standard with the following changes. Everything else, such as
indentation, short array syntax, doc comments and `use` statements, is the `CakePHP` standard
unchanged.

### Excluded Everywhere

| Sniff | Why |
| --- | --- |
| `PSR1.Classes.ClassDeclaration.MissingNamespace` | CakePHP 2 classes live in the global namespace. |
| `PSR1.Files.SideEffects.FoundWithSymbols` | `App::uses()` at the top of a class file counts as a side effect. |
| `Squiz.Classes.ClassFileName.NoMatch` | Some CakePHP 2 files are not named after their class, e.g. `Config/Schema/schema.php`. |
| `SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedNameWithoutNamespace` | `\Exception` and other global names are fine without a namespace. |
| `SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName` | Class names in annotations are global names. |
| `SlevomatCodingStandard.TypeHints.PropertyTypeHint` | Native types would change the signatures that CakePHP 2 classes and their subclasses share; types are documented in docblocks. |
| `SlevomatCodingStandard.TypeHints.ParameterTypeHint` | Same as above. |
| `SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint` | Same as above. The tests are the exception: see `CakePHP2.TypeHints.TestReturnTypeHint` below. |
| `SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing` | CakePHP 2 code is not written for strict types. |
| `Zend.NamingConventions.ValidVariableName.StringVarNotCamelCaps` | Variables are named after models: `"{$Model->alias}"`. |
| `Zend.NamingConventions.ValidVariableName.PublicHasUnderscore` | CakePHP 2 has underscored public properties. |
| `Zend.NamingConventions.ValidVariableName.PublicVarHasUnderscore` | Same as above. |
| `SlevomatCodingStandard.ControlStructures.AssignmentInCondition.AssignmentInCondition` | Common in CakePHP 2: `while ($row = $result->fetch())`. |
| `CakePHP.Commenting.FunctionComment.SpacingAfter` | Allows a blank line after a doc comment. |
| `Generic.Files.LineLength` | Much of the CakePHP 2 code base has long lines. |

### Excluded in Some Places

| Sniff | Not checked in | Why |
| --- | --- | --- |
| `PSR1.Methods.CamelCapsMethodName.NotCamelCaps` | `*/Controller/*`, `*/Console/Command/*`, `*/Model/Behavior/*` | Controller actions (`admin_index`), shell commands and behavior methods use underscores. |
| `CakePHP.Commenting.FunctionComment.Missing` | `*/tests/TestCase/*`, `*/Test/Case/*` | Test methods need no doc comment. |
| `PSR1.Classes.ClassDeclaration.MultipleClasses` | `*/tests/TestCase/*`, `*/Test/Case/*` | Test files may declare test doubles next to the test case. |
| `Squiz.Classes.ValidClassName.NotPascalCase` | `*/database.php`, `*/database.php.default` | The datasource configuration class is `DATABASE_CONFIG`. |
| `Squiz.PHP.NonExecutableCode.Unreachable` | `*/acl.ini.php` | The file starts with `exit;` so that the INI data is never served. |

### Added

| Sniff | Setting |
| --- | --- |
| `Generic.Formatting.MultipleStatementAlignment` | `maxPadding` 1: aligned assignments may have at most one extra space. |
| `CakePHP2.TypeHints.TestReturnTypeHint` | In `*/tests/TestCase/*` and `*/Test/Case/*`, every method and function needs a native return type: `setUp(): void`, `testFoo(): void`, `fooProvider(): array`. PHPUnit declares its template methods with native types, and test methods are only called by PHPUnit, so the tests can use them although the rest of CakePHP 2 code does not. `phpcbf` adds `: void` to methods that return nothing. Constructors are not checked. (The `CakePHP` standard does not check return types in `tests/` at all.) |

## Adding Exceptions

Add project-specific exceptions after the standard. To keep a sniff away from some files:

```xml
<rule ref="CakePHP2"/>

<!-- PHP calls the methods of stream wrappers by fixed names (stream_open, url_stat, ...) -->
<rule ref="PSR1.Methods.CamelCapsMethodName.NotCamelCaps">
    <exclude-pattern>Lib/S3StreamWrapper.php</exclude-pattern>
</rule>
```

To turn a sniff off everywhere, exclude it in the reference:

```xml
<rule ref="CakePHP2">
    <exclude name="SlevomatCodingStandard.TypeHints"/>
</rule>
```

Run `vendor/bin/phpcs -s` to see the name of the sniff behind each message.

## Migrating from a CakePHP-Based phpcs.xml

Until this package, the pieceofcake2 repositories and the pieceofcake2/app skeleton had the
exceptions above written out in every `phpcs.xml`, after `<rule ref="CakePHP">`. To switch:

1. Replace the dependency:

   ```bash
   composer remove --dev cakephp/cakephp-codesniffer
   composer require --dev pieceofcake2/phpcs-cakephp2
   ```

2. In `phpcs.xml`, replace the `<rule ref="CakePHP">` block and the rules that the standard
   now covers (`MultipleStatementAlignment`, `FunctionComment.Missing`, `MultipleClasses`,
   `NotCamelCaps` for controllers, shells and behaviors, `ReturnTypeHint.MissingNativeTypeHint`,
   `NotPascalCase` for `database.php` and `Unreachable` for `acl.ini.php`) with
   `<rule ref="CakePHP2"/>`. Keep your `<config>`, `<arg>` and `<file>` entries and any
   exceptions for your own files.

3. Run `vendor/bin/phpcs`. Since 1.1.0, test methods without a native return type are reported
   (`CakePHP2.TypeHints.TestReturnTypeHint`); run `vendor/bin/phpcbf` to add `: void`, and add the
   other return types by hand.

The old configuration had a rule meant to require native return types in `tests/TestCase/`
(`ReturnTypeHint.MissingNativeTypeHint` with an include pattern). It never took effect, because the
`CakePHP` standard does not check return types in `tests/`. `CakePHP2.TypeHints.TestReturnTypeHint`
does this job.

## Development

```bash
composer install

# Run the tests: phpcs with each standard on its files in fixtures/
vendor/bin/phpunit

# Check the coding standard of the tests
vendor/bin/phpcs
```

`composer test` runs both. Each standard has its own directory in `fixtures/`, such as
`fixtures/CakePHP2/`: `valid/` holds CakePHP 2 code that must pass, and `invalid/` code with
known violations; `tests/RulesetTest.php` lists the sniffs expected for each file. The fixtures are outside `tests/` because the `CakePHP` standard skips
some sniffs in `tests/`.
