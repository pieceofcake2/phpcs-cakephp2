# CHANGELOG

## Unreleased

- The `CakePHP2Namespaced` standard, for CakePHP 2 applications and plugins whose classes are in namespaces
  (`App\Controller\PostsController`), as CakePHP 2.13 allows. It is the `CakePHP2` standard with the
  namespace rules turned back on: classes need a namespace, a class file must not call `App::uses()` (or
  have other side effects) and must be named after its class, and annotations name classes by their short,
  imported names (`use Cake\Model\Model;` and `@param Model $Model`, not `@param \Cake\Model\Model $Model`).
  A `use` statement for a class that only annotations name counts as used. The classes in `config/`
  (`DATABASE_CONFIG`, the schema classes) stay global. `CakePHP2` is unchanged.
  `ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedNameWithoutNamespace` stays excluded, because its fix in
  `phpcbf` turns `#[\AllowDynamicProperties]` in a file without a namespace into `[AllowDynamicProperties]`.
- `CakePHP2Namespaced.Commenting.TypeHint` replaces `CakePHP.Commenting.TypeHint` in `CakePHP2Namespaced`.
  It checks the same order of types in annotations (`string|null`), but skips a doc comment that another
  sniff is rewriting in the same `phpcbf` pass. The original stops `phpcbf` with a fatal error when the class
  names of an annotation are imported. Exclude the message as
  `CakePHP2Namespaced.Commenting.TypeHint.IncorrectFormat`.

## 1.1.0

- `CakePHP2.TypeHints.TestReturnTypeHint`: methods and functions in `*/tests/TestCase/*` and `*/Test/Case/*`
  need a native return type (`setUp(): void`, `testFoo(): void`, `fooProvider(): array`). The `CakePHP`
  standard does not check return types in `tests/`, so the rule of the old configurations that was meant to
  require them never took effect. `phpcbf` adds `: void` to methods that return nothing.

## 1.0.0

- The `CakePHP2` standard: the `CakePHP` standard of cakephp/cakephp-codesniffer 5.x with the exceptions that
  CakePHP 2 applications and plugins need
