<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.pengaturan.index', compact('settings'));
    }

    public function update(UpdateSettingRequest $request)
    {
        $keys = [
            'nama_toko', 'alamat', 'no_hp', 'pajak', 'service_charge',
            'ongkir', 'jam_buka', 'jam_tutup',
            'wa_api_key', 'wa_sender', 'wa_api_url', 'wa_store_number',
        ];

        Setting::setValue('auto_stock_deduction', $request->boolean('auto_stock_deduction'));

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::setValue($key, $request->$key);
            }
        }

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::getValue('logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('logo')->store('settings', 'public');
            Setting::setValue('logo', $path);
        }

        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan berhasil disimpan');
    }
}
