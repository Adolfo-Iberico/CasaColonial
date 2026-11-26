<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KitchenDisplayController extends Controller
{
    public function index(): View
    {
        return view('restaurant.kds.index');
    }
}
