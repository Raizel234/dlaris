<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'ruangan_id' => $this->ruangan_id,
            'ruangan' => new RuanganResource($this->whenLoaded('ruangan')),
            'nama_pemesan' => $this->nama_pemesan,
            'nomor_hp' => $this->nomor_hp,
            'tanggal' => $this->tanggal,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'durasi' => $this->durasi,
            'total_harga' => (float) $this->total_harga,
            'status' => $this->status,
            'catatan' => $this->catatan,
            'transaksi' => new TransaksiResource($this->whenLoaded('transaksi')),
            'created_at' => $this->created_at,
        ];
    }
}
