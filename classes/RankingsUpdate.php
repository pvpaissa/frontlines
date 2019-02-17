<?php

namespace Cleanse\Frontlines\Classes;

use Log;
use Cleanse\PvPaissa\Classes\UpdateOrCreatePlayer;
use Cleanse\PvPaissa\Classes\HelperRankSort;
use Cleanse\Frontlines\Classes\FrontlinesCrawler;
use Cleanse\Frontlines\Models\Overall;
use Cleanse\Frontlines\Models\Weekly;

class RankingsUpdate
{
    public function updateWeek($data)
    {
        $list = new FrontlinesCrawler($data['server'], $data['week']);

        $players = $list->crawl();

        if (empty($players)) {
            Log::info($data['week'] . ': Week empty.');
            return;
        }

        foreach ($players as $player) {
            $fl = new UpdateOrCreatePlayer('frontlines', $player);
            $fl->update();
        }
    }

    public function weeklyPlayerSort($data)
    {
        $players = Weekly::where('week', $data['week'])->get(['id', 'wins']);

        $players = $players->toArray();

        if (!empty($players)) {
            $sort = new HelperRankSort;

            $updatedPlayers = $sort->sortRanks($players, 'wins');

            Weekly::where('week', $data['week'])
                ->orderBy('wins', 'desc')
                ->chunk(200, function ($players) use ($updatedPlayers) {
                    foreach ($players as $player) {
                        if (isset($updatedPlayers[$player->id]) && !empty($updatedPlayers[$player->id])) {
                            $player->rank = $updatedPlayers[$player->id];

                            $player->save();

                            $this->sumPlayer($player->player_id);
                        }
                    }
                });
        }

        Log::info('Weekly done. ' . $data['week']);
        $this->overallPlayerQueue();
    }

    public function overallPlayerQueue()
    {
//        $count = FrontlinesOverall::get()->count();
//
//        if (!$count > 0) {
//            Log::info('No players.');
//            return;
//        }
//
//        for ($i = 1; $i <= $count; $i+100) {
//            Log::info($i);
//            Queue::push('\Cleanse\Frontlines\Classes\Jobs\RankFrontlinesOverall', ['rank' => $i]);
//        }
    }

    public function overallPlayerSort($data)
    {
        $rank = $data['rank'];
        Overall::orderBy('wins', 'desc')
            ->skip($rank)->take(10)
            ->chunk(100, function ($frontlinesPlayers) use (&$rank) {
                foreach ($frontlinesPlayers as $player) {
                    $player->rank = $rank;

                    $player->save();

                    $rank++;
                }
            });
    }

    private function sumPlayer($player)
    {
        $stats = Weekly::where('player_id', $player)->get();

        $wins = $stats->sum('wins');
        //$matches = $stats->sum('matches');
        //$percent = round($wins / $matches * 100, 1);

        //Update Overall
        $update = Overall::where('player_id', $player)->first();

        $update->wins = $wins;
        //$update->matches = $matches;
        //$update->percent = $percent;

        $update->save();
    }
}
