<?php

namespace Cleanse\Frontlines\Classes\Jobs;

use Cleanse\Frontlines\Classes\RankingsUpdate;

class RankFrontlinesOverall
{
    public function fire($job, $data)
    {
        $update = new RankingsUpdate;

        $update->overallPlayerSort($data);

        $job->delete();
    }
}
