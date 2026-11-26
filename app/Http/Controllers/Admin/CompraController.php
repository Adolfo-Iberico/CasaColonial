<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buy;
use App\Models\BuyDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.compra.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'total' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        try {
            $validator->validate();

            $buy = Buy::create([
                'user_id' => $request->user_id,
                'supplier_id' => $request->supplier_id, // ✅ Corregido: sin espacio extra
                'total' => $request->total,
                'date' => $request->date,
            ]);

            return redirect()->route('admin.compra.index')
                ->with('success', 'La compra fue creada con éxito');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'total' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        try {
            $validator->validate();

            $buy = Buy::findOrFail($id);
            $buy->update([
                'user_id' => $request->user_id,
                'supplier_id' => $request->supplier_id,
                'total' => $request->total,
                'date' => $request->date,
            ]);

            return redirect()->route('admin.compra.index')
                ->with('success', 'La compra fue actualizada con éxito');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $buy = Buy::findOrFail($id);
        DB::transaction(function () use ($buy): void {
            BuyDetails::where('buy_id', $buy->id)->delete();
            $buy->delete();
        });

        return redirect()->route('admin.compra.index')
            ->with('success', 'La compra fue eliminada con éxito');
    }
    // Métodos para exportar a PDF y Excel
    public function exportPdf()
    {
        $buys = Buy::where('status', true)->get();
        $pdf = Pdf::loadView('admin.compra.pdf', compact('buys'));
        return $pdf->download('reporte_compras.pdf');
    }

}