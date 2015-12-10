<?php

namespace App\Http\Controllers;
use Session;

class InnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::put('name', auth()->user()->name);
        return view('inner');
    }
}
