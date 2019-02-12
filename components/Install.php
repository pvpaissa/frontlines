<?php

namespace Cleanse\Frontlines\Components;

use Queue;
use Cms\Classes\ComponentBase;
use Cleanse\Pvpaissa\Classes\HelperDataCenters;

class Install extends ComponentBase
{
    public $message;

    public function componentDetails()
    {
        return [
            'name'            => 'PvPaissa Frontlines Installer.',
            'description'     => 'Adds completed frontlines weeks to the database.'
        ];
    }

    public function onRun()
    {
        $this->install();

        $this->message = $this->page['message'] = 'Queued.';
    }

    public function install()
    {
        $dataCenters = new HelperDataCenters;

        foreach ($dataCenters->datacenters as $dc) {
            foreach ($dc as $server) {
                $data = [
                    'server' => $server,
                    'week' => '201427'
                ];

                Queue::push('\Cleanse\Frontlines\Classes\Jobs\ScrapeFrontlines', $data);
            }
        }

        $week = ['week' => '201427'];
        Queue::push('\Cleanse\Frontlines\Classes\Jobs\RankFrontlinesWeekly', $week);
    }

    public function update()
    {
        Queue::push('\Cleanse\Frontlines\Classes\Jobs\RankFrontlinesOverall');
    }
}
