# CHANGELOG

## 1.1.0

- `CakePHP2.TypeHints.TestReturnTypeHint`: methods and functions in `*/tests/TestCase/*` and `*/Test/Case/*`
  need a native return type (`setUp(): void`, `testFoo(): void`, `fooProvider(): array`). The `CakePHP`
  standard does not check return types in `tests/`, so the rule of the old configurations that was meant to
  require them never took effect. `phpcbf` adds `: void` to methods that return nothing.

## 1.0.0

- The `CakePHP2` standard: the `CakePHP` standard of cakephp/cakephp-codesniffer 5.x with the exceptions that
  CakePHP 2 applications and plugins need
