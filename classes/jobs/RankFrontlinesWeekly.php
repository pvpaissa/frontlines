<?php

namespace Cleanse\Frontlines\Classes\Jobs;

use Cleanse\Frontlines\Classes\RankingsUpdate;

class RankFrontlinesWeekly
{
    public function fire($job, $data)
    {
        $crawl = new RankingsUpdate();
        $crawl->weeklyPlayerSort($data);

        $job->delete();
    }
}
