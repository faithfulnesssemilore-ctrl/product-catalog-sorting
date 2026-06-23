<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Catalog;
use App\Sorter\PopularitySorter;
use App\Sorter\PriceSorter;
use App\Sorter\DateSorter;
use App\Sorter\SorterRegistry;

$products = [
    ['id' => 1, 'name' => 'Alabaster Table', 'price' => 12.99, 'created' => '2019-01-04', 'sales_count' => 32,   'views_count' => 730],
    ['id' => 2, 'name' => 'Zebra Table',     'price' => 44.49, 'created' => '2012-01-04', 'sales_count' => 301,  'views_count' => 3279],
    ['id' => 3, 'name' => 'Coffee Table',    'price' => 10.00, 'created' => '2014-05-28', 'sales_count' => 1048, 'views_count' => 20123],
];

$registry = new SorterRegistry();
$registry->register('homepage', new PopularitySorter('desc'));   // most sales first
$registry->register('sale_page', new PriceSorter('asc'));        // cheapest first
$registry->register('new_arrivals', new DateSorter('desc'));     // newest first

$catalog = new Catalog($products);

echo "=== HOMEPAGE (popularity - sales count descending) ===" . PHP_EOL;
foreach ($catalog->getProducts($registry->get('homepage')) as $p) {
    echo "- {$p['name']} : sales_count = {$p['sales_count']}" . PHP_EOL;
}

echo PHP_EOL . "=== SALE PAGE (price ascending) ===" . PHP_EOL;
foreach ($catalog->getProducts($registry->get('sale_page')) as $p) {
    echo "- {$p['name']} : price = $" . number_format($p['price'], 2) . PHP_EOL;
}

echo PHP_EOL . "=== NEW ARRIVALS (created date descending) ===" . PHP_EOL;
foreach ($catalog->getProducts($registry->get('new_arrivals')) as $p) {
    echo "- {$p['name']} : created = {$p['created']}" . PHP_EOL;
}