<?php
namespace App\Http\Controllers\Site1;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Home
     */
    public function index()
    {
        return view('site1.home');
    }

}
