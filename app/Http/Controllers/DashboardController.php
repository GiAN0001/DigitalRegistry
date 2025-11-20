<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\View\View; // Be sure to import View

class DashboardController extends Controller
{
    public function index() : View
    {
        return view('dashboard');
    }
}