<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.customers.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'required|email|unique:customers,email',
            'address' => 'nullable|string|max:500',
            'date_registered' => 'nullable|date',
        ]);

        try {
            $validator->validate();

            $customer = Customer::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'date_registered' => $request->date_registered,
            ]);

            return redirect()->route('admin.customers.index')->with('success', 'El cliente fue creado con exito.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'required|email|unique:customers,email,' . $id,
            'address' => 'nullable|string|max:500',
            'date_registered' => 'nullable|date',
        ]);

        try {
            $validator->validate();

            $customer = Customer::findOrFail($id);
            $customer->update([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'date_registered' => $request->date_registered,
            ]);

            return redirect()->route('admin.customers.index')->with('success', 'El cliente fue actualizado con exito.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->orders()->exists()) {
            return back()->withErrors([
                'customer' => 'No se puede eliminar un cliente con órdenes registradas.',
            ]);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'El cliente fue eliminado con exito.');
    }
    public function exportPdf()
{
    $customers = Customer::where('status', true)->get();
    $pdf = Pdf::loadView('Admin.cliente.pdf', compact('customers'));
    return $pdf->download('reporte_clientes.pdf');
}
}
