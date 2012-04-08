trainMapper
===========

trainMapper is a command-line tool to generate KML files showing train journey routes.

A sample file is in `stratford.yml`, containing two lines (Stratford - Clapham Junction and Stratford - Bishop's Stortford).

Install steps
-------------

Acquire three components:

* [UniversalClassLoader](https://github.com/symfony/ClassLoader)
* [Buzz](https://github.com/kriswallsmith/Buzz)
* [Yaml](https://github.com/symfony/Yaml)

Update `include.php` with the correct path information.

Steps
-----

Firstly, get the coordinates:

```
cat lines.yml | php getCoordinates.php > coordinates
```

If trainMapper cannot find the coordinates for one or several of the stations, it will notify you. Correct the station articles on Wikipedia and re-run the command.

Secondly, generate the KML file:

```
cat coordinates.yml | php generateKml.php > map.kml
```

Finally, put `map.kml` on the web somewhere, and then you can enter the URL into Google Maps to display the stations with lines on the map.
