Faithfulness Semilore
Fullstack Intern Program						                               Product Catalog Sorting — Engineering Task
Task Submission 

Task Summary


The product manager wants to A/B test different product sorting strategies across different sections of the site — homepage by popularity, sale page by price, new-arrivals page by date — with different teams owning their own section's sorting logic.	      	Testing
 Use either PHPUnit or Pest — your choice. Justify why you picked one over the other in your submission notes. Tests must cover at minimum:		
●  Price sorting ascending and descending						
●  Sales-per-view sorting
●  Chained sorting						
●  Zero views edge case									
●  Invalid direction input						
Questions to answer alongside your code		
●  Which SOLID principles does your design satisfy, and where specifically?		●  Why did you choose the data structure you used for the registry?			
●  What happens if two products have the same price in a chained sorter — and how does your code handle it
●  If you were to load sorters automatically without manually registering each one, how would you approach that?
 Design Decision
Every sorter exposes both `sort()` and `compare(a, b)`. Exposing `compare()` — rather than only `sort()` — is what makes chaining possible: `ChainedSorter` asks each child sorter’s `compare()` in priority order and only consults the next sorter when the current one reports an exact tie (`0`). Two independent full sorts run back‑to‑back cannot do this correctly, because the second sort has no concept that the first sort’s ordering was meaningful.
A shared AbstractFieldSorter base class
`PriceSorter`, `SalesPerViewSorter`, `PopularitySorter`, and `DateSorter` are all, underneath, “extract one number from a product, compare it, flip for direction”. That mechanism is written once in `AbstractFieldSorter`; each concrete sorter only supplies its own `extractValue()`. This keeps the field‑specific business logic (what a price is, how a ratio is computed, how a date becomes a number) completely separate from the generic comparison mechanism.
 A backed enum for direction
`SortDirection::ASC` / `::DESC` replaces loose `'asc'`/`'desc'` strings. Validation happens once in `SortDirection::fromString()`, which throws an `\InvalidArgumentException` on bad input. After that point, nothing downstream needs to re‑check whether a direction value is valid — it already is, by construction.
An associative array for the registry
`SorterRegistry` is a thin wrapper around a PHP associative array (a hash map). The only operation ever performed against it is “given a key, return the matching sorter”, which is exactly the access pattern a hash map is built for — constant‑time lookup regardless of how many sorters are registered. No ordering or secondary lookup requirement exists, so a more elaborate structure would add complexity without benefit.
The zero‑views edge case
`sales_count / views_count` with `views_count == 0` is the one place this codebase could misbehave silently — PHP returns `INF` for a float division by zero rather than throwing, and `INF` would float to the top of any descending sort regardless of whether the product has ever sold anything. `SalesPerViewSorter` defines a zero‑view product’s ratio as exactly `0.0`: nobody has seen it, so it should not outrank products people have genuinely engaged with.
 Architecture Overview

Catalog depends only on the SorterInterface abstraction — never on any concrete sorter class. That single dependency arrow is what satisfies the Open/Closed Principle for this task: `Catalog` is open for extension (any new `SorterInterface` implementation works immediately) and closed for modification (it is never edited again).
 Catalog ---------------------> SorterInterface  <interface>   (depends on the abstraction)        ^               | implement
                  |                    |                      |
       AbstractFieldSorter      ChainedSorter         (any future
         (abstract)                      (delegates to other    team sorter)
          ^   ^   ^   ^   
SorterInterface instances)
          |   |   |   |         
    PriceSorter      PopularitySorter    DateSorter
     SalesPerView Sorter
 SorterRegistry: string key ----> SorterInterface instance
   (associative array / hash map lookup, used to resolve a
    sorter from a config value or query-string parameter)
```
Why PHPUnit over Pest
I picked PHPUnit. Pest is nicer to read line-by-line, but it's a thin layer on top of PHPUnit that leans on globally available functions (it(), test(), expect()) and its own bootstrap conventions. For a task being reviewed by someone who'll just run composer install and expect tests to run with zero extra setup or configuration decisions, PHPUnit is the safer default — every PHP developer and every CI pipeline already knows what to do with it, no extra config file philosophy to explain. The trade-off is verbosity ($this->assertSame instead of expect(...)->toBe(...)), and I think that's a fair price for one less thing a reviewer has to think about.
Folder structure, and why each piece lives where it does

src/
  Catalog.php              <- the one class the task fixes in place
  Sorter/
    SorterInterface.php     <- the contract everything else depends on
    AbstractFieldSorter.php <- shared comparison/sort mechanics
    PriceSorter.php
    SalesPerViewSorter.php
    DateSorter.php
    ChainedSorter.php
    SortDirection.php        <- the asc/desc enum + validation
  Registry/
    SorterRegistry.php
  Exception/
    InvalidSortDirectionException.php
    UnknownSorterException.php
tests/
  CatalogTest.php
  PriceSorterTest.php
  SalesPerViewSorterTest.php
  ChainedSorterTest.php
  SorterRegistryTest.php
examples/team-sections.php
Sorter/ is its own folder because it's where most of the actual business logic lives, and because that's where a new team adding a sorter will be working — they shouldn't need to go anywhere near Catalog.php or Registry/ to do their job. Exception/ is split out on its own because exceptions are part of the public contract of this code (callers are expected to catch them by name) — burying them inside Sorter/ would make them easy to miss when skimming the codebase for "what can go wrong here."
Which SOLID principles this satisfies, and exactly where
Single Responsibility Principle. Catalog only stores products and delegates sorting — it has one reason to change: if how products are stored changes. AbstractFieldSorter only owns comparison mechanics — its one reason to change is if that mechanism itself is wrong. PriceSorter and SalesPerViewSorter each own exactly one piece of business knowledge (which field, and how to read it) — their one reason to change is if that field's meaning changes. SorterRegistry only owns the key-to-object mapping. Nothing in this codebase has two unrelated jobs.
Open/Closed Principle. Catalog is open for extension (any new class implementing SorterInterface works immediately) and closed for modification (it will never be edited again to support a new sort strategy). This is the literal answer to the constraint "Catalog must not change regardless of how many sorters are added later."
Liskov Substitution Principle. Anywhere a SorterInterface is expected, any implementation — PriceSorter, ChainedSorter, a sorter written by a different team next year — can be substituted without breaking the caller. Catalog::getProducts() doesn't behave differently, or need different handling, depending on which concrete sorter it received.
Interface Segregation Principle. SorterInterface only has the two methods a sorter actually needs (sort, compare) — nothing is forced onto implementers that they don't use. ChainedSorter doesn't extend AbstractFieldSorter precisely because that class's machinery (extract one field, compare two numbers) isn't relevant to what ChainedSorter does, and forcing it to inherit that anyway would mean implementing methods it has no real use for.
Dependency Inversion Principle. Catalog depends on SorterInterface (an abstraction), never on a concrete sorter class. ChainedSorter depends on SorterInterface too, not on AbstractFieldSorter — which is exactly why it can chain a PriceSorter next to a hypothetical future sorter that has nothing to do with AbstractFieldSorter at all.
Why an associative array for the registry
The only operation SorterRegistry ever needs to perform is "given a key, give me back the matching sorter" — that's it. A plain PHP associative array ($sorters[$key] = $sorter) is a hash map under the hood, and a hash map gives roughly constant-time lookup by key regardless of how many sorters get registered. There's no requirement anywhere in the task to iterate sorters in a particular order, or look one up by anything other than its key, so reaching for an ordered list or any heavier structure would just be solving a problem nobody has.
What happens when two products tie in a chained sorter
ChainedSorter::compare() asks its sorters one at a time, in the order they were passed in. The first sorter whose compare() returns a non-zero result wins outright — its answer is final, and no later sorter in the chain is even consulted. Only when a sorter returns 0 (meaning, as far as it's concerned, the two products are equal) does the next sorter in the chain get asked. If every sorter in the chain returns 0 — total tie all the way down — PHP's usort() is a stable sort as of PHP 8.0, so the two products simply keep whatever relative order they already had in the original array. Nothing crashes, nothing is arbitrarily reshuffled.
Concretely: new ChainedSorter(new PriceSorter('asc'), new DateSorter('desc')) on two products sharing a price falls through to comparing their created dates, and the newer one wins. Two products that don't share a price never reach the date comparison at all — PriceSorter's non-zero verdict short-circuits the rest of the chain.
How I'd auto-load sorters instead of registering each one by hand
Right now, wiring up the registry means one explicit ->register(...) call per sorter, written by hand, somewhere at application boot. That's fine at the scale of four or five sorters, but it does mean every team adding a sorter also has to remember to go add a registration line somewhere central — which is a small crack in the "teams never touch shared code" goal.
The way I'd remove that crack: give every concrete sorter class a small, explicit piece of self-describing metadata — a PHP 8 attribute right above the class, something like #[RegisteredAs('price_asc')] — and write a one-time discovery step at boot that scans the Sorter/ directory (via glob() plus PHP's ReflectionClass), checks which classes implement SorterInterface, reads that attribute off each one, instantiates it, and registers it under the name the attribute declares. Teams would then add a sorter by dropping a new file in Sorter/ and nothing else — no separate file to remember to edit.
I didn't build this for the actual submission, on purpose: it adds real complexity (reflection, attribute parsing, a discovery step that has to run somewhere and has its own failure modes if a class is malformed) for a scale of "four or five sorters" that doesn't need it yet. Explicit registration is also just easier to read and debug — you can grep for every registered key in one file. I'd reach for auto-discovery the moment the number of sorters, or the number of independent teams shipping them, made the manual list itself become the maintenance burden — not before.
The zero-views edge case, explicitly
sales_count / views_count with views_count == 0 is the one piece of arithmetic in this codebase that can misbehave if left alone — PHP returns INF for a float division by zero rather than throwing, and an INF would float to the top of any descending sort regardless of whether the product has ever actually sold anything. SalesPerViewSorter treats a zero-view product as having a ratio of exactly 0.0 instead: nobody has seen it, so it shouldn't outrank products people have genuinely engaged with. That's a judgment call, not the only valid one — an equally defensible alternative is "always sort zero-view products to the very bottom, regardless of direction" — but 0.0 is the simplest rule that's still correct, and it's one sentence to explain to the next engineer who reads this file.
