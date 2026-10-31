# Larasell PHPStan rules

PHPStan rules shared by the Larasell packages, so a mistake one package made is caught in all of
them.

## Installation

```bash
composer require --dev larasell-dev/phpstan-rules
```

Then include the rules in `phpstan.neon`:

```neon
includes:
    - vendor/larasell-dev/phpstan-rules/extension.neon
```

With [phpstan/extension-installer](https://github.com/phpstan/extension-installer), skip the include:
the rules are registered automatically.

## Rules

### Mutable Carbon parameter types

Apps can switch Laravel's dates to `Carbon\CarbonImmutable` with
`Date::use(CarbonImmutable::class)`. `CarbonImmutable` extends neither `Illuminate\Support\Carbon`
nor `Carbon\Carbon`, so a parameter typed with one of those throws a `TypeError` as soon as such an
app passes it a date, for example a table column's `map()` callback. Type-hint
`Carbon\CarbonInterface` instead:

```php
// Reported
TextColumn::make('created_at')->map(fn (?Carbon $createdAt) => $createdAt?->diffForHumans());

// Fine
TextColumn::make('created_at')->map(fn (?CarbonInterface $createdAt) => $createdAt?->diffForHumans());
```

Only parameter types are reported. Returning, creating or calling these classes, e.g.
`Carbon::today()`, is fine.

The rules are configured for [spaze/phpstan-disallowed-calls](https://github.com/spaze/phpstan-disallowed-calls)
in [`rules.neon`](rules.neon).

## Development

```bash
composer test       # Pest: runs PHPStan with the rules on tests/Fixtures
composer lint       # Pint
npm run commitlint  # Commitlint on the last commit
```

Each fixture marks the lines a rule must report with a `// forbidden` comment; every other line must
stay clean.

Changes that make PHPStan report more get an upgrade note in `breaking-changes/` and a `!` in the
pull request title, e.g. `feat!: ...`; see `breaking-changes/AGENT.md`. CI checks that both go
together. release-please opens the release pull requests.
