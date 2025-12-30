<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Dinas;
use Illuminate\Routing\Controller;

class LandingPageController extends Controller
{
    public function index()
    {
        $totalAset = Barang::count();
        $totalDinas = Dinas::count();

        return view('welcome', compact('totalAset', 'totalDinas'));
    }
}
