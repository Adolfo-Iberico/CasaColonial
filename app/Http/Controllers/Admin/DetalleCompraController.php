<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BuyDetails;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DetalleCompraController extends Controller
{
    public function index()
    {
        return view('admin.detalle.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'buy_id' => 'required|exists:buys,id', // Debe ser un ID de compra válido 📚
            'name' => 'required|string|max:255', // Nombre del producto es obligatorio 🏭
            'cantidad' => 'required|numeric', // Cantidad es obligatoria ✍️
            'precio_unitario' => 'nullable|numeric', // Precio unitario es opcional 📝
            'subtotal' => 'required|numeric', // Subtotal es obligatorio 📊
        ]);

        try {
            $validator->validate();

            $buyDetails = BuyDetails::create([
                'buy_id' => $request->buy_id,
                'name'=> $request->name,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'subtotal' => $request->subtotal,
            ]);

            return redirect()->route('admin.detalle.index')
                ->with('success', 'El detalle de la compra  fue registrado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }
   
    public function update(Request $request, string $id)
{
    $validator = Validator::make($request->all(), [
        'buy_id' => 'required|exists:buys,id',
        'name' => 'required|string|max:255',
        'cantidad' => 'required|numeric',
        'precio_unitario' => 'nullable|numeric',
        'subtotal' => 'required|numeric',
    ]);

    try {
        $validator->validate();

        $buyDetails = BuyDetails::findOrFail($id);
        $buyDetails->update([
            'buy_id' => $request->buy_id,
            'name' => $request->name,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'subtotal' => $request->subtotal,
        ]);

        return redirect()->route('admin.detalle.index')
            ->with('success', 'El detalle de la compra fue actualizado correctamente.');
    } catch (ValidationException $e) {
        return back()->withErrors($e->validator->errors())->withInput();
    }
}


    public function destroy(string $id)
    {
        $buyDetails = BuyDetails::findOrFail($id);
        $buyDetails->delete();

        return redirect()->route('admin.detalle.index')
            ->with('success', 'El detalle de la compra fue eliminado correctamente.');
    }
    public function exportPdf()
    {
        $buyDetails = BuyDetails::where('status', true)->get();
        $pdf = Pdf::loadView('admin.compra.pdf', compact('buyDetails'));
        return $pdf->download('reporte_compras.pdf');
    }
}
