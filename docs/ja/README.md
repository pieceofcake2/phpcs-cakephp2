# PHPCS-CakePHP2 ドキュメント

pieceofcake2/phpcs-cakephp2 は、[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp) で動作する
CakePHP 2 のアプリケーションとプラグインのための
[PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) のコーディング規約 `CakePHP2` です。
現在の CakePHP のコーディング規約である [cakephp/cakephp-codesniffer](https://github.com/cakephp/cakephp-codesniffer) 5.x の
`CakePHP` 規約をベースにしており、CakePHP 2 のコードに必要な例外だけを追加しています。
クラスを名前空間に置くコードには、もう一つの規約 `CakePHP2Namespace` を使います。

このパッケージは cakephp/cakephp-codesniffer をフォークしていません。cakephp/cakephp-codesniffer に依存し、その `CakePHP`
規約を参照しているので、新しい sniff や修正された sniff は通常の `composer update` で取り込まれます。

- [インストール](#installation)
- [規約の選び方](#choosing-a-standard)
- [設定](#configuration)
- [ルール](#rules)
- [CakePHP2Namespace](#cakephp2namespace)
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
プラグインがインストールされます。このプラグインは `CakePHP2`、`CakePHP2Namespace`、`CakePHP` の各規約を PHP_CodeSniffer に登録します。
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
# The installed coding standards are ..., CakePHP2, CakePHP2Namespace, CakePHP and SlevomatCodingStandard
```

PHP_CodeSniffer は PHP 8.1 以降で実行してください。プロジェクトの PHP 8.0 のテストジョブで `composer install` が
動作するように、このパッケージは PHP 8.0 にもインストールできます。ただしその場合、Composer は
cakephp/cakephp-codesniffer 5.1.0 にフォールバックし、その `CakePHP` 規約は現在の slevomat/coding-standard では
読み込めません。`phpcs.xml` の `php_version` には、コードがサポートする最も古い PHP のバージョンを設定してください
(後述)。

[pieceofcake2/app](https://github.com/pieceofcake2/app) スケルトンには、この規約がすでに設定されています。

<a id="choosing-a-standard"></a>

## 規約の選び方

このパッケージには 2 つの規約があります。違うのは名前空間に関するルールだけです。

- `CakePHP2` は、これまでの CakePHP 2 と同じく、クラスがグローバルなコードのための規約です。名前空間のない
  `class PostsController extends AppController` を、`App::uses()` や Composer のクラスマップで読み込みます。
  元の構成 (`app/Controller/`) でも、pieceofcake2/app の構成 (`src/Controller/`) でも同じです。
- `CakePHP2Namespace` は、CakePHP 2.13 からできるようになった、クラスを名前空間に置くコードのための規約です。
  `namespace App\Controller;` と `use` 文を書き、Composer の PSR-4 オートロードでクラスを読み込みます。
  名前空間のないクラス、クラスファイルの中の `App::uses()`、クラス名と違う名前のクラスファイルを報告します。
  [CakePHP2Namespace](#cakephp2namespace) をご覧ください。

アプリケーションやプラグインにグローバルなクラスが残っている間は `CakePHP2` を使い、クラスを名前空間に移し終えたら
`CakePHP2Namespace` に切り替えてください。

<a id="configuration"></a>

## 設定

プロジェクトのルートに `phpcs.xml` ファイルを追加します。PHP_CodeSniffer はこのファイルを自動的に読み込むので、
`vendor/bin/phpcs` と `vendor/bin/phpcbf` に引数は必要ありません。

pieceofcake2/app のディレクトリ構成 (`config/`、`src/`、`tests/`) のアプリケーションでは、次のようにします。

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

クラスを名前空間に置くアプリケーションでは、代わりに `CakePHP2Namespace` を参照します。

```xml
<rule ref="CakePHP2Namespace"/>
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
| `SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint` | 上と同じです。テストは例外です。下の `CakePHP2.TypeHints.TestReturnTypeHint` をご覧ください。 |
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
| `CakePHP2.TypeHints.TestReturnTypeHint` | `*/tests/TestCase/*` と `*/Test/Case/*` では、すべてのメソッドと関数にネイティブの戻り値の型が必要です: `setUp(): void`、`testFoo(): void`、`fooProvider(): array`。PHPUnit はテンプレートメソッドをネイティブの型で宣言しており、テストメソッドを呼ぶのは PHPUnit だけなので、CakePHP 2 のほかのコードと違ってテストでは使えます。何も返さないメソッドには `phpcbf` が `: void` を追加します。コンストラクターはチェックしません (`CakePHP` 規約は `tests/` では戻り値の型をまったくチェックしません)。 |
| `SlevomatCodingStandard.Namespaces.UnusedUses` | `searchAnnotations` true: `use Cake\Network\CakeResponse;` のあとの `@return CakeResponse\|null` のように、アノテーションでしか使っていないクラスの `use` 文も、使われているとみなします。アプリケーション自身のクラスがグローバルでも、CakePHP 2.13 の名前空間付きのコアクラスは `use` で取り込むためです。 |
| `CakePHP2.Commenting.TypeHint` | `CakePHP.Commenting.TypeHint` の代わりに使います。[アノテーションの型の順序](#order-of-the-types-in-annotations) をご覧ください。 |

<a id="cakephp2namespace"></a>

## CakePHP2Namespace

`CakePHP2Namespace` は、`CakePHP2` 規約に次の変更を加えたものです。`CakePHP2` を参照しているので、それ以外
(テスト以外ではネイティブの型を使わないこと、モデル名の変数、アンダースコア付きのメンバー、コントローラー、シェル、
ビヘイビア、テストに対する例外、書式) は同じです。

<a id="checked-again"></a>

### 再びチェックするもの

`CakePHP2` はクラスがグローバルなので次の sniff を除外していますが、`CakePHP2Namespace` はチェックします。

| Sniff | チェックしない場所 | 理由 |
| --- | --- | --- |
| `PSR1.Classes.ClassDeclaration.MissingNamespace` | `*/config/*` | クラスは名前空間の中で宣言します。設定ファイルのクラスはグローバルのままです: `DATABASE_CONFIG` と `config/Schema/` のスキーマクラス。 |
| `PSR1.Files.SideEffects.FoundWithSymbols` | — | クラスは Composer が読み込むので、クラスファイルに `App::uses()` は必要ありません (`CakePHP` 規約は `config/` と `tests/` をチェックしません)。 |
| `Squiz.Classes.ClassFileName.NoMatch` | `*/config/*`、`*/tests/TestCase/*`、`*/Test/Case/*` | PSR-4 は、クラス名と同じ名前のファイルからしかクラスを読み込めません。設定ファイルの名前はそのままで、テストファイルではテストダブルを宣言してもかまいません。 |

`SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedNameWithoutNamespace` は `CakePHP2`
と同じく除外したままなので、設定ファイルやテンプレートなど名前空間のないファイルでは、引き続き `\Exception` と書けます。
この sniff を `phpcbf` で直すと、そうしたファイルの属性が壊れてしまうためです。`#[\AllowDynamicProperties]` が
`[AllowDynamicProperties]` になり、構文エラーになります。

<a id="class-names-in-annotations"></a>

### アノテーションのクラス名

アノテーションでも、コードと同じく、`use` でインポートした短い名前でクラスを書きます。

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

| Sniff | 設定 |
| --- | --- |
| `SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly` | `searchAnnotations` true: `@param \Cake\Model\Model $Model` や `@throws \InvalidArgumentException` のような、アノテーションの完全修飾名もコードと同じく報告します。`CakePHP` 規約がコードで求めているのと同じく、グローバルなクラスもインポートします (`use InvalidArgumentException;`)。`phpcbf` は `use` 文を追加し、短い名前に書き換えます。 |

アノテーションに完全修飾名を求める `SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation.NonFullyQualifiedClassName`
は、`CakePHP2` と同じく除外したままです。アノテーションでしか使っていない `use` 文を使われているとみなすのも、`CakePHP2` と同じです。

<a id="order-of-the-types-in-annotations"></a>

### アノテーションの型の順序

`@param`、`@return`、`@var` の型の順序 (`null|string` ではなく `string|null`) をチェックする
`CakePHP.Commenting.TypeHint` の代わりに、どちらの規約でも `CakePHP2.Commenting.TypeHint` を使います。チェックする内容は
同じですが、メッセージは `CakePHP2.Commenting.TypeHint.IncorrectFormat` として報告されるので、除外するときは
この名前を指定してください。元の sniff と同じく、`tests/` はチェックしません。

元の sniff は、同じ回でほかの sniff がドキュメントコメントを書き換えると、`phpcbf` を致命的エラーで止めてしまいます。
たとえば `CakePHP2Namespace` で `phpcbf` がアノテーションのクラス名をインポートするときは、`ReferenceUsedNamesOnly` が
ドキュメントコメント全体を書き換え、その途中のコメントをこの sniff が解析します。
代わりの sniff は、書き換え中のドキュメントコメントをスキップして `phpcbf` の次の回でチェックするので、`phpcbf` は
引き続き型を並べ替えます。

<a id="adding-exceptions"></a>

## 例外の追加

プロジェクト固有の例外は、規約 (`CakePHP2` または `CakePHP2Namespace`) の後に追加します。一部のファイルで sniff を無効にするには、次のようにします。

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

1. 依存パッケージを置き換えます。

   ```bash
   composer remove --dev cakephp/cakephp-codesniffer
   composer require --dev pieceofcake2/phpcs-cakephp2
   ```

2. `phpcs.xml` で、`<rule ref="CakePHP">` のブロックと、この規約が対応するようになったルール
   (`MultipleStatementAlignment`、`FunctionComment.Missing`、`MultipleClasses`、コントローラー、シェル、
   ビヘイビアに対する `NotCamelCaps`、`ReturnTypeHint.MissingNativeTypeHint`、`database.php` に対する
   `NotPascalCase` と `acl.ini.php` に対する `Unreachable`) を `<rule ref="CakePHP2"/>` に置き換えます。
   `<config>`、`<arg>`、`<file>` の項目と、自分のファイルに対する例外はそのまま残してください。

3. `vendor/bin/phpcs` を実行します。1.1.0 からは、ネイティブの戻り値の型がないテストメソッドが報告されます
   (`CakePHP2.TypeHints.TestReturnTypeHint`)。`vendor/bin/phpcbf` で `: void` を追加し、ほかの戻り値の型は
   手で追加してください。

以前の設定には、`tests/TestCase/` でネイティブの戻り値の型を必須にするためのルール (include pattern 付きの
`ReturnTypeHint.MissingNativeTypeHint`) がありました。しかし `CakePHP` 規約は `tests/` では戻り値の型をチェックしないので、
このルールは一度も効果がありませんでした。その役割は `CakePHP2.TypeHints.TestReturnTypeHint` が担います。

<a id="development"></a>

## 開発

```bash
composer install

# Run the tests: phpcs with each standard on its files in fixtures/
vendor/bin/phpunit

# Check the coding standard of the tests
vendor/bin/phpcs
```

`composer test` は両方を実行します。`fixtures/` には規約ごとのディレクトリ（`fixtures/CakePHP2/` など）があり、
`valid/` には合格しなければならない CakePHP 2 のコードが、`invalid/` には既知の違反を含むコードがあります。
`tests/RulesetTest.php` には、ファイルごとに期待される sniff が列挙されています。`CakePHP` 規約は `tests/` では一部の sniff をスキップするので、フィクスチャは `tests/` の
外に置いています。
