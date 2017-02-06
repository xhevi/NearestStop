<?php
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	$mysqli = new mysqli("localhost", "root", "root", "gtfsprague");
	/* check connection */
	if ($mysqli->connect_errno) {
	    printf("Connect failed: %s\n", $mysqli->connect_error);
	    exit();
	}

	/*
		@user_lat - users latitude
		@user_lon - users longitude
		@max_dist - maximal distance to scan from users coordinates
		@limit - number of POIs to return
	*/

	function find_NN($user_lat=0, $user_lon=0, $max_dist=2, $limit=10) {

		global $mysqli; // Remove this after integration

		$sql = "set @lon1 = ".$user_lon."-".$max_dist."/abs(cos(radians(".$user_lat."))*69)";
		$mysqli->query($sql);
		$sql = "set @lon2 = ".$user_lon."+".$max_dist."/abs(cos(radians(".$user_lat."))*69)";
		$mysqli->query($sql);
		$sql = "set @lat1 = ".$user_lat."-(".$max_dist."/69)";
		$mysqli->query($sql);
		$sql = "set @lat2 = ".$user_lat."+(".$max_dist."/69)";
		$mysqli->query($sql);

		$sql = "SELECT stop_id, stop_name, 3956*2 * ASIN(SQRT(\n"
	    . "POWER(SIN((".$user_lat." - ABS(s.stop_lat)) * pi()/180 / 2), 2) + COS(".$user_lat." * pi()/180 ) * COS(ABS(s.stop_lat) * pi()/180) * POWER(SIN((".$user_lon."-s.stop_lon) * pi()/180 / 2), 2) )) as distance\n"
	    . "FROM stops as s\n"
	    . "WHERE s.stop_lon\n"
	    . "between @lon1 and @lon2 \n"
	    . "and s.stop_lat \n"
	    . "between @lat1 and @lat2 \n"
	    . "having distance < ".$max_dist."\n"
	    . "ORDER BY distance limit ".$limit."\n";
		$result = $mysqli->query($sql);
		while ($request_list_row = $result->fetch_assoc()) {
		    $stops[] = $request_list_row;
		}
		return $stops;
	}

	$stops = find_NN(50.046629, 14.456472, 2, 10); // O2
	echo "<br><pre>Stops:";
	print_r($stops);
	echo "</pre>";

	$mysqli->close();

?>