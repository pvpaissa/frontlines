<?php

namespace Cleanse\Frontlines\Classes;

use Log;
use GuzzleHttp;
use Symfony\Component\DomCrawler\Crawler;
use Cleanse\PvPaissa\Classes\HelperDataCenters;

class FrontlinesCrawler
{
    public $server;
    public $week;
    private $players = [];

    public function __construct($server, $week)
    {
        $this->server = $server;
        $this->week = $week;
    }

    public function crawl()
    {
        // If week is false we are current.
        if (!$this->week) {
            return Log::info('Ended all from '.$this->server);
        }

        $dataCenterPlayers = $this->guzzle();

        $crawler = new Crawler($dataCenterPlayers);

        // If this week has no data, get the next week and rerun.
        if ($crawler->filterXPath('//*[@id="ranking"]/div[4]/div[2]/div/p[@class="ranking__no_data"]')->count()) {
            Log::info('Skipping week: '.$this->week.' on server '.$this->server);

            $nextPageNode = '//*[@id="ranking"]/div[4]/div[2]/div/div/a[contains(@class, \'ranking__calendar__next\')]';

            if ($crawler->filterXPath($nextPageNode)->count()) {
                $segments = explode('/', rtrim($crawler->filterXPath($nextPageNode)->attr('href'), '/'));
                $this->week = end($segments);
            } else {
                $this->week = false;
            }

            $this->crawl();
        }

        $xpath = '//*[@id="ranking"]/div[4]/div[2]/div/table/tbody/tr';
        $crawler->filterXPath($xpath)
            ->each(function (Crawler $node) {
                $player = [];

                //Data Center, Server, and Week
                $dc = new HelperDataCenters;
                $player['data_center'] = $dc->getDC($this->server);
                $player['server'] = $this->server;
                $player['week'] = $this->week;

                $characterNode = $node->filterXPath('//tr')->attr('data-href');
                $segments = explode('/', rtrim($characterNode, '/'));
                $characterId = end($segments);
                $player['character'] = $characterId;

                //Avatar
                $player['avatar'] = $node->filterXPath('//tr/td[2]/img')->attr('src');

                //Name and Character ID
                $player['name'] = $node->filterXPath('//tr/td[3]/h4')->text();

                //$player['pvp_rank'] = 0;

                //GC
                if ($node->filterXPath('//tr/td[4]/img')->count()) {
                    $player['grand_company'] = trim($node->filterXPath('//tr/td[4]/img')->attr('alt'));
                } else {
                    $player['grand_company'] = 'NA';
                }

                //Wins
                $player['wins'] = trim($node->filterXPath('//tr/td[5]')->text());

                $this->players[] = $player;
            });

        return $this->players;
    }

    private function guzzle()
    {
        $client = new GuzzleHttp\Client();

        $url = 'http://na.finalfantasyxiv.com/lodestone/ranking/frontline/weekly/';
        $url .= $this->week;
        $url .= '/?sort=win&filter=1';

        $urlVars = '&worldname='.$this->server;

        $res = $client->get($url.$urlVars);

        return $res->getBody()->getContents();
    }
}
