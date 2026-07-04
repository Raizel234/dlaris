<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'meja_id' => $this->meja_id,
            'meja' => new MejaResource($this->whenLoaded('meja')),
            'nomor_order' => $this->nomor_order,
            'status' => $this->status,
            'catatan' => $this->catatan,
            'total' => (float) $this->total,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'transaksi' => new TransaksiResource($this->whenLoaded('transaksi')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
