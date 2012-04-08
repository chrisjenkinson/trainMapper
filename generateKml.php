<?php

require_once 'include.php';

use Symfony\Component\Yaml\Yaml;

list ($lines, $stationsUnique) = Yaml::Parse(stream_get_contents(STDIN));

foreach ($lines as $keyLine => $line)
{
	foreach ($line['stations'] as $keyStation => $station)
	{
		foreach ($stationsUnique as $k => $v)
		{
			if ($v['origName'] == $station)
			{
				$lines[$keyLine]['stations'][$keyStation] = $v;
				break;
			}
		}
	}
}

$xml = new XMLWriter();

$xml->openMemory();

$xml->setIndentString("\t");
$xml->setIndent(true);

$xml->startDocument('1.0', 'UTF-8');

$xml->startElement('kml');

$xml->writeAttribute('xmlns', 'http://www.opengis.net/kml/2.2');

$xml->startElement('Document');

$xml->writeElement('name', 'Railway stations');

$xml->startElement('Style');

$xml->writeAttribute('id', 'RS');

$xml->startElement('LineStyle');

$xml->writeElement('color', '000000');

$xml->writeElement('width', '15');

$xml->endElement(); // LineStyle

$xml->endElement(); // Style

foreach ($lines as $line)
{
	$xml->startElement('Folder');
	
	$xml->writeElement('name', $line['name']);
	$xml->writeElement('description', $line['name']);

	foreach ($line['stations'] as $station)
	{
		$xml->startElement('Placemark');

		$xml->writeElement('name', $station['name']);
		$xml->writeElement('description', $station['name']);

		$xml->startElement('Point');

		$xml->writeElement('coordinates', sprintf('%f,%f', $station['lng'], $station['lat']));

		$xml->endElement(); // Point

		$xml->endElement(); // Placemark
	}
	
	$xml->startElement('Placemark');
	
	$xml->startElement('LineString');
	
	$xml->startElement('coordinates');
	
	foreach ($line['stations'] as $station)
	{
		$xml->text(sprintf('%f,%f ', $station['lng'], $station['lat']));
	}
	
	$xml->endElement(); // coordinates
	
	$xml->endElement(); // LineString
	
	$xml->endElement(); // Placemark

	$xml->endElement(); // Folder
}

$xml->endElement(); // Document

$xml->endElement(); // kml

$xml->endDocument();

echo $xml->flush();

