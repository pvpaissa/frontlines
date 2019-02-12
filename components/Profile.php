<?php

namespace Cleanse\Frontlines\Components;

use Cms\Classes\ComponentBase;
use Cleanse\Pvpaissa\Models\Player;
use Cleanse\Frontlines\Models\Weekly;

class Profile extends ComponentBase
{
    public $character;
    
    public $arr;
    public $hw;
    public $sb1;
    public $sb2;
    
    public $fullStatsEndedOn = '201838';

    public function componentDetails()
    {
        return [
            'name'            => 'FFXIV Frontlines character profile.',
            'description'     => 'Grabs the players Frontline stats.'
        ];
    }

    public function defineProperties()
    {
        return [
            'character' => [
                'title'       => 'Character Slug',
                'description' => 'Look up the character by their id.',
                'default'     => '{{ :character }}',
                'type'        => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->character = $this->page['character'] = $this->loadWeeklyStats();

        if ($this->character) {
            $this->arr  = $this->page['arr']    = $this->getStats($this->character->id, '201427', '201526');
            $this->hw   = $this->page['hw']     = $this->getStats($this->character->id, '201527', '201725');
            $this->sb1  = $this->page['sb1']    = $this->getStats($this->character->id, '201726', '201925');
            $this->sb2  = $this->page['sb2']    = $this->getStats($this->character->id, '201926', '999999');
        }
    }

    public function loadWeeklyStats()
    {
        $character = $this->property('character');

        return Player::with([
                'frontlines' => function($q) {
                    $q->orderBy('updated_at', 'desc');
                },
                'frontlines_week' => function($q) {
                    $q->orderBy('week', 'desc');
                }
            ])
            ->where('character', $character)
            ->first();
    }

    public function getStats($player, $start, $end)
    {
        $stats = Weekly::where('player_id', '=', $player)
            ->where('week', '>=', $start)
            ->where('week', '<=', $end)
            ->get();

        if ($stats->count()) {
            $season = [];
            $season['wins'] = $stats->sum('wins');

            return $season;
        } else {
            return false;
        }

    }
}
