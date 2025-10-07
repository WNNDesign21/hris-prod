<?php

namespace App\Http\Controllers\Utils;

use Storage;
use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $dataValidate = [
            'id' => ['required', 'string', 'max:255'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['message' => $errors], 402);
        }

        try {
            $id = $request->id;
            $encrypted_id = Crypt::encryptString(gzcompress($id));
            $qrcode = QrCode::format('png')->size(400)->generate($encrypted_id);
            $fileName = 'QR-'.date('YmdHis').'.png';
            Storage::put("attachment/qrcode_generator/{$fileName}", $qrcode);
            $file_path = asset('storage/attachment/qrcode_generator/'.$fileName);
       
            return response()->json(['message' => 'Generated QR Success!', 'data' => $file_path], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
