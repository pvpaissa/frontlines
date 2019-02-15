<?php

namespace Cleanse\Frontlines\Components;

use Auth;
use Queue;
use Cms\Classes\ComponentBase;
use Cleanse\Pvpaissa\Classes\HelperDataCenters;
use Cleanse\Frontlines\Classes\FrontlinesHelper;

class Install extends ComponentBase
{
    public $week;

    /* Empty week: 201443 */
    private $initialWeek = '201427';

    public function componentDetails()
    {
        return [
            'name'            => 'PvPaissa Frontlines Installer.',
            'description'     => 'Adds completed frontlines weeks to the database.'
        ];
    }

    public function onRun()
    {
        if (!Auth::check()) {
            return null;
        }

        $type = $this->prepareVars();

        if ($type) {
            $this->update($type);
        } else {
            $this->update($this->initialWeek);
        }
    }

    public function update($week)
    {
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

        $this->successMessage($week);
    }

    private function prepareVars()
    {
        $when = new FrontlinesHelper('Balmung');

        return $when->nextWeek();
    }

    private function successMessage($week)
    {
        $this->page['install_status'] = 'success';
        $this->page['install_message'] = 'Queued up week of: ' . $week;
    }
}
