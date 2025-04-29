<?php

namespace App\Http\Controllers\Superuser;

use Storage;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Superuser - Settings",
            'page' => 'superuser-setting',
        ];
        return view('pages.superuser.setting.index', $dataPage);
    }

    public function upload_logo(Request $request)
    {
        $request->validate([
            'app_logo' => 'nullable|image|mimes:jpg|max:2048',
        ]);

        try {
            if ($request->hasFile('app_logo')) {
                $file = $request->file('app_logo');
                $fileName = 'app_logo.'.$file->getClientOriginalExtension();
                $filePath = "system/setting/$fileName";

                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }

                $file->storeAs("system/setting", $fileName);

                return response()->json([
                    'status' => true,
                    'message' => 'Logo uploaded successfully',
                    'filename' => $fileName,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No file uploaded',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while uploading the logo: ' . $e->getMessage(),
            ]);
        }
    }

    public function reset_logo()
    {
        try {
            $filePath = "system/setting/app_logo.jpg";

            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
                return response()->json([
                    'status' => true,
                    'message' => 'Logo reset successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Logo not found',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while resetting the logo: ' . $e->getMessage(),
            ]);
        }
    }

    public function upload_icon(Request $request)
    {
        $request->validate([
            'app_icon' => 'required|file|max:2048|extensions:ico',
        ]);

        try {
            if ($request->hasFile('app_icon')) {
                $file = $request->file('app_icon');
                $fileName = 'favicon.'.$file->getClientOriginalExtension();
                $filePath = "system/setting/$fileName";

                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }

                $file->storeAs("system/setting", $fileName);

                return response()->json([
                    'status' => true,
                    'message' => 'Web Icon uploaded successfully',
                    'filename' => $fileName,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No file uploaded',
                ], 422);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while uploading the logo: ' . $e->getMessage(),
            ], 422);
        }
    }
}
