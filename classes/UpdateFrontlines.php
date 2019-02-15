<?php

namespace Cleanse\Frontlines\Classes;

use Cleanse\Frontlines\Models\Overall;
use Cleanse\Frontlines\Models\Weekly;

class UpdateFrontlines
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function update($player)
    {
        $this->addMode($player);
        $this->addWeek($player);
    }

    public function addMode($player)
    {
        Overall::firstOrCreate([
            'player_id' => $player
        ]);
    }

    private function addWeek($player)
    {
        Weekly::firstOrCreate([
            'player_id' => $player,
            'wins' => $this->data['wins'],
            'week' => $this->data['week']
        ]);
    }
}
