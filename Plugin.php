<?php

namespace Cleanse\Frontlines;

use DateTime;
use DB;
use Log;
use Queue;
use System\Classes\PluginBase;
use Cleanse\Pvpaissa\Classes\HelperDataCenters;
use Cleanse\Frontlines\Classes\FrontlinesHelper;
use Cleanse\Frontlines\Models\Overall;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name' => 'PvPaissa Frontlines',
            'description' => 'Add FFXIV Frontlines Rankings to your website.',
            'author' => 'Paul Lovato',
            'icon' => 'icon-shield'
        ];
    }

    public function registerComponents()
    {
        return [
            'Cleanse\Frontlines\Components\Rankings'    => 'cleanseFrontlinesRankings',
            'Cleanse\Frontlines\Components\Profile'     => 'cleanseFrontlinesProfile',
            'Cleanse\Frontlines\Components\Install'     => 'cleanseFrontlinesInstall'
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'yearweek' => [$this, 'makeDateFromYearWeek']
            ]
        ];
    }

    public function makeDateFromYearWeek($yearWeek)
    {
        $year = substr($yearWeek, 0, -2);
        $week = substr($yearWeek, 4);

        $date = new DateTime();

        $date->setISODate($year, $week);
        return $date->format('Y-m-d');
    }

//    public function registerSchedule($schedule)
//    {
//        $schedule->call(function () {
//            $when = new FrontlinesHelper('Balmung');
//            $week = $when->nextWeek();
//
//            $dataCenters = new HelperDataCenters;
//
//            foreach ($dataCenters->datacenters as $dc) {
//                foreach ($dc as $server) {
//                    $data = [
//                        'server' => $server,
//                        'week' => $week
//                    ];
//
//                    Queue::push('\Cleanse\Frontlines\Classes\Jobs\ScrapeFrontlines', $data);
//                }
//            }
//
//            Queue::push('\Cleanse\Frontlines\Classes\Jobs\RankFrontlinesWeekly', ['week' => $week]);
//        })->cron('16 4 * * 1');
//
//        $schedule->call(function () {
//            Log::info('Frontlines overall starting.');
//
//            $rank = 1;
//            Overall::orderBy('wins', 'desc')
//                ->chunk(100, function ($frontlinesPlayers) use (&$rank) {
//                    DB::connection()->disableQueryLog();
//                    foreach ($frontlinesPlayers as $player) {
//                        $player->rank = $rank;
//
//                        $player->save();
//
//                        $rank++;
//                    }
//                });
//
//            Log::info('Frontlines overall done.');
//        })->cron('50 4 * * 1');
//    }
}
