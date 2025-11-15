<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\View\View; // Be sure to import View

class DashboardController extends Controller
{
    public function index() : View
    {
        // This just loads the 'dashboard.blade.php' file
        return view('dashboard');
    }
}