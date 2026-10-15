# PHPCS-CakePHP2 ドキュメント

pieceofcake2/phpcs-cakephp2 は、[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp) で動作する
CakePHP 2 のアプリケーションとプラグインのための
[PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) のコーディング規約 `CakePHP2` です。
現在の CakePHP のコーディング規約である [cakephp/cakephp-codesniffer](https://github.com/cakephp/cakephp-codesniffer) 5.x の
`CakePHP` 規約をベースにしており、CakePHP 2 のコードに必要な例外だけを追加しています。

このパッケージは cakephp/cakephp-codesniffer をフォークしていません。cakephp/cakephp-codesniffer に依存し、その `CakePHP`
規約を参照しているので、新しい sniff や修正された sniff は通常の `composer update` で取り込まれます。

- [インストール](#installation)
- [設定](#configuration)
- [ルール](#rules)
- [例外の追加](#adding-exceptions)
- [CakePHP ベースの phpcs.xml からの移行](#migrating-from-a-cakephp-based-phpcsxml)
- [開発](#development)

<a id="installation"></a>

## インストール

[Composer](https://getcomposer.org/) で、開発用の依存パッケージとして規約をインストールします。

```bash
composer require --dev pieceofcake2/phpcs-cakephp2
```

これにより、PHP_CodeSniffer、cakephp/cakephp-codesniffer と
[dealerdirect/phpcodesniffer-composer-installer](https://github.com/PHPCSStandards/composer-installer)
プラグインがインストールされます。このプラグインは `CakePHP2` 規約と `CakePHP` 規約を PHP_CodeSniffer に登録します。
Composer はプラグインを許可するか尋ねます。許可するか、自分で `composer.json` に追加してください。

```json
{
    "config": {
        "allow-plugins": {
            "dealerdirect/phpcodesniffer-composer-installer": true
        }
    }
}
```

規約が登録されていることを確認します。

```bash
vendor/bin/phpcs -i
# The installed coding standards are ..., CakePHP2, CakePHP and SlevomatCodingStandard
```

PHP_CodeSniffer は PHP 8.1 以降で実行してください。プロジェクトの PHP 8.0 のテストジョブで `composer install` が
動作するように、このパッケージは PHP 8.0 にもインストールできます。ただしその場合、Composer は
cakephp/cakephp-codesniffer 5.1.0 にフォールバックし、その `CakePHP` 規約は現在の slevomat/coding-standard では
読み込めません。`phpcs.xml` の `php_version` には、コードがサポートする最も古い PHP のバージョンを設定してください
(後述)。

[pieceofcake2/app](https://github.com/pieceofcake2/app) スケルトンには、この規約がすでに設定されています。

<a id="configuration"></a>

## 設定

プロジェクトのルートに `phpcs.xml` ファイルを追加します。PHP_CodeSniffer はこのファイルを自動的に読み込むので、
`vendor/bin/phpcs` と `vendor/bin/phpcbf` に引数は必要ありません。

pieceofcake2/app のディレクトリ構成 (`config/`、`src/`、`tests/`) のアプリケーションの場合:

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

元の構成のアプリケーションでは、代わりに `app/` 以下のディレクトリを列挙します。たとえば `./app/Config`、
`./app/Controller`、`./app/Model`、`./app/View` と `./app/Test` です。プラグインでは、プラグイン自身のディレクトリ
(`Controller`、`Model`、`Lib`、`tests`、...) を列挙します。

規約のパターンは、これらのディレクトリがどこにあってもマッチします。そのため `Controller/` は、`src/Controller/`、
`app/Controller/`、プラグインの `Controller/` のいずれも指します。

<a id="rules"></a>

## ルール

`CakePHP2` は、`CakePHP` 規約に次の変更を加えたものです。インデント、短い配列構文、ドキュメントコメント、`use` 文など、
それ以外はすべて `CakePHP` 規約のままです。

<a id="excluded-everywhere"></a>

### すべての場所で除外

| Sniff | 理由 |
| --- | --- |
| `PSR1.Classes.ClassDeclaration.MissingNamespace` | CakePHP 2 のクラスはグローバル名前空間にあります。 |
| `PSR1.Files.SideEffects.FoundWithSymbols` | クラスファイルの先頭にある `App::uses()` が副作用とみなされます。 |
| `Squiz.Classes.ClassFileName.NoMatch` | CakePHP 2 には、クラス名と異なる名前のファイルがあります (例: `Config/Schema/schema.php`)。 |
| `SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedNameWithoutNamespace` | 名前空間がなければ、`\Exception` などのグローバルな名前をそのまま使ってかまいません。 |
| `SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName` | アノテーション内のクラス名はグローバルな名前です。 |
| `SlevomatCodingStandard.TypeHints.PropertyTypeHint` | ネイティブの型は、CakePHP 2 のクラスとそのサブクラスが共有するシグネチャを変えてしまいます。型は docblock に記述します。 |
| `SlevomatCodingStandard.TypeHints.ParameterTypeHint` | 上と同じです。 |
| `SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint` | 上と同じです。ネイティブの戻り値の型は引き続き使えます (例: テストメソッドの `: void`)。 |
| `SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing` | CakePHP 2 のコードは strict types を前提に書かれていません。 |
| `Zend.NamingConventions.ValidVariableName.StringVarNotCamelCaps` | 変数はモデルの名前で命名されます: `"{$Model->alias}"`。 |
| `Zend.NamingConventions.ValidVariableName.PublicHasUnderscore` | CakePHP 2 にはアンダースコア付きの public プロパティがあります。 |
| `Zend.NamingConventions.ValidVariableName.PublicVarHasUnderscore` | 上と同じです。 |
| `SlevomatCodingStandard.ControlStructures.AssignmentInCondition.AssignmentInCondition` | CakePHP 2 ではよく使われます: `while ($row = $result->fetch())`。 |
| `CakePHP.Commenting.FunctionComment.SpacingAfter` | ドキュメントコメントの後の空行を許可します。 |
| `Generic.Files.LineLength` | CakePHP 2 のコードベースの多くに長い行があります。 |

<a id="excluded-in-some-places"></a>

### 一部の場所で除外

| Sniff | チェックしない場所 | 理由 |
| --- | --- | --- |
| `PSR1.Methods.CamelCapsMethodName.NotCamelCaps` | `*/Controller/*`、`*/Console/Command/*`、`*/Model/Behavior/*` | コントローラーのアクション (`admin_index`)、シェルのコマンド、ビヘイビアのメソッドはアンダースコアを使います。 |
| `CakePHP.Commenting.FunctionComment.Missing` | `*/tests/TestCase/*`、`*/Test/Case/*` | テストメソッドにはドキュメントコメントは必要ありません。 |
| `PSR1.Classes.ClassDeclaration.MultipleClasses` | `*/tests/TestCase/*`、`*/Test/Case/*` | テストファイルでは、テストケースの隣にテストダブルを宣言してもかまいません。 |
| `Squiz.Classes.ValidClassName.NotPascalCase` | `*/database.php`、`*/database.php.default` | データソースの設定クラスは `DATABASE_CONFIG` です。 |
| `Squiz.PHP.NonExecutableCode.Unreachable` | `*/acl.ini.php` | INI のデータが決して配信されないように、このファイルは `exit;` で始まります。 |

<a id="added"></a>

### 追加

| Sniff | 設定 |
| --- | --- |
| `Generic.Formatting.MultipleStatementAlignment` | `maxPadding` 1: 揃えた代入に追加できる空白は最大 1 つです。 |

<a id="adding-exceptions"></a>

## 例外の追加

プロジェクト固有の例外は、規約の後に追加します。一部のファイルで sniff を無効にするには、次のようにします。

```xml
<rule ref="CakePHP2"/>

<!-- PHP calls the methods of stream wrappers by fixed names (stream_open, url_stat, ...) -->
<rule ref="PSR1.Methods.CamelCapsMethodName.NotCamelCaps">
    <exclude-pattern>Lib/S3StreamWrapper.php</exclude-pattern>
</rule>
```

すべての場所で sniff を無効にするには、参照の中で除外します。

```xml
<rule ref="CakePHP2">
    <exclude name="SlevomatCodingStandard.TypeHints"/>
</rule>
```

各メッセージの元になっている sniff の名前は、`vendor/bin/phpcs -s` を実行すると確認できます。

<a id="migrating-from-a-cakephp-based-phpcsxml"></a>

## CakePHP ベースの phpcs.xml からの移行

このパッケージができるまで、pieceofcake2 のリポジトリと pieceofcake2/app スケルトンでは、上記の例外をすべての
`phpcs.xml` の `<rule ref="CakePHP">` の後に書き出していました。切り替えるには次のようにします。

1. 依存パッケージを置き換えます:

   ```bash
   composer remove --dev cakephp/cakephp-codesniffer
   composer require --dev pieceofcake2/phpcs-cakephp2
   ```

2. `phpcs.xml` で、`<rule ref="CakePHP">` のブロックと、この規約が対応するようになったルール
   (`MultipleStatementAlignment`、`FunctionComment.Missing`、`MultipleClasses`、コントローラー、シェル、
   ビヘイビアに対する `NotCamelCaps`、`ReturnTypeHint.MissingNativeTypeHint`、`database.php` に対する
   `NotPascalCase` と `acl.ini.php` に対する `Unreachable`) を `<rule ref="CakePHP2"/>` に置き換えます。
   `<config>`、`<arg>`、`<file>` の項目と、自分のファイルに対する例外はそのまま残してください。

3. `vendor/bin/phpcs` を実行します。結果は以前と同じです。

以前の設定には、`tests/TestCase/` でネイティブの戻り値の型を必須にするルールがありました。しかし `CakePHP` 規約は
`tests/` では戻り値の型をチェックしないので、このルールは一度も効果がありませんでした。そのため、この規約は
ネイティブの戻り値の型を必須にしていません。

<a id="development"></a>

## 開発

```bash
composer install

# Run the tests: phpcs with the standard on the files in fixtures/
vendor/bin/phpunit

# Check the coding standard of the tests
vendor/bin/phpcs
```

`composer test` は両方を実行します。`fixtures/valid/` には合格しなければならない CakePHP 2 のコードが、
`fixtures/invalid/` には既知の違反を含むコードがあります。`tests/RulesetTest.php` には、ファイルごとに期待される
sniff が列挙されています。`CakePHP` 規約は `tests/` では一部の sniff をスキップするので、フィクスチャは `tests/` の
外に置いています。
