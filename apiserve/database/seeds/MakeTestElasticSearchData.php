<?php

use App\User;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class MakeTestElasticSearchData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();

        $user = User::find(1);

        Collection::times(100, function($number) use($client, $user){
            $s = 60.0;
            $dateFormat = date('Y-m-d', strtotime("+ ${number} day"));
            $s += ($number/10);

            $client->index([
                'index' => 'weight',
                'type'  => "u_".$user->id,
                'body'  => [
                    'id'     => $user->id,
                    'weight' => $s,
                    'date'   => $dateFormat
                ]
            ]);
        });
    }
}
