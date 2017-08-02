<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreUserPost;
use App\User;
use Carbon\Carbon;
use Elasticsearch\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

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

    public function dashboard(Request $req)
    {
        /** @var User $user */
        $user = $req->user();
        /** @var Client $client */
        $client = resolve('ElasticSearchClient');
        $goalDate = Carbon::parse($user->goal_date);

        $res = $client->search([
            'index' => 'weight',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'range' => [
                                    'date' => [
                                        'gte' => 'now-2m/d',
                                        'lt'  => 'now+'.$goalDate->diffInDays(Carbon::now(), true).'d/d'
                                    ]
                                ]
                            ],
                            [
                                'term' => [
                                    'id' => $user->id
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    'date' => 'asc'
                ],
                'aggs' => [
                    'date'=> [
                        'date_histogram' => [
                            'field' => 'date',
                            'interval' => 'day'
                        ],
                        'aggs' => [
                            'weight' => [
                                'avg' => ['field' => 'weight']
                            ],
                            'obj' => [
                                'top_hits' => [
                                    '_source' => [
                                        'include' => ['weight', 'date', 'id']
                                    ],
                                    'size' => 1
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        //dd($res);
        $dataList = collect($res['aggregations']['date']['buckets'])->map(function($row){
            return [
                'weight' => $row['weight']['value'],
                'date' => $row['obj']['hits']['hits'][0]['_source']['date'],
                'id' => $row['obj']['hits']['hits'][0]['_source']['id'],
            ];
        })->groupBy('id')->map(function($items, $id){
            //$d = collect($items);
            $weights = $items->pluck('weight');
            $dates = $items->pluck('date');
            $user = User::find($id);

            return [
                'label' => $user->name,
                'data'  => $dates->combine($weights)
            ];
        });


        $startDate = strtotime('-2month');

        $diffDate = $goalDate->diffInDays(Carbon::createFromTimestamp($startDate), true);

        $dateList = Collection::times(($diffDate), function($i)use($startDate){
            return date('Y-m-d', strtotime("+ $i day", $startDate));
        });
        //dd($dateList);
        //dd($dataList);

        $graphList = $dataList->map(function($item, $key) use($dateList){
            /** @var Collection $data */
            $data  = $item['data'];
            return [
                'label'=> $item['label'],
                'data' => $dateList->map(function($colDate) use($data){
                    return $data->get($colDate);
                })
            ];
        });
        /** @var Collection $plotData */
        $plotData = clone $graphList->first()['data'];
        $plotData->pop();
        $plotData->push(65);

        $graphList = collect($graphList)->merge([
            [
                'label' => '目標',
                'data' => $plotData
            ]
        ]);

        return [
            'd' => $graphList->values(),
            'date' => $dateList->values(),
            'plain' => $dataList->values()
        ];
    }

    public function addWeight(Request $req)
    {
        $user = $req->user();
        $weight = $req->get('weight');
        if($weight > 0){
            $dateFormat = date('Y-m-d', strtotime("now"));
            /** @var Client $client */
            $client = resolve('ElasticSearchClient');
            $client->index([
                'index' => 'weight',
                'type'  => "u_".$user->id,
                'id'    => $dateFormat,
                'body'  => [
                    'id'     => $user->id,
                    'weight' => $weight,
                    'date'   => $dateFormat
                ]
            ]);
            sleep(1.5);
            return ['ok' => 1];
        }
    }
}
