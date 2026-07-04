<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RuanganResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'kapasitas' => $this->kapasitas,
            'tarif_per_jam' => (float) $this->tarif_per_jam,
            'fasilitas' => $this->fasilitas,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'status' => $this->status,
            'bookings_aktif' => BookingResource::collection($this->whenLoaded('bookingsAktif')),
        ];
    }
}
