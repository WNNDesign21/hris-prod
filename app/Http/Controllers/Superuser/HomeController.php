<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Superuser Home",
            'page' => 'superuser-index',
        ];
        return view('pages.superuser.index', $dataPage);
    }
}
