<?php

namespace Larasell\PhpstanRules\Tests\Fixtures;

use Carbon\Carbon as CarbonCarbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Lines marked "forbidden" must be reported; every other line must not be.
 */
final class Dates
{
    public function __construct(
        public ?Carbon $publishedAt = null, // forbidden
    ) {}

    /**
     * @return list<\Closure>
     */
    public function columns(): array
    {
        return [
            fn (?Carbon $createdAt): ?string => $createdAt?->diffForHumans(), // forbidden
            function (CarbonCarbon $expiresAt): string { // forbidden
                return $expiresAt->toDateString();
            },
            fn (?CarbonInterface $updatedAt): ?string => $updatedAt?->diffForHumans(),
            fn (CarbonImmutable $deletedAt): string => $deletedAt->toDateString(),
        ];
    }

    public function format(Carbon|string $date): string // forbidden
    {
        return (string) $date;
    }

    public function accepts(CarbonInterface $date): CarbonInterface
    {
        return $date;
    }

    public function today(): Carbon
    {
        return Carbon::today();
    }

    public function parse(string $date): CarbonCarbon
    {
        return new CarbonCarbon($date);
    }
}
