<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Book;

echo "Total books: " . Book::count() . "\n";
echo "\n";

$books = Book::limit(5)->get();
foreach ($books as $book) {
    echo "- {$book->title} (available: {$book->available_copies})\n";
}

// Try to update first book to have stock
if ($books->count() > 0) {
    echo "\nUpdating first book to have 5 available copies...\n";
    $book = $books->first();
    $book->available_copies = 5;
    $book->save();
    echo "Updated: {$book->title} now has {$book->available_copies} available copies\n";
}
