<?php
declare(strict_types=1);

namespace PieceOfCake2\PhpcsCakePHP2\Test;

use PHPUnit\Framework\TestCase;

/**
 * Runs phpcs with each standard on its files in fixtures/ and compares the reported sniffs.
 *
 * fixtures/<standard>/valid holds code that must pass; fixtures/<standard>/invalid holds code with known violations.
 */
class RulesetTest extends TestCase
{
    /**
     * Files per standard, with the sniff codes expected for each file
     *
     * @var array<string, array<string, array{string, list<string>}>>
     */
    private const FILES = [
        'CakePHP2' => [
            'controller actions with underscores' => ['valid/Controller/PostsController.php', []],
            'behavior callbacks' => ['valid/Model/Behavior/SluggableBehavior.php', []],
            'shell with assignment in condition' => ['valid/Console/Command/ReportShell.php', []],
            'DATABASE_CONFIG' => ['valid/Config/database.php', []],
            'acl.ini.php' => ['valid/Config/acl.ini.php', []],
            'test case without docblocks and with a test double' => ['valid/tests/TestCase/Model/PostTest.php', []],
            'underscored method and wide alignment outside controllers' => [
                'invalid/Lib/BadStyle.php',
                [
                    'Generic.Formatting.MultipleStatementAlignment.Incorrect',
                    'PSR1.Methods.CamelCapsMethodName.NotCamelCaps',
                    'SlevomatCodingStandard.Whitespaces.DuplicateSpaces.DuplicateSpaces',
                ],
            ],
            'test methods and test doubles without native return types' => [
                'invalid/tests/TestCase/MissingReturnTypeTest.php',
                ['CakePHP2.TypeHints.TestReturnTypeHint.MissingNativeTypeHint'],
            ],
            'public method with a leading underscore' => [
                'invalid/Model/Post.php',
                ['CakePHP.NamingConventions.ValidFunctionName.PublicWithUnderscore'],
            ],
        ],
        'CakePHP2Namespaced' => [
            'controller with a use statement only for an annotation' => ['valid/src/Controller/PostsController.php', []],
            'model with an imported global exception class' => ['valid/src/Model/Post.php', []],
            'behavior with short class names in annotations' => ['valid/src/Model/Behavior/SluggableBehavior.php', []],
            'shell with a class in the same namespace' => ['valid/src/Console/Command/ReportShell.php', []],
            'test case with a test double' => ['valid/tests/TestCase/Model/PostTest.php', []],
            'global DATABASE_CONFIG' => ['valid/config/database.php', []],
            'global schema class' => ['valid/config/Schema/schema.php', []],
            'class without a namespace' => [
                'invalid/src/Model/Post.php',
                ['PSR1.Classes.ClassDeclaration.MissingNamespace'],
            ],
            'App::uses() next to a class' => [
                'invalid/src/Model/Comment.php',
                ['PSR1.Files.SideEffects.FoundWithSymbols'],
            ],
            'unused use statement' => [
                'invalid/src/Controller/CommentsController.php',
                ['SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse'],
            ],
            'fully qualified class names in annotations' => [
                'invalid/src/Model/Behavior/CounterBehavior.php',
                ['SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName'],
            ],
            'file not named after its class' => [
                'invalid/src/Utility/Slugger.php',
                ['Squiz.Classes.ClassFileName.NoMatch'],
            ],
            'leading backslash in a file without a namespace' => [
                'invalid/config/bootstrap.php',
                ['SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedNameWithoutNamespace'],
            ],
        ],
    ];

    /**
     * @return array<string, array{string, string, list<string>}>
     */
    public static function fileProvider(): array
    {
        $cases = [];
        foreach (self::FILES as $standard => $files) {
            foreach ($files as $name => [$file, $expected]) {
                $cases[$standard . ': ' . $name] = [$standard, $file, $expected];
            }
        }

        return $cases;
    }

    /**
     * @param string $standard Standard to check the file with
     * @param string $file File in fixtures/<standard>/
     * @param list<string> $expected Sniff codes expected to be reported
     * @dataProvider fileProvider
     */
    public function testFile(string $standard, string $file, array $expected): void
    {
        $this->assertSame($expected, $this->sniff($standard, $file));
    }

    /**
     * phpcbf imports the fully qualified class names of annotations and writes their short names
     */
    public function testFixFullyQualifiedNamesInAnnotations(): void
    {
        $root = dirname(__DIR__);
        $file = $root . '/fixtures/CakePHP2Namespaced/invalid/src/Model/Behavior/CounterBehavior.php';
        $command = sprintf(
            '%s %s -q --standard=CakePHP2Namespaced --stdin-path=%s - < %s 2>&1',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($root . '/vendor/bin/phpcbf'),
            escapeshellarg($file),
            escapeshellarg($file),
        );
        exec($command, $output);
        $fixed = implode("\n", $output);

        $this->assertStringContainsString("use Cake\\Model\\Model;\nuse Cake\\Model\\ModelBehavior;\nuse InvalidArgumentException;\n", $fixed);
        $this->assertStringContainsString('@param Model $Model Model using this behavior', $fixed);
        $this->assertStringContainsString('@throws InvalidArgumentException When the model has no table', $fixed);
    }

    /**
     * @param string $standard Standard to check the file with
     * @param string $file File in fixtures/<standard>/
     * @return list<string> Sorted, unique sniff codes reported for the file
     */
    private function sniff(string $standard, string $file): array
    {
        $root = dirname(__DIR__);
        $command = sprintf(
            '%s %s -q --standard=%s --report=json --no-cache %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($root . '/vendor/bin/phpcs'),
            escapeshellarg($standard),
            escapeshellarg($root . '/fixtures/' . $standard . '/' . $file),
        );
        exec($command, $output);
        $report = json_decode(implode("\n", $output), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame(1, $report['totals']['files'] ?? count($report['files']), 'phpcs checked the file');

        $sources = [];
        foreach ($report['files'] as $result) {
            foreach ($result['messages'] as $message) {
                $sources[] = $message['source'];
            }
        }
        $sources = array_values(array_unique($sources));
        sort($sources);

        return $sources;
    }
}
