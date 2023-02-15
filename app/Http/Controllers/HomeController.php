<?php

namespace App\Http\Controllers;

use App\Models\ActiveProductListing;
use App\Models\FileCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $data = [];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->data['fileCategories'] = FileCategory::get()->toArray();

        return view('home', $this->data);
    }
}
