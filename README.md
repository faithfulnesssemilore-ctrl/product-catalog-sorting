# Product Catalog Sorting

A plain-PHP library that sorts a product catalog using interchangeable,
chainable, and registry‑driven sorting strategies. Built to satisfy a
real‑world A/B testing requirement: different teams can add new sorters
without ever modifying the shared `Catalog` class or any existing sorter.

---

## Table of Contents

1 [What this solves](#what-this-solves)
2 [Requirements satisfied](#requirements-satisfied)
3 [Installation](#installation)
4 [Quick start](#quick-start)
5 [Architecture overview](#architecture-overview)
6 [File structure](#file-structure)
7 [SOLID principles](#solid-principles)
8 [Registry and configuration](#registry-and-configuration)
9 [Edge cases handled](#edge-cases-handled)
10 [Testing](#testing)
11 [Adding a new sorter](#adding-a-new-sorter)


---

## What this solves

The product manager wants to A/B test different product sorting strategies
across different sections of the website – homepage by popularity, sale
page by price, new‑arrivals by date – with each section owned by a
different team.

This library provides a `Catalog` that stays **closed for modification**
while allowing new sorting rules to be added at any time by simply
creating a new class and registering it with a string key.

---

## Requirements satisfied

 Sort by price (ascending and descending)
 Sort by sales‑per‑view ratio (with zero‑views handling)
Sort direction configurable at instantiation (asc / desc)
Chaining – tie‑breaking with a second (or more) sorters
-`SorterRegistry – resolve a sorter by a string key, driven by config/query string
Catalog never changes, regardless of how many sorters are added
 Automated tests covering all required scenarios

---

## Installation

bash
git clone  https://github.com/faithfulnesssemilore-ctrl/product-catalog-sorting.git
cd product-catalog-sorting
composer install

## Archtecure design

 Catalog -----> depends on -----> SorterInterface  (the contract)
    │                                    ▲
    │                                    │
    │                --------------------┼--------------------
    │                │                   │                   │
    └--------->  │  AbstractSorter (abstract)       │ ChainedSorter
                     │   - direction: SortDirection          │   - sorters[]
                     │   - extractValue(p): float (abstract) │   delegates compare()
                     │            ▲                         │
                     │   ┌────────┼────────┐                │
                     │ PriceSorter  SalesPerViewSorter  DateSorter
                     │
              SorterRegistry   (string key → SorterInterface)

Catalog holds products and delegates sorting – never touched again.

SorterInterface is the single dependency of Catalog and ChainedSorter.

AbstractFieldSorter centralises direction handling and the usort loop. Concrete field sorters only implement extractValue() (return a float).

ChainedSorter holds an ordered list of sorters; its compare() asks them one by one until a non‑zero result (tie‑break).

SorterRegistry is a thin hash‑map wrapper for constant‑time sorter lookup by key.

## File structure
src/
|>Catalog.php
|> Sorter/
│   |> SorterInterface.php
│   |>AbstractFieldSorter.php
│   |> PriceSorter.php
│   |> SalesPerViewSorter.php
│   |> DateSorter.php          (tie‑breaker example)
│   |> ChainedSorter.php
│   ├── SortDirection.php       (backed enum)
│   └── SorterRegistry.php
|
tests/
├── PriceSorterTest.php
├── SalesPerViewSorterTest.php
├── ChainedSorterTest.php
└── SorterRegistryTest.php
examples/
├── basic-usage.php
└── registry-and-chaining.php
Each directory owns one concern. Adding a new sorter only touches Sorter/ and (optionally) a registration line in your bootstrap – never Catalog.

## SOLID principles
Principle	Where it appears
Single Responsibility	Catalog stores products and delegates sorting. AbstractSorter owns comparison mechanics. Each concrete sorter owns exactly one field’s meaning. SorterRegistry only maps keys to sorters.
Open/Closed	Catalog is open for extension (any SorterInterface works) and closed for modification (never edited). New sorters are added without changing existing code.
Liskov Substitution	Any SorterInterface implementation can replace another without breaking callers. ChainedSorter chains any mix of sorters transparently.
Interface Segregation	The interface exposes only sort() and compare() – nothing implementers don't need. ChainedSorter implements the interface directly instead of inheriting unrelated machinery.
Dependency Inversion	High‑level Catalog and ChainedSorter depend on SorterInterface (abstraction), never on concrete sorters.

## Registry and configuration

The SorterRegistry is a simple associative array (string → SorterInterface).
It supports:

php
$registry = new SorterRegistry();
$registry->register('price_asc', new PriceSorter('asc'));
$registry->register('best_sellers', new SalesPerViewSorter('desc'));

$key = $_GET['sort'] ?? 'price_asc';
$sorter = $registry->get($key);      // throws UnknownSorterException if key missing
Why an associative array? The only operation is “given a key, return the object” – a hash map gives constant‑time lookup and needs no extra structure.

## Edge cases handled

Zero views – SalesPerViewSorter returns 0.0 ratio instead of dividing by zero. Products with zero views sort safely and do not pollute top results.

Identical sort values (ties) – ChainedSorter falls back to the next sorter in the chain. If all sorters return 0, PHP 8.0+’s stable usort preserves original order.

Invalid direction – SortDirection::fromString() throws a InvalidSortDirectionException with a descriptive message.

Empty product list – sort() returns an empty array immediately, no warnings.

Missing array keys – extractValue() uses ?? defaults to avoid undefined index notices.

## Testing
Framework: PHPUnit (chosen for its zero‑config familiarity in any PHP CI pipeline).

Running tests:

bash
composer test
# or directly: ./vendor/bin/phpunit tests/
Covered scenarios:

Price sorting ascending and descending

Sales‑per‑view sorting (both directions)

Zero‑views product ratio = 0.0, sorted correctly

Chained sorting: price tie broken by date (newest first)

Chaining: no tie does not consult later sorters

Invalid direction throws custom exception

Original product array never mutated after sorting

Registry: register, resolve, has(), unknown key throws exception

All test files are located in tests/, mirroring the src/ structure.

## Adding a new sorter
Create a new class in src/Sorter/ that extends AbstractSorter (for numeric fields) or implements SorterInterface directly (for custom logic).

Implement extractValue() (return a float) or compare() and sort().
 Register it in your bootstrap or config with a string key.

Example – a PopularitySorter:

php
class PopularitySorter extends AbstractSorter
{
    protected function extractValue(array $product): float
    {
        return (float) ($product['clicks'] ?? 0);
    }
}
That’s it. Catalog, existing sorters, and the registry remain unchanged.
