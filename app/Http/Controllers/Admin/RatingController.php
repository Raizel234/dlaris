<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRatingRequest;
use App\Models\Menu;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request)
    {
        $rating = Rating::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'menu_id' => $request->menu_id,
            ],
            [
                'rating' => $request->rating,
                'komentar' => $request->komentar,
                'transaksi_id' => $request->transaksi_id,
            ]
        );

        $this->updateMenuRating($request->menu_id);

        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil dikirim',
            'data' => $rating->load('user'),
        ]);
    }

    public function byMenu($menuId)
    {
        $ratings = Rating::with('user')
            ->where('menu_id', $menuId)
            ->latest()
            ->get();

        $menu = Menu::findOrFail($menuId);

        return response()->json([
            'success' => true,
            'data' => [
                'rata_rating' => $menu->rata_rating,
                'total_ulasan' => $menu->total_ulasan,
                'ratings' => $ratings,
            ],
        ]);
    }

    public function updateMenuRating($menuId)
    {
        $avg = Rating::where('menu_id', $menuId)->avg('rating');
        $count = Rating::where('menu_id', $menuId)->count();

        Menu::where('id', $menuId)->update([
            'rata_rating' => round($avg ?? 0, 2),
            'total_ulasan' => $count,
        ]);
    }
}
