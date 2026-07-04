<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        $backups = collect();
        $files = Storage::disk('local')->files('backups');
        foreach ($files as $file) {
            $backups->push([
                'filename' => basename($file),
                'size' => Storage::disk('local')->size($file),
                'last_modified' => Storage::disk('local')->lastModified($file),
            ]);
        }
        $backups = $backups->sortByDesc('last_modified');

        return view('admin.backup.index', compact('backups'));
    }

    public function create()
    {
        try {
            $filename = 'backup-' . now()->format('Ymd-His') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            if (!is_dir(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $sql = '';

            $tables = DB::select('SHOW TABLES');
            $dbName = env('DB_DATABASE');

            foreach ($tables as $table) {
                $tableName = $table->{"Tables_in_{$dbName}"};

                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "\n\n" . $createTable[0]->{'Create Table'} . ";\n\n";

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $columns = array_map(function ($col) {
                        return "`{$col}`";
                    }, array_keys((array) $row));
                    $values = array_map(function ($val) {
                        if (is_null($val)) return 'NULL';
                        return "'" . str_replace("'", "\\'", $val) . "'";
                    }, array_values((array) $row));

                    $sql .= "INSERT INTO `{$tableName}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
            }

            file_put_contents($path, $sql);

            return redirect()->route('admin.backup.index')
                ->with('success', "Backup berhasil: {$filename}");
        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $filename = basename($filename);
        $path = storage_path('app/backups/' . $filename);
        if (!file_exists($path)) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'File backup tidak ditemukan');
        }
        return response()->download($path);
    }

    public function destroy($filename)
    {
        $filename = basename($filename);
        $path = 'backups/' . $filename;
        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
        return redirect()->route('admin.backup.index')
            ->with('success', 'Backup berhasil dihapus');
    }
}
