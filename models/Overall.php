<?php

namespace Cleanse\Frontlines\Models;

use Model;

class Overall extends Model
{
    public $table = 'cleanse_frontlines_overall';

    public $fillable = [
        'player_id',
        'rank',
        'wins',
        'percent',
        'matches'
    ];

    public $hasOne = [
        'player' => [
            'Cleanse\Pvpaissa\Models\Player',
            'key' => 'id',
            'otherKey' => 'player_id'
        ]
    ];

    public $belongsTo = [
        'player' => [
            'Cleanse\Pvpaissa\Models\Player',
            'key' => 'player_id'
        ]
    ];
}
