<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MejaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_meja' => $this->nomor_meja,
            'kapasitas' => $this->kapasitas,
            'area' => $this->area,
            'status' => $this->status,
            'qr_code' => $this->qr_code ? asset('storage/' . $this->qr_code) : null,
            'qr_url' => $this->qr_code ? asset('storage/' . $this->qr_code) : null,
        ];
    }
}
