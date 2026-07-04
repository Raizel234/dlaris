<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'menu_id' => $this->menu_id,
            'menu' => new MenuResource($this->whenLoaded('menu')),
            'jumlah' => $this->jumlah,
            'harga' => (float) $this->harga,
            'catatan' => $this->catatan,
            'subtotal' => (float) ($this->jumlah * $this->harga),
        ];
    }
}
