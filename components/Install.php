<?php

namespace Cleanse\Frontlines\Components;

use Auth;
use Queue;
use Cms\Classes\ComponentBase;
use Cleanse\Pvpaissa\Classes\HelperDataCenters;

//
use Cleanse\Frontlines\Classes\FrontlinesHelper;

class Install extends ComponentBase
{
    public $message;

    private $initialWeek = '201427';
    private $emptyWeek = '201443';


    public function componentDetails()
    {
        return [
            'name'            => 'PvPaissa Frontlines Installer.',
            'description'     => 'Adds completed frontlines weeks to the database.'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'title'       => 'Install Type',
                'description' => 'Install or update.',
                'default'     => '{{ :type }}',
                'type'        => 'string'
            ]
        ];
    }

    public function onRun()
    {
        if (!Auth::check()) {
            return null;
        }

        $type = $this->property('type');

        if ($type == 'update') {
            $this->update();
        } else {
            $this->install();
        }
    }

    public function install()
    {
        $when = new FrontlinesHelper('Balmung');
        $week = $when->nextWeek();

        //Already Installed
        if ($week) {
            $this->page['install_status'] = 'danger';
            $this->page['install_message'] = 'Already Installed.';
            return;
        }

        $dataCenters = new HelperDataCenters;

        foreach ($dataCenters->datacenters as $dc) {
            foreach ($dc as $server) {
                $data = [
                    'server' => $server,
                    'week' => $this->initialWeek
                ];

                Queue::push('\Cleanse\Frontlines\Classes\Jobs\ScrapeFrontlines', $data);
            }
        }

        $week = ['week' => $this->initialWeek];
        Queue::push('\Cleanse\Frontlines\Classes\Jobs\RankFrontlinesWeekly', $week);

        $this->page['install_status'] = 'success';
        $this->page['install_message'] = 'Queued up week of: ' . $this->initialWeek;
    }

    //Will be for catching up on weeks.
    public function update()
    {
        set_time_limit(8180);// seconds

        $when = new FrontlinesHelper('Balmung');
        $week = $when->nextWeek();

        $dataCenters = new HelperDataCenters;

        foreach ($dataCenters->datacenters as $dc) {
            foreach ($dc as $server) {
                $data = [
                    'server' => $server,
                    'week' => $week
                ];

                Queue::push('\Cleanse\Frontlines\Classes\Jobs\ScrapeFrontlines', $data);
            }
        }

        Queue::push('\Cleanse\Frontlines\Classes\Jobs\RankFrontlinesWeekly', ['week' => $week]);

        $this->page['install_status'] = 'success';
        $this->page['install_message'] = 'Queued up week of: ' . $week;
    }
}
