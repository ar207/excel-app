<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ODBCController extends Controller
{
    private $data = [];
    private $message = '';
    private $success = false;

    /**
     * Used to return index of specific resource
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('odbc.index');
    }
}
