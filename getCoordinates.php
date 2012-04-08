<?php

require_once 'include.php';
require_once 'Buzz/lib/Buzz/Browser.php';

use Symfony\Component\Yaml\Yaml;

$apiString = '';

$api = array(
	'format'	=> 'json',
	'action'	=> 'query',
	'prop'		=> 'revisions',
	'rvprop'	=> 'content',
	'redirects'	=> 'true'
);

foreach ($api as $k => $v)
{
	$apiString .= sprintf('%s=%s&', $k, $v);
}

$lines = Yaml::parse(stream_get_contents(STDIN));

$client = new Buzz\Client\Curl();
$curl = $client->getCurl();

curl_setopt($curl, CURLOPT_USERAGENT, 'Railway station scraper (chris@chrisjenkinson.org)');

$browser = new Buzz\Browser($client);

$error = 0;

$stations = array();

foreach ($lines as $line)
{
	$stations = array_merge($stations, $line['stations']);
}

$stationsUnique = array_unique($stations);

sort($stationsUnique);

$full = array();

foreach ($stationsUnique as $station)
{
	$foundError = false;
	
	$response = $browser->get('http://en.wikipedia.org/w/api.php?' . $apiString . 'titles=' . urlencode(str_replace(' ', '_', $station . ' railway station')));

	$contentJson = $response->getContent();

	$contentTmp = json_decode($contentJson, true);

	$page = reset($contentTmp['query']['pages']);
	
	if (key($contentTmp['query']['pages']) == -1)
	{
		fwrite(STDERR, sprintf("Could not find station '%s'.\n", $station));
		$foundError = true;
	}
	else
	{
		preg_match('/latitude(\s*)\=(\s*)(\-*)([0-9]+)\.([0-9]+)/', $page['revisions'][0]['*'], $latMatches);
		preg_match('/longitude(\s*)\=(\s*)(\-*)([0-9]+)\.([0-9]+)/', $page['revisions'][0]['*'],
	$lngMatches);

		if (!(empty($latMatches) || empty($lngMatches)))
		{
			$fullTmp = array(
				'origName'	=> $station,
				'name'		=> $page['title'],
				'lat'		=> sprintf('%s%s.%s', $latMatches[3], $latMatches[4], $latMatches[5]),
				'lng'		=> sprintf('%s%s.%s', $lngMatches[3], $lngMatches[4], $lngMatches[5])
			);
		}
		else
		{
			fwrite(STDERR, sprintf("No co-ordinates found for station '%s' (checking article '%s').\n", $station, $page['title']));
			$foundError = true;
		}
	}	
	
	if (!$foundError)
	{
		$full[] = $fullTmp;
	}
	else
	{
		$error++;
	}
}

if ($error)
{
	fwrite(STDERR, 'Could not find ' . $error . ' station(s). Please check and correct the name(s) as appropriate.' . "\n");
	die();
}

echo Yaml::Dump(array($lines, $full));

