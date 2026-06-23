Product Catalog Sorting — Engineering Task
Context
Our product manager wants to A/B test different product sorting strategies across different sections of the website. The homepage might sort by popularity, the sale page by price, and a new arrivals page by date — and each team owns their own section.
Given
$products = [
    ['id' => 1, 'name' => 'Alabaster Table', 'price' => 12.99, 'created' =>
'2019-01-04', 'sales_count' => 32,   'views_count' => 730],
    ['id' => 2, 'name' => 'Zebra Table',     'price' => 44.49, 'created' =>
'2012-01-04', 'sales_count' => 301,  'views_count' => 3279],
    ['id' => 3, 'name' => 'Coffee Table',    'price' => 10.00, 'created' =>
'2014-05-28', 'sales_count' => 1048, 'views_count' => 20123],
];
$productPriceSorter        = ?
$productSalesPerViewSorter = ?
$catalog = new Catalog($products);
$productsSortedByPrice        = $catalog->getProducts($productPriceSorter);
$productsSortedBySalesPerView =
$catalog->getProducts($productSalesPerViewSorter);
Task
Extend the code to make Catalog::getProducts() work as shown above. Your solution must:
1. Support sorting by price and by sales-per-view ratio (sales_count / views_count)
2. Be extensible — different teams should be able to add new sorters without modifying Catalog or any existing sorter
3. Support sort direction (ascending and descending) configurable at instantiation
4. Support chaining — e.g. sort by price first, then break ties by newest created date
5. Handle edge cases: products with zero views, identical sort values, invalid direction
input
6. Implement a SorterRegistry where sorters are registered by name and resolved by key — so sorting can be driven by a config value or query string parameter
Testing
Use either PHPUnit or Pest — your choice. Justify why you picked one over the other in your
submission notes. Tests must cover at minimum:
● Price sorting ascending and descending
● Sales-per-view sorting
● Chained sorting
● Zero views edge case
● Invalid direction input
Questions to answer alongside your code
● Which SOLID principles does your design satisfy, and where specifically?
● Why did you choose the data structure you used for the registry?
● What happens if two products have the same price in a chained sorter — and how does
your code handle it?
● If you were to load sorters automatically without manually registering each one, how
would you approach that?
Constraints
● Plain PHP, no framework
● You may pull in external libraries via Composer
● Catalog must not change regardless of how many sorters are added later
● Structure your code across multiple files — your file and folder organization is part of
what's being evaluated