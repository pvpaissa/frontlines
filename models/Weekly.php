<?php

namespace Cleanse\Frontlines\Models;

use Model;

/**
 * Class Weekly
 *
 * @property integer $id
 * @property integer $player_id
 * @property integer $rank
 * @property integer $wins
 * @property string  $percent -- deprecated
 * @property integer $matches -- deprecated
 * @property string  $week
 */
class Weekly extends Model
{
    public $table = 'cleanse_frontlines_week';

    public $fillable = [
        'player_id',
        'rank',
        'wins',
        'percent', // -- deprecated
        'matches', // -- deprecated
        'week'
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
