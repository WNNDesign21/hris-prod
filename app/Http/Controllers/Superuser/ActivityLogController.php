<?php

namespace App\Http\Controllers\Superuser;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityLogController extends Controller
{
    public function index()
    {
        $dataPage = [
            'pageTitle' => "Superuser - Activity Log",
            'page' => 'superuser-activity-log',
        ];
        return view('pages.superuser.activity-log.index', $dataPage);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'activity_log.log_name',
            1 => 'activity_log.description',
            2 => 'users.username',
            3 => 'activity_log.created_at',
        );

        $totalData = ActivityLog::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $settings['start'] = $start;
        $settings['limit'] = $limit;
        $settings['dir'] = $dir;
        $settings['order'] = $order;

        $dataFilter = [];

        $createdAt = $request->createdAt;
        if (!empty($createdAt)) {
            $createdAt = explode('|', $createdAt);
            $start = $createdAt[0];
            $end = $createdAt[1];

            $dataFilter['start'] = $start;
            $dataFilter['end'] = $end;
        }

        $causer = $request->causer;
        if (!empty($causer)) {
            $dataFilter['causer_id'] = $causer;
        }

        $search = $request->input('search.value');
        if (!empty($search)) {
            $dataFilter['search'] = $search;
        }

        $uploadLog = ActivityLog::getData($dataFilter, $settings);
        $totalFiltered = ActivityLog::countData($dataFilter);

        $dataTable = [];

        if (!empty($uploadLog)) {
            foreach ($uploadLog as $data) {
                $nestedData['log_name'] = $data?->log_name;
                $nestedData['description'] = $data?->description;
                $nestedData['causer'] = $data?->username;
                $nestedData['created_at'] = Carbon::parse($data->created_at)->translatedFormat('d F Y H:i:s');
                $dataTable[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $dataTable,
            "order" => $order,
            "dir" => $dir,
            "column"=>$request->input('order.0.column')
        );

        return response()->json($json_data, 200);
    }

    public function causer(Request $request){
        $search = $request->input('search');
        $page = $request->input("page");
        $idCats = $request->input('catsProd');
        $adOrg = $request->input('adOrg');

        $query = User::select(
            'id',
            'username',
        );

        $organisasi_id = auth()->user()->organisasi_id;
        if($organisasi_id){
            $query->organisasi($organisasi_id);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('username', 'ILIKE', "%{$search}%");
            });
        }

        $data = $query->simplePaginate(10);

        $morePages = true;
        $pagination_obj = json_encode($data);
        if (empty($data->nextPageUrl())) {
            $morePages = false;
        }

        foreach ($data->items() as $user) {
            $dataUser[] = [
                'id' => $user->id,
                'text' => $user->username
            ];
        }

        $results = array(
            "results" => $dataUser,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
