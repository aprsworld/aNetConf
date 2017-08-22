<?php

function iwlistScan_parse($s) {
	$lines = explode("\n",$s);

	$cells=array();
	$ourAddress="";

	for ( $i=1 ; $i<count($lines) ; $i++ ) {
		$line=trim($lines[$i]);

//		echo $line . "\n";


		$sepPos=strpos($line,":");

		if ( FALSE === $sepPos ) {
			/* everything appears to be ':' delimited besides the signal. */
			// Quality=0/100  Signal level=96/100  
			// Signal: -38 dBm  Quality: 70/70
			// Quality=100/100  Signal level:-47dBm  Noise level=-100dBm

			$signalPos=strpos($line,'Signal');
			if ( FALSE !== $signalPos ) {
				$signalString=substr($line,$signalPos);

//				printf("# signalString='%s'\n",$signalString);

				$var = array_filter(preg_split("/\D+/", $signalString));
				$signalLevel=reset($var);

//				printf("# signalLevel='%s'\n",$signalLevel);

				if ( $signalLevel <= 0 ) {
					/* negative value is probably dBm */
					$cells[$ourAddress]['signal']=$signalLevel;
				} else {
					/* positive value is probably percentage thing */
					/* Signal level to rough dBm = (fraction_of_total/2)-100 according to 
					https://stackoverflow.com/questions/31681696/how-to-get-iwconfig-to-display-signal-level-as-dbm-instead-of-a-fraction*/
					$cells[$ourAddress]['signal']=($signalLevel/2)-100;
				}
			}

			continue;
		}

		$key=trim(substr($line,0,$sepPos));
		$value=trim(substr($line,$sepPos+1));

//		printf("key='%s' value='%s'\n",$key,$value);

		if ( FALSE !== strpos($line,"- Address:" ) ) {
			if ( '' != $ourAddress ) {
				$cells[$ourAddress]['security']=implode("-",$security);
			}

			$ourAddress=$value;

//			printf("#########################################################################\n");
//			printf("# detected start of new block. Address='%s'\n",$ourAddress);
			$security=array();
		} else if ( 'ESSID' == $key ) {
			$cells[$ourAddress]['ssid']=str_replace('"', '', $value);
		} else if ( 'IE' == $key ) {
			if ( FALSE !== strpos($line,"WPA2") ) {
				$security[]='WPA2';
			} else if ( FALSE !== strpos($line,"WPA ") ) {
				$security[]='WPA';
			}
		} else if ( 'PSK' == $value ) {
				if ( FALSE === in_array('PSK',$security) )
					$security[]='PSK';
		} else if ( 'CCMP' == $value ) {
				if ( FALSE === in_array('CCMP',$security) )
					$security[]='CCMP';
		}

//		echo "--------------------------------------\n";
	}

	/* save gatered security from last cell */
	if ( '' != $ourAddress ) {
		$cells[$ourAddress]['security']=implode("-",$security);
	}

	return $cells;
}

/* merge access points by name */
function ap_merge($ap) {
	$networks=array();
	$nn=0;

	foreach ($ap as $ssid => $data) {
		$key =  array_search($data['ssid'], array_column($networks,'ssid'));
		if ( FALSE === $key ) { 
			$networks[$nn]['ssid']=$data['ssid'];
			$networks[$nn]['signal']=$data['signal'];
			$networks[$nn]['security']=$data['security'];
			$networks[$nn]['nAccessPoints']=1;
			$nn++;
		} else {
			$networks[$key]['nAccessPoints']++;
			if ( $data['signal'] > $networks[$key]['signal'] ) {
				$networks[$key]['signal']=$data['signal'] ;
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

/* seems to need to run multiple times before it actually finishes the scan. */
shell_exec("sudo iwlist wlan0 scan");
shell_exec("sudo iwlist wlan0 scan");
$iwlistOutput = shell_exec("sudo iwlist wlan0 scan");

/* get individual access points */
$aps = iwlistScan_parse($iwlistOutput);

/* merge down to networks */
$networks = ap_merge($aps);

echo json_encode($networks);
//echo json_encode(wpaSupplicantScan_parse(shell_exec('sudo wpaSupplicantScan 2> /dev/null')));

?>
