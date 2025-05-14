<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory\NoSeri;
use App\Models\Inventory\Perawatan;
use Carbon\Carbon;

class GenerateNextYearMaintenance extends Command
{
    protected $signature = 'maintenance:generate-next-year';
    protected $description = 'Generate perawatan otomatis untuk tahun berikutnya hanya untuk alat yang membutuhkan perawatan.';

    public function handle()
    {
        $nextYear = now()->addYear()->year;
        $serials = NoSeri::with('tools')->get(); // Ambil semua NoSeri dengan relasi Tools

        $countCreated = 0;

        foreach ($serials as $seri) {
            $tools = $seri->tools;

            // ✅ Skip jika alat tidak memenuhi syarat perawatan
            if (
                !$tools || 
                !$tools->jadwal_perawatan || $tools->jadwal_perawatan <= 0 ||
                !$tools->waktu_perawatan || $tools->waktu_perawatan <= 0 ||
                $tools->jumlah_orang_perawatan <= 0
            ) {
                continue;
            }

            $intervalMonths = (int) $tools->jadwal_perawatan;
            $durasiPerNoseri = (float) $tools->waktu_perawatan; // dalam menit
            $jumlahOrang = (int) max($tools->jumlah_orang_perawatan, 1);
            $durasiEffisien = ceil($durasiPerNoseri / $jumlahOrang);
            $durasiInSeconds = $durasiEffisien * 60;

            // Cari tanggal perawatan terakhir di tahun sekarang
            $last = Perawatan::where('no_seri_id', $seri->id)
                            ->whereYear('tgl_perawatan', now()->year)
                            ->orderByDesc('tgl_perawatan')
                            ->first();

            if (!$last) {
                $start = Carbon::create($nextYear, 1, 1, 8, 0);
            } else {
                $start = Carbon::parse($last->tgl_perawatan)->addMonths($intervalMonths);
            }

            // Buat jadwal perawatan hingga akhir tahun depan
            while ($start->year === $nextYear) {
                Perawatan::create([
                    'no_seri_id'      => $seri->id,
                    'users_id'        => $tools->users_id,
                    'no_perawatan'    => 'JP' . str_pad($seri->id, 8, '0', STR_PAD_LEFT),
                    'tgl_perawatan'   => $start,
                    'waktu_perawatan' => gmdate('H:i:s', $durasiInSeconds),
                ]);

                $countCreated++;
                $start->addMonths($intervalMonths);
            }
        }

        $this->info("Selesai. Jumlah perawatan yang dibuat untuk tahun {$nextYear}: {$countCreated}");
    }
}
