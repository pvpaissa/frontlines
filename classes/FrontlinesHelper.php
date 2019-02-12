<?php

namespace Cleanse\Frontlines\Classes;

use GuzzleHttp;
use Symfony\Component\DomCrawler\Crawler;
use Cleanse\Frontlines\Models\Weekly;

class FrontlinesHelper
{
    public $server;

    public function __construct($server)
    {
        $this->server = $server;
    }

    public function nextWeek()
    {
        $lastWeek = $this->getLast();

        if ($lastWeek) {
            return $this->crawl($lastWeek);
        } else {
            return $lastWeek;
        }
    }

    public function getLast()
    {
        $server = $this->server;

        $last = Weekly::whereHas('player', function($q) use ($server) {
            $q->where('server', 'like', '%'.$server.'%');
        })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!is_null($last)) {
            return $last->week;
        } else {
            return false;
        }
    }

    private function crawl($week)
    {
        $url = "http://na.finalfantasyxiv.com/lodestone/ranking/frontline/weekly/{$week}/";

        if ($week == '201427') {
            $nextPageNode = '//*[@id="ranking"]/div[4]/div[2]/div/div[1]/a';
        } else {
            $nextPageNode = '//*[@id="ranking"]/div[4]/div[2]/div/div[1]/a[2]';
        }

        $lastWeekStandings = $this->guzzle($url);

        $crawler = new Crawler($lastWeekStandings);

        if ($crawler->filterXPath($nextPageNode)->count()) {
            $segments = explode('/', rtrim($crawler->filterXPath($nextPageNode)->attr('href'), '/'));
            $nextWeek = end($segments);
        } else {
            $nextWeek = false;
        }

        return $nextWeek;
    }

    private function guzzle($url)
    {
        $client = new GuzzleHttp\Client();

        $res = $client->get($url);

        return $res->getBody()->getContents();
    }
}
