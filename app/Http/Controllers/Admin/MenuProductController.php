<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuProductController extends Controller
{
    public function index(): View
    {
        return view('admin.menu.index', [
            'products' => Product::query()->orderBy('category')->orderBy('name')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedProduct($request);
        $data['active'] = $request->boolean('active');

        Product::create($data);

        return redirect()->route('admin.menu.index')->with('success', 'Producto agregado a la carta.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validatedProduct($request);
        $data['active'] = $request->boolean('active');
        $product->update($data);

        return redirect()->route('admin.menu.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Producto eliminado de la carta.');
    }

    private function validatedProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'station' => ['required', Rule::in(['bar', 'kitchen'])],
            'active' => ['sometimes', 'boolean'],
        ]);
    }
}
