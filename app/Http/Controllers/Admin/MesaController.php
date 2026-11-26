<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MesaController extends Controller
{
    public function index()
    {
        return view('admin.mesas.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nro_mesas' => 'required|integer|unique:tables',
            'capacidad' => 'required|integer|min:1',
            'disponible' => 'required|in:0,1',
        ]);

        try {
            $validator->validate();

            Table::create([
                'nro_mesas' => $request->nro_mesas,
                'capacidad' => $request->capacidad,
                'disponible' => $request->disponible,
            ]);

            return redirect()->route('admin.mesas.index')->with('success', 'Mesa creada exitosamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        $canManageTables = $request->user()->hasPermission('tables.manage');
        $validator = Validator::make($request->all(), [
            'nro_mesas' => $canManageTables ? 'required|integer|unique:tables,nro_mesas,' . $id : 'sometimes|prohibited',
            'capacidad' => $canManageTables ? 'required|integer|min:1' : 'sometimes|prohibited',
            'disponible' => 'required|in:0,1',
        ]);

        try {
            $validator->validate();

            $table = Table::findOrFail($id);
            $changes = ['disponible' => $request->disponible];

            if ($canManageTables) {
                $changes['nro_mesas'] = $request->nro_mesas;
                $changes['capacidad'] = $request->capacidad;
            }

            $table->update($changes);

            return redirect()->route('admin.mesas.index')->with('success', 'Mesa actualizada exitosamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function destroy(string $id)
    {
        $table = Table::findOrFail($id);

        if ($table->orders()->exists()) {
            return back()->withErrors([
                'table' => 'No se puede eliminar una mesa que tiene órdenes registradas.',
            ]);
        }

        $table->delete();

        return redirect()->route('admin.mesas.index')->with('success', 'Mesa eliminada exitosamente.');
    }
}
