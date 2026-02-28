<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Book;
use App\Models\Loan;

echo "=== Database Test ===\n\n";

// Check users
$users = User::count();
echo "Total users: $users\n";
if ($users > 0) {
    $user = User::first();
    echo "First user: {$user->name} ({$user->email})\n";
}

echo "\n";

// Check books
$books = Book::count();
echo "Total books: $books\n";
if ($books > 0) {
    $book = Book::first();
    echo "First book: {$book->title}\n";
    echo "Available copies: {$book->available_copies}\n";
}

echo "\n";

// Check loans
$loans = Loan::count();
echo "Total loans: $loans\n";

// Check table structure
echo "\n=== Loan Table Columns ===\n";
$columns = \DB::select("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'peminjamanbuku' AND TABLE_SCHEMA = 'yourpustaka'");
foreach ($columns as $col) {
    echo "{$col->COLUMN_NAME}: {$col->DATA_TYPE}\n";
}

echo "\n=== Test Complete ===\n";
