<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class Sto
{
    /**
     * ONLY nOTES
     * Query to get QTY Book on physical inventory
     * "SELECT SUM(QtyOnHand) FROM M_StorageOnHand WHERE M_Product_ID=? AND M_Locator_ID=? "
     * AND M_AttributeSetInstance_ID=?"; //3 << Kalau ada attribute set instance nanti
     */
    private static function login()
    {
        $url = "https://server.tricentrumfortuna.com:12/api/v1/auth/tokens";
        $username = "ict_system";
        $password = "ict@iDempiere2024";
        $clientId = "1000000";
        $roleId = "1000065";
        $organizationId = "1000002";

        $request = Http::withOptions([
            'verify' => false,
        ])->post($url, [
            "userName" => $username,
            "password" => $password,
            "parameters" => [
                "clientId" => $clientId,
                "roleId" => $roleId,
                "organizationId" => $organizationId
            ]
        ]);


        if ($request->successful()) {
            /**
             * store token to session
             */
            session(['token' => $request['token'], 'refresh_token' => $request['refresh_token']]);
        }
    }

    private static function refreshToken($refresh_token)
    {
        $url = "https://server.tricentrumfortuna.com:12/api/v1/auth/refresh";
        $request = Http::withOptions([
            'verify' => false,
        ])->post($url, [
            "refresh_token" => $refresh_token
        ]);

        if ($request->successful()) {
            /**
             * store token to session
             */
            session(['token' => $request['token'], 'refresh_token' => $request['refresh_token']]);
        }
    }

    private static function cekSessions()
    {


        if (!session('token')) {
            if (!session('refresh_token')) {
                self::login();
                $session = true;
            } else {
                $refresh_token = session('refresh_token');
                self::refreshToken($refresh_token);
                $session = true;
            }
        }
    }


    /**
     * public method
     */



    public static function logout()
    {
        $url = "https://server.tricentrumfortuna.com:12/api/v1/auth/logout";

        $token = session('token');
        $request = Http::withOptions([
            'verify' => false,
        ])->post($url, [
            "token" => $token
        ]);

        return $request->status();
    }

    public static function addSto()
    {
        self::cekSessions();

        $token = session('token');
        $url = "https://server.tricentrumfortuna.com:12/api/v1/models/M_Inventory";

        $request = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post($url, [
            "userName" => $username,
            "password" => $password,
            "parameters" => [
                "clientId" => $clientId,
                "roleId" => $roleId,
                "organizationId" => $organizationId
            ]
        ]);
    }



    /**
     * testing method
     */





    public static function getsSto()
    {
        /**
         * M_Inventory.C_DocType_ID IN (SELECT C_DocType_ID FROM C_DocType Where DocBaseType='MMI' AND (DocSubTypeInv='PI' OR DocSubTypeInv IS NULL))
         */

        self::cekSessions();

        $url = "https://server.tricentrumfortuna.com:12/api/v1/models/M_Inventory";

        $token = session('token');

        $request = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get($url);

        if ($request->status() == 200) {
            return $request->json();
        } else {
            self::login();
        }

        // if ($request->successful()) {
        //     return $request->json();
        // } else {
        //     self::login();
        // }

    }

    public static function testLogin()
    {
        self::login();

        $data = [
            'token' => session('token'),
            'refresh_token' => session('refresh_token')
        ];

        return ResponseFormat::success($data, 200);
    }

    public static function testingFlow()
    {
        return self::cekSessions();
    }
}
