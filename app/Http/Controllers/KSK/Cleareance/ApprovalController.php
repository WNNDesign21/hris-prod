<?php

namespace App\Http\Controllers\KSK\Cleareance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $dataPage = [
            'pageTitle' => "KSK-E - Approval Cleareance",
            'page' => 'ksk-cleareance-approval',
        ];
        return view('pages.ksk-e.cleareance.approval.index', $dataPage);
    }
}
