<?php

namespace App\Http\Controllers\Utils;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DeleteQrImgController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try{
            $file_path = $request->file_path;
            if($file_path){
                Storage::delete($file_path);
            }
            return response()->json(['message' => 'QR Code IMG Deleted!'],200);
        } catch(Throwable $error){
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }
}
