<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreUserPost;
use App\User;
use Elasticsearch\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class UserController extends Controller
{

    public function get(Request $request)
    {
        return $request->user();
    }
    public function update(StoreUserPost $req)
    {
        $user = $req->user();
        $user->fill($req->all());
        $user->save();
        return $user;
    }

    public function dashboard()
    {
        /** @var Client $client */
        $client = resolve('ElasticSearchClient');
        $res = $client->search([
            'index' => 'weight',
            'body' => [
                'size' => 100,
                'sort' => [
                    'date' => 'asc'
                ]
            ]
        ]);
        $data = collect($res['hits']['hits'])->map(function($row){
            return $row['_source'];
        })->groupBy('id')->map(function($items, $id){
            $d = collect($items);
            $weights = $d->pluck('weight');
            $dates = $d->pluck('date');
            $user = User::find($id);
            return [
                'label' => $user->name,
                'data'  => $weights,
                'dates' => $dates
            ];
        })->values();
        if($data->isNotEmpty()){
            $dates = $data->first()['dates'];
        }else{
            $dates = [];
        }
        return [
            'd' => $data,
            'date' => $dates
        ];
    }
}
