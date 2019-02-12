<?php

namespace Cleanse\Frontlines\Classes\Jobs;

use Log;
use Cleanse\Frontlines\Classes\RankingsUpdate;

class ScrapeFrontlines
{
    public function fire($job, $data)
    {
        if ($data['server'] == 'Aegis') {
            Log::info('Starting install.');
        }

        if ($data['server'] == 'Zodiark') {
            Log::info('Installed.');
        }

        $crawl = new RankingsUpdate();
        $crawl->updateWeek($data);

        $job->delete();
    }
}
