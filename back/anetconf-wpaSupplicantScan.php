<?php

function wpaSupplicantScan_parse($s) {
	$ap=array();

	$lines=explode("\n",$s);


	for ( $apN=0 ; $apN<count($lines) ; $apN++ ) {
		$parts=explode("\t",$lines[$apN]);

		if ( count($parts) < 5 )
			continue;

		$ap[$apN]['bssid']=$parts[0];
		$ap[$apN]['frequency']=$parts[1];
		$ap[$apN]['signal']=$parts[2];

		/* clean up the security field */
		/* remove ESS, since everything seems to broadcast it anyhow */
		$parts[3]=str_replace("[ESS]","",$parts[3]);
		$parts[3]=str_replace("[","",$parts[3]);
		$parts[3]=str_replace("]","",$parts[3]);


		$ap[$apN]['security']=$parts[3];


		$ap[$apN]['ssid']=$parts[4];
	}

	return $ap;
}

/* merge access points by name */
function ap_merge($ap) {
	$networks=array();
	$nn=0;


	for ( $i=0 ; $i<count($ap) ; $i++ ) {
		$key =  array_search($ap[$i]['ssid'], array_column($networks,'ssid'));
		if ( FALSE === $key ) { 
			$networks[$nn]['ssid']=$ap[$i]['ssid'];
			$networks[$nn]['signal']=$ap[$i]['signal'];
			$networks[$nn]['security']=$ap[$i]['security'];
			$networks[$nn]['nAccessPoints']=1;
			$nn++;
		} else {
			$networks[$key]['nAccessPoints']++;
			if ( $ap[$i]['signal'] > $networks[$key]['signal'] ) {
				$networks[$key]['signal']=$ap[$i]['signal'] ;
			}
		}
	}


	return $networks;
}

/* JavaScript requires cross origin header if page using JSON data isn't on the
same server */
$oparts=parse_url($_SERVER["HTTP_REFERER"]);
$origin=$oparts['scheme'] . "://" . $oparts['host'];
header("Access-Control-Allow-Origin: " . $origin);

header('Content-type: text/plain');

/* do scan using wpaSupplicantScan python script */
$wpaSupplicantScan = shell_exec('sudo wpaSupplicantScan 2> /dev/null');

/* get individual access points */
$aps = wpaSupplicantScan_parse($wpaSupplicantScan);

/* merge down to networks */
$networks = ap_merge($aps);

echo json_encode($networks);
//echo json_encode(wpaSupplicantScan_parse(shell_exec('sudo wpaSupplicantScan 2> /dev/null')));

?>
