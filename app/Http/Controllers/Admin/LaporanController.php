<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftar;
use App\Exports\RekapExport;
use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\PencariKerja;
use App\Services\StatistikService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanController extends Controller
{
    public function index(Request $request, StatistikService $statistik): View
    {
        $user = $request->user();

        return view('admin.laporan.index', [
            'rekap' => $statistik->rekapKecamatan($user),
            'perStatus' => $statistik->perStatus(PencariKerja::visibleTo($user)),
            'ringkasan' => $statistik->ringkasanAdmin(),
            'kecamatan' => Kecamatan::orderBy('nama')->pluck('nama', 'id'),
        ]);
    }

    public function excel(Request $request): BinaryFileResponse
    {
        $filter = $this->filter($request);

        return Excel::download(new RekapExport($request->user(), $filter), 'rekap-poliran-'.now()->format('Ymd-His').'.xlsx');
    }

    public function pdf(Request $request, StatistikService $statistik): Response
    {
        $user = $request->user();

        $pdf = Pdf::loadView('pdf.rekap', [
            'rekap' => $statistik->rekapKecamatan($user),
            'perStatus' => $statistik->perStatus(PencariKerja::visibleTo($user)),
            'ringkasan' => $statistik->ringkasanAdmin(),
            'dicetakOleh' => $user->name,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('rekap-poliran-'.now()->format('Ymd-His').'.pdf');
    }

    /** @return array{status: string|null, kecamatan_id: int|null} */
    private function filter(Request $request): array
    {
        $data = $request->validate([
            'status' => ['nullable', Rule::enum(StatusPendaftar::class)],
            'kecamatan_id' => ['nullable', 'integer', 'exists:kecamatan,id'],
        ]);

        return ['status' => $data['status'] ?? null, 'kecamatan_id' => isset($data['kecamatan_id']) ? (int) $data['kecamatan_id'] : null];
    }
}
