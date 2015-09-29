<?php

namespace App\Http\Controllers;
use Session;

class SurfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::put('name', auth()->user()->name);
        return view('surf');
    }
}
