<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;

echo "=== Loan Creation Test ===\n\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "ERROR: No user found in database\n";
    exit(1);
}

echo "User: {$user->name} ({$user->email})\n\n";

// Get first book
$book = Book::where('available_copies', '>', 0)->first();
if (!$book) {
    echo "ERROR: No available book found\n";
    exit(1);
}

echo "Book: {$book->title}\n";
echo "Available copies: {$book->available_copies}\n\n";

// Simulate form data
$formData = [
    'user_name' => $user->name,
    'user_email' => $user->email,
    'user_phone' => '081234567890',
    'book_id' => $book->id,
    'loan_date' => now(),
    'due_date' => now()->addDays(14),
    'barcode_code' => Loan::generateBarcodeCode(),
    'status' => 'pending',
];

echo "Creating loan with data:\n";
foreach ($formData as $key => $value) {
    echo "  $key: " . (is_object($value) ? $value->toDateTimeString() : $value) . "\n";
}

echo "\n";

try {
    $loan = Loan::create($formData);
    echo "✓ Loan created successfully!\n";
    echo "  Loan ID: {$loan->id}\n";
    echo "  Barcode: {$loan->barcode_code}\n";
    echo "  Status: {$loan->status}\n";

    // Verify loan can be retrieved
    $retrieved = Loan::find($loan->id);
    echo "\n✓ Loan retrieved successfully\n";
    echo "  User Name: {$retrieved->user_name}\n";
    echo "  User Email: {$retrieved->user_email}\n";
    echo "  User Phone: {$retrieved->user_phone}\n";

} catch (\Exception $e) {
    echo "✗ ERROR creating loan:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
