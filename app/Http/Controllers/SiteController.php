<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Place;

class SiteController extends Controller
{
    /**
     * Show the application front page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $places = Place::all();
        return view('index', compact('places'));
    }
}
