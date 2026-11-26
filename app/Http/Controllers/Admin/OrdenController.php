<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrdenController extends Controller
{
    public function index()
    {
        return view('admin.orden.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id', // Debe ser un ID de cliente válido 📚
            'table_id' => 'required|exists:tables,id', // Debe ser un ID de mesa válido 🏭
            'nombre_plato' => 'required|string|max:255', // Nombre del plato es obligatorio ✍️
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string|max:255', // Método de pago es obligatorio 💳
        ]);

        try {
            $validator->validate();
            $order = Order::create([
                'customer_id' => $request->customer_id,
                'table_id' => $request->table_id,
                'created_by' => $request->user()->id,
                'nombre_plato' => $request->nombre_plato,
                'total' => $request->total,
                'metodo_pago' => $request->metodo_pago,
                'status' => 'completed',
            ]);
            return redirect()->route('admin.orden.index')->with('success', 'Orden creada exitosamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        if ($order->payments()->exists()) {
            return back()->withErrors([
                'order' => 'No se puede editar una orden que ya tiene cobros registrados.',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id', // Debe ser un ID de cliente válido 📚
            'table_id' => 'required|exists:tables,id', // Debe ser un ID de mesa válido 🏭
            'nombre_plato' => 'required|string|max:255', // Nombre del plato es obligatorio ✍️
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string|max:255', // Método de pago es obligatorio 💳
        ]);

        try {
            $validator->validate();

            $order->update([
                'customer_id' => $request->customer_id,
                'table_id' => $request->table_id,
                'nombre_plato' => $request->nombre_plato,
                'total' => $request->total,
                'metodo_pago' => $request->metodo_pago,
            ]);

            return redirect()->route('admin.orden.index')
                ->with('success', 'La orden fue actualizada correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);

        if ($order->payments()->exists()) {
            return back()->withErrors([
                'order' => 'No se puede eliminar una orden que tiene cobros registrados.',
            ]);
        }

        $order->delete();

        return redirect()->route('admin.orden.index')
            ->with('success', 'La orden fue eliminada correctamente.');
    }
}
