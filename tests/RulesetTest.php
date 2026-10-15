<?php
declare(strict_types=1);

namespace PieceOfCake2\PhpcsCakePHP2\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Runs phpcs with the CakePHP2 standard on the files in fixtures/ and compares the reported sniffs.
 *
 * fixtures/valid holds CakePHP 2 code that must pass; fixtures/invalid holds code with known violations.
 */
class RulesetTest extends TestCase
{
    /**
     * @return array<string, array{string, list<string>}>
     */
    public static function fileProvider(): array
    {
        return [
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
            'public method with a leading underscore' => [
                'invalid/Model/Post.php',
                ['CakePHP.NamingConventions.ValidFunctionName.PublicWithUnderscore'],
            ],
        ];
    }

    /**
     * @param string $file File in fixtures/
     * @param list<string> $expected Sniff codes expected to be reported
     */
    #[DataProvider('fileProvider')]
    public function testFile(string $file, array $expected): void
    {
        $this->assertSame($expected, $this->sniff($file));
    }

    /**
     * @param string $file File in fixtures/
     * @return list<string> Sorted, unique sniff codes reported for the file
     */
    private function sniff(string $file): array
    {
        $root = dirname(__DIR__);
        $command = sprintf(
            '%s %s -q --standard=CakePHP2 --report=json --no-cache %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($root . '/vendor/bin/phpcs'),
            escapeshellarg($root . '/fixtures/' . $file),
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
