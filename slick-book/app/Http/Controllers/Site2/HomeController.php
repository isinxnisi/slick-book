<?php
namespace App\Http\Controllers\Site2;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Home
     */
    public function index()
    {
        return view('site2.home');
    }

}
