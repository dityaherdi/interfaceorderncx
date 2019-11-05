<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Test;

class TestController extends Controller
{
    public function testConnection()
    {
        $test = Test::where('ACCOUNT_NUM', '018769')->get();
        return view('index', compact('test'));
    }
}
