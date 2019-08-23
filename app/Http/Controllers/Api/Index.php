<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class Index extends Controller
{
    public function index(){
        return view('web')->file('index.html');
    }

}
