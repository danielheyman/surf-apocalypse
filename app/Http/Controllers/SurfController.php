<?php

namespace App\Http\Controllers;
use Session;
use Auth;

class SurfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::put('name', Auth::user()->name);
        return view('surf');
    }
}
