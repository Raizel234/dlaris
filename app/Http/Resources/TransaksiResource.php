<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'booking_id' => $this->booking_id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'kode_transaksi' => $this->kode_transaksi,
            'metode_bayar' => $this->metode_bayar,
            'total' => (float) $this->total,
            'nominal_bayar' => (float) $this->nominal_bayar,
            'kembalian' => (float) $this->kembalian,
            'status' => $this->status,
            'order' => new OrderResource($this->whenLoaded('order')),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'created_at' => $this->created_at,
        ];
    }
}
