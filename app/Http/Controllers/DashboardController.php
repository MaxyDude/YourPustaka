<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Recommended: 10 buku terbaru dari database
        $recommended_books = Book::withAvg('ulasan', 'rating')
            ->latest()
            ->take(10)
            ->get();

        // Popular: buku dengan rating terbaik dari database
        $popular_books = Book::with('ulasan')
            ->withAvg('ulasan', 'rating')
            ->orderByDesc('ulasan_avg_rating')
            ->take(10)
            ->get();

        // Kategori filter diambil langsung dari data buku di database
        $filter_categories = Book::query()
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $data = [
            'total_books' => Book::count(),
            'total_users' => User::count(),
            'total_loans' => Loan::count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'overdue_loans' => Loan::where('status', 'overdue')->count(),
            'pending_loans' => Loan::where('status', 'pending')->count(),
            'user' => $user,
            'recommended_books' => $recommended_books,
            'popular_books' => $popular_books,
            'filter_categories' => $filter_categories,
        ];

        return view('dashboard.dashboard', $data);
    }
}
