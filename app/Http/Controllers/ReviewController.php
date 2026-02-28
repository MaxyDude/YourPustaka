<?php

namespace App\Http\Controllers;

use App\Models\Ulasan;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'book_id' => ['required', 'exists:books,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Check if user already reviewed this book
            $existingReview = Ulasan::where('user_id', Auth::id())
                ->where('buku_id', $request->book_id)
                ->first();

            if ($existingReview) {
                // Update existing review
                $existingReview->update([
                    'rating' => $request->rating,
                    'komentar' => $request->comment,
                ]);
                $review = $existingReview;
            } else {
                // Create new review
                $review = Ulasan::create([
                    'user_id' => Auth::id(),
                    'buku_id' => $request->book_id,
                    'rating' => $request->rating,
                    'komentar' => $request->comment,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil disimpan',
                'review' => [
                    'id' => $review->id,
                    'user_name' => Auth::user()->name,
                    'rating' => $review->rating,
                    'comment' => $review->komentar,
                    'created_at' => $review->created_at->format('d F Y'),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all reviews for a specific book.
     */
    public function getBookReviews($book_id)
    {
        try {
            // Verify book exists
            $book = Book::find($book_id);
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak ditemukan',
                ], 404);
            }

            // Get all reviews for the book
            $reviews = Ulasan::where('buku_id', $book_id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user_name' => $review->user->name ?? 'Anonim',
                        'rating' => $review->rating,
                        'comment' => $review->komentar,
                        'created_at' => $review->created_at->format('d F Y'),
                    ];
                });

            // Calculate average rating
            $averageRating = $reviews->isEmpty() ? 0 : $reviews->avg('rating');

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
                'average_rating' => round($averageRating, 1),
                'total_reviews' => $reviews->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if current user has reviewed this book.
     */
    public function hasUserReviewed($book_id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'has_reviewed' => false,
            ]);
        }

        $review = Ulasan::where('user_id', Auth::id())
            ->where('buku_id', $book_id)
            ->first();

        return response()->json([
            'success' => true,
            'has_reviewed' => $review ? true : false,
            'review' => $review ? [
                'rating' => $review->rating,
                'comment' => $review->komentar,
            ] : null,
        ]);
    }
}
