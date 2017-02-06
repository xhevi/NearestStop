set @orig_lat=50.046629; set @orig_lon=14.425803; /* O2 Office */
set @orig_lat=50.1036729; set @orig_lon=14.3906642; /* NTK Office */
set @dist=2;

set @lon1 = @orig_lon-@dist/abs(cos(radians(@orig_lat))*69); 
set @lon2 = @orig_lon+@dist/abs(cos(radians(@orig_lat))*69); 
set @lat1 = @orig_lat-(@dist/69); 
set @lat2 = @orig_lat+(@dist/69);

SELECT stop_id, stop_name, 3956 * 2 * ASIN(SQRT(
POWER(SIN((@orig_lat - ABS(s.stop_lat)) * pi()/180 / 2), 2) +  COS(@orig_lat * pi()/180 ) * COS(ABS(s.stop_lat) * pi()/180) *  POWER(SIN((@orig_lon-s.stop_lon) * pi()/180 / 2), 2) )) as  distance
FROM stops as s
WHERE  s.stop_lon
between @lon1 and @lon2 
and s.stop_lat 
between @lat1 and @lat2 
having distance < @dist
ORDER BY distance limit 10