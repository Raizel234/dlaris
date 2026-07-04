<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kategori_id' => $this->kategori_id,
            'kategori' => new KategoriResource($this->whenLoaded('kategori')),
            'nama' => $this->nama,
            'slug' => $this->slug,
            'deskripsi' => $this->deskripsi,
            'harga' => (float) $this->harga,
            'stok' => $this->stok,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'is_tersedia' => $this->is_tersedia,
            'is_best_seller' => $this->is_best_seller,
            'is_new' => $this->is_new,
            'created_at' => $this->created_at,
        ];
    }
}
