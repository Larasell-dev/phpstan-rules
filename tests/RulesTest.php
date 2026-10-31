<?php

declare(strict_types=1);

/**
 * Runs PHPStan with the rules on a fixture and returns the reported lines per file.
 *
 * @return array<string, list<int>>
 */
function reportedLines(string $fixture): array
{
    $command = implode(' ', array_map('escapeshellarg', [
        PHP_BINARY, __DIR__.'/../vendor/bin/phpstan', 'analyse', '--configuration='.__DIR__.'/phpstan.neon',
        '--error-format=json', '--no-progress', '--memory-limit=512M', __DIR__.'/Fixtures/'.$fixture,
    ]));

    // PHPStan writes hints for humans to stderr; only the JSON on stdout matters here.
    exec($command.' 2>/dev/null', $output);
    $result = json_decode(implode("\n", $output), true, flags: JSON_THROW_ON_ERROR);

    expect($result['errors'])->toBe([]);

    return array_map(
        fn (array $file): array => array_column($file['messages'], 'line'),
        $result['files'],
    );
}

/**
 * The lines of a fixture marked with a "// forbidden" comment.
 *
 * @return list<int>
 */
function markedLines(string $fixture): array
{
    $lines = file(__DIR__.'/Fixtures/'.$fixture);

    $indexes = array_keys(array_filter($lines, fn (string $line): bool => str_contains($line, '// forbidden')));

    return array_map(fn (int $index): int => $index + 1, $indexes);
}

it('forbids mutable Carbon classes as parameter types, and nothing else', function () {
    $reported = reportedLines('Dates.php');

    expect(array_values($reported)[0] ?? [])->toBe(markedLines('Dates.php'));
});
