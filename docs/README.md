# PHPCS-CakePHP2 Documentation

pieceofcake2/phpcs-cakephp2 is a
[PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) standard, `CakePHP2`, for
CakePHP 2 applications and plugins running on
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp). It builds on the `CakePHP`
standard of [cakephp/cakephp-codesniffer](https://github.com/cakephp/cakephp-codesniffer) 5.x,
the coding standard of current CakePHP, and only adds the exceptions that CakePHP 2 code needs.
A second standard, `CakePHP2Namespaced`, is for code whose classes are in namespaces.

The package does not fork cakephp/cakephp-codesniffer. It depends on it and references its
`CakePHP` standard, so new and fixed sniffs arrive with a normal `composer update`.

- [Installation](#installation)
- [Choosing a Standard](#choosing-a-standard)
- [Configuration](#configuration)
- [Rules](#rules)
- [CakePHP2Namespaced](#cakephp2namespaced)
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
plugin, which registers the `CakePHP2`, `CakePHP2Namespaced` and `CakePHP` standards with PHP_CodeSniffer. Composer
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
# The installed coding standards are ..., CakePHP2, CakePHP2Namespaced, CakePHP and SlevomatCodingStandard
```

Run PHP_CodeSniffer on PHP 8.1 or later. The package can be installed on PHP 8.0 too, so that
`composer install` works in the PHP 8.0 test jobs of a project, but there Composer falls back
to cakephp/cakephp-codesniffer 5.1.0, whose `CakePHP` standard does not load with the current
slevomat/coding-standard. Set `php_version` in `phpcs.xml` to the oldest PHP version your code
supports (see below).

The [pieceofcake2/app](https://github.com/pieceofcake2/app) skeleton is already set up with the
standard.

## Choosing a Standard

The package has two standards. They differ only in the rules about namespaces.

- `CakePHP2` is for code with global classes, the way CakePHP 2 has always worked: `class
  PostsController extends AppController` without a namespace, loaded with `App::uses()` or the
  class map of Composer. This is the original layout (`app/Controller/`) and the layout of
  pieceofcake2/app (`src/Controller/`) alike.
- `CakePHP2Namespaced` is for code whose classes are in namespaces, which CakePHP 2.13 allows:
  `namespace App\Controller;`, `use` statements and the PSR-4 autoloading of Composer. It
  reports a class without a namespace, `App::uses()` in a class file and a class file that is
  not named after its class. See [CakePHP2Namespaced](#cakephp2namespaced).

Keep `CakePHP2` while an application or plugin still has global classes, and switch to
`CakePHP2Namespaced` once its classes are in namespaces.

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

For an application whose classes are in namespaces, reference `CakePHP2Namespaced` instead:

```xml
<rule ref="CakePHP2Namespaced"/>
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

## CakePHP2Namespaced

`CakePHP2Namespaced` is the `CakePHP2` standard with the following changes. It references
`CakePHP2`, so everything else (no native types outside the tests, variables named after models,
underscored members, the exceptions for controllers, shells, behaviors and tests, formatting)
is the same.

### Checked Again

`CakePHP2` excludes these sniffs because its classes are global. `CakePHP2Namespaced` checks them.

| Sniff | Not checked in | Why |
| --- | --- | --- |
| `PSR1.Classes.ClassDeclaration.MissingNamespace` | `*/config/*` | Classes are declared in a namespace. The configuration files keep their global classes: `DATABASE_CONFIG` and the schema classes in `config/Schema/`. |
| `PSR1.Files.SideEffects.FoundWithSymbols` | — | Composer loads the classes, so a class file needs no `App::uses()`. (The `CakePHP` standard does not check `config/` and `tests/`.) |
| `Squiz.Classes.ClassFileName.NoMatch` | `*/config/*`, `*/tests/TestCase/*`, `*/Test/Case/*` | PSR-4 finds a class only in the file named after it. The configuration files keep their names, and test files may declare test doubles. |

`SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedNameWithoutNamespace`
stays excluded as in `CakePHP2`, so the files without a namespace, such as the configuration
files and the templates, may still write `\Exception`. Its fix in `phpcbf` breaks attributes
there: `#[\AllowDynamicProperties]` becomes `[AllowDynamicProperties]`, which does not parse.

### Class Names in Annotations

Annotations name classes the way the code does: by their short names, imported with `use`.

```php
<?php
namespace App\Model\Behavior;

use Cake\Model\Model;
use Cake\Model\ModelBehavior;
use InvalidArgumentException;

/**
 * Counter Behavior
 */
class CounterBehavior extends ModelBehavior
{
    /**
     * Count the records of a model
     *
     * @param Model $Model Model using this behavior
     * @return int
     * @throws InvalidArgumentException When the model has no table
     */
    public function countAll(Model $Model)
    {
        if (!$Model->useTable) {
            throw new InvalidArgumentException('No table');
        }

        return $Model->find('count');
    }
}
```

| Sniff | Setting |
| --- | --- |
| `SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly` | `searchAnnotations` true: fully qualified names in annotations, such as `@param \Cake\Model\Model $Model` or `@throws \InvalidArgumentException`, are reported like the ones in the code. Global classes are imported too (`use InvalidArgumentException;`), as the `CakePHP` standard requires in the code. `phpcbf` adds the `use` statements and writes the short names. |
| `SlevomatCodingStandard.Namespaces.UnusedUses` | `searchAnnotations` true: a `use` statement for a class that only annotations name, such as `@property Post $Post` of a controller, counts as used. |

`SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName`,
which would require fully qualified names in annotations, stays excluded as in `CakePHP2`.

### Order of the Types in Annotations

`CakePHP2Namespaced.Commenting.TypeHint` replaces `CakePHP.Commenting.TypeHint`, which checks the
order of the types in `@param`, `@return` and `@var` (`string|null`, not `null|string`). It
checks the same and reports `CakePHP2Namespaced.Commenting.TypeHint.IncorrectFormat`, so exclude
it by that name. Like the original, it does not check `tests/`.

The original sniff stops `phpcbf` with a fatal error when `phpcbf` imports a class name of an
annotation: `ReferenceUsedNamesOnly` rewrites the doc comment as a whole, and the sniff parses
it in the middle of the change. The replacement skips a doc comment that is being rewritten and
checks it in the next pass of `phpcbf`, so `phpcbf` still sorts the types.

## Adding Exceptions

Add project-specific exceptions after the standard (`CakePHP2` or `CakePHP2Namespaced`). To keep a sniff away from some files:

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
