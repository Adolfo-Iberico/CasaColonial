<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SuppliersExport;
use App\Http\Controllers\Controller;
use App\Models\Buy;
use App\Models\BuyDetails;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ProveedorController extends Controller
{
    public function index()
    {
        return view('admin.proveedor.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruc' => 'required|digits:11|unique:suppliers,ruc',
            'razonsocial' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|digits:9',
            'email' => 'required|email|max:255',
        ]);

        try {
            $validator->validate();

            $supplier = Supplier::create([
                'ruc' => $request->ruc,
                'razonsocial' => $request->razonsocial,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            return redirect()->route('admin.proveedor.index')
                ->with('success', 'El proveedor fue registrado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'ruc' => 'required|digits:11|unique:suppliers,ruc,' . $id,
            'razonsocial' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|digits:9',
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $validator->validate();

            $supplier = Supplier::findOrFail($id);
            $supplier->update([
                'ruc' => $request->ruc,
                'razonsocial' => $request->razonsocial,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            return redirect()->route('admin.proveedor.index')
                ->with('success', 'El proveedor fue actualizado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        DB::transaction(function () use ($supplier): void {
            $buyIds = Buy::where('supplier_id', $supplier->id)->pluck('id');
            BuyDetails::whereIn('buy_id', $buyIds)->delete();
            $supplier->delete();
        });

        return redirect()->route('admin.proveedor.index')
            ->with('success', 'El proveedor fue eliminado correctamente.');
    }

    public function exportPdf()
    {
        $suppliers = Supplier::where('status', true)->get();
        $pdf = Pdf::loadView('admin.proveedor.pdf', compact('suppliers'));
        return $pdf->download('reporte_proveedor.pdf');
    }
}