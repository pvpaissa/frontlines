<?php

namespace Cleanse\Frontlines\Components;

use Config;
use Cms\Classes\ComponentBase;
use Cleanse\Frontlines\Models\Overall;

class Rankings extends ComponentBase
{
    public $rankings;
    public $last;

    public function componentDetails()
    {
        return [
            'name'            => 'Overall Frontline Leaders',
            'description'     => 'Grabs the total wins for frontlines players.'
        ];
    }

    public function onRun()
    {
        $this->rankings = $this->page['rankings'] = $this->loadRankings();
        $this->last = $this->page['last_update'] = $this->getLastUpdate();
        $this->last = $this->page['year_week'] = $this->getYW();
    }

    public function loadRankings()
    {
        return Overall::orderBy('wins', 'desc')
            ->orderBy('rank')
            ->paginate(50);
    }

    public function onServer()
    {
        $server = post('newItem', 'Balmung');

        if ($server != '') {
            $server = Overall::whereHas('player', function($q) use ($server){
                $q->where('name', 'like', '%'.$server.'%');
            })
                ->orderBy('rank')
                ->get();
        }

        $this->page['items'] = $server;
    }

    private function getLastUpdate()
    {
        return date('Y-m-d', strtotime('last monday -7 days'));
    }

    private function getYW()
    {
        return date('YW', strtotime('last sunday -7 days'));
    }
}
