<?php

namespace Cleanse\Frontlines\Classes\Jobs;

use Log;
use Cleanse\Frontlines\Classes\RankingsUpdate;

class ScrapeFrontlines
{
    public function fire($job, $data)
    {
        $crawl = new RankingsUpdate();
        $crawl->updateWeek($data);

        $job->delete();
    }
}
