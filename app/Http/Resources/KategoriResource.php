<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'slug' => $this->slug,
            'deskripsi' => $this->deskripsi,
            'ikon' => $this->ikon,
            'gambar' => $this->gambar ? asset('storage/' . $this->gambar) : null,
            'is_active' => $this->is_active,
            'menus' => MenuResource::collection($this->whenLoaded('menus')),
            'menus_aktif' => MenuResource::collection($this->whenLoaded('menusAktif')),
            'created_at' => $this->created_at,
        ];
    }
}
