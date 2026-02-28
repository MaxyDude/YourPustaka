<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $loans = Loan::with('book')
            ->where('user_email', $user->email)
            ->latest()
            ->get();

        $totalBooks = $loans->count();
        $borrowedCount = $loans->whereIn('status', ['pending', 'approved', 'active'])->count();
        $completedCount = $loans->where('status', 'returned')->count();

        $favorites = collect();
        if (Schema::hasTable('koleksipribadi_tabel')) {
            $favoriteBookIds = DB::table('koleksipribadi_tabel')
                ->where('user_id', $user->id)
                ->latest('created_at')
                ->limit(6)
                ->pluck('buku_id')
                ->filter()
                ->unique()
                ->values();

            if ($favoriteBookIds->isNotEmpty()) {
                $favorites = Book::query()
                    ->withAvg('ulasan', 'rating')
                    ->whereIn('id', $favoriteBookIds)
                    ->get()
                    ->sortBy(function (Book $book) use ($favoriteBookIds) {
                        return $favoriteBookIds->search($book->id);
                    })
                    ->values();
            }
        }

        // Fallback jika koleksi pribadi kosong: ambil buku dari histori pinjaman terbaru.
        if ($favorites->isEmpty()) {
            $favoriteBookIds = $loans->pluck('book_id')
                ->filter()
                ->unique()
                ->take(6)
                ->values();

            if ($favoriteBookIds->isNotEmpty()) {
                $favorites = Book::query()
                    ->withAvg('ulasan', 'rating')
                    ->whereIn('id', $favoriteBookIds)
                    ->get()
                    ->values();
            }
        }

        return view('profile.profile', compact(
            'user',
            'loans',
            'favorites',
            'totalBooks',
            'borrowedCount',
            'completedCount'
        ));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->no_handphone = $validated['phone'] ?? null;

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
