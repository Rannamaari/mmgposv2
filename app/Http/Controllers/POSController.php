<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livewire\POSPage;

class POSController extends Controller
{
    public function index()
    {
        return view('pos.standalone');
    }
}
