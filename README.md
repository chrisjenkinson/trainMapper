trainMapper
===========

A sample file is in `stratford.yml`, containing two lines (Stratford - Clapham Junction and Stratford - Bishop's Stortford).

Steps:

  cat lines.yml | php getCoordinates.php > coordinates.yml

If trainMapper cannot find the coordinates for one or several of the stations, it will notify you. Correct the station articles on Wikipedia and re-run the command.

  cat coordinates.yml | php generateKml.php > map.kml

Put `map.kml` on the web somewhere, and then you can enter the URL into Google Maps to display the stations with lines on the map.
