<?
header('Content-type: text/plain');




if ( ! isset($_REQUEST['action']) ) {
	$_REQUEST['action']=array('read');
}

if ( in_array('save',$_REQUEST['action']) ) {
	/* build new $net from our request variables */
	$net=array();
	$net['interface']=array();

	/* eth0 */
	$net['interface']['eth0']['mode']=$_REQUEST['eth0_mode'];
	$net['interface']['eth0']['configuration']=$_REQUEST['eth0_configuration'];
	$net['interface']['eth0']['ip']=$_REQUEST['eth0_ip'];
	$net['interface']['eth0']['netmask']=$_REQUEST['eth0_netmask'];
	$net['interface']['eth0']['gateway']=$_REQUEST['eth0_gateway'];
	if ( '' != trim($_REQUEST['eth0_nameserver_primary']) )
		$net['interface']['eth0']['nameserver'][0]=$_REQUEST['eth0_nameserver_primary'];
	if ( '' != trim($_REQUEST['eth0_nameserver_secondary']) )
		$net['interface']['eth0']['nameserver'][1]=$_REQUEST['eth0_nameserver_secondary'];

	/* wlan0 */
	$net['interface']['wlan0']['mode']=$_REQUEST['wlan0_mode'];
	$net['interface']['wlan0']['configuration']=$_REQUEST['wlan0_configuration'];
	$net['interface']['wlan0']['ip']=$_REQUEST['wlan0_ip'];
	$net['interface']['wlan0']['netmask']=$_REQUEST['wlan0_netmask'];
	$net['interface']['wlan0']['gateway']=$_REQUEST['wlan0_gateway'];

	if ( '' != trim($_REQUEST['wlan0_nameserver_primary']) )
		$net['interface']['wlan0']['nameserver'][]=$_REQUEST['wlan0_nameserver_primary'];
	if ( '' != trim($_REQUEST['wlan0_nameserver_secondary']) )
		$net['interface']['wlan0']['nameserver'][]=$_REQUEST['wlan0_nameserver_secondary'];

	if ( 'wlan0_manual_ssid' == $_REQUEST['wlan0_ssid'] ) {
		$net['interface']['wlan0']['ssid']=$_REQUEST['wlan0_manual_ssid']; /* need to figure out manual versus auto */
	} else {
		$net['interface']['wlan0']['ssid']=$_REQUEST['wlan0_ssid']; /* need to figure out manual versus auto */
	}

	$net['interface']['wlan0']['psk']=$_REQUEST['wlan0_psk'];



/*

	print_r($net);

	printf("-----------------\n");

	$net2 = json_decode($settingsExisting,TRUE);

	print_r($_REQUEST);

	printf("-----------------\n");

	print_r($net2);

	printf("-----------------\n");
*/
	$w=array();
	$w['net']=$net;
	echo json_encode($w);
} 

if ( in_array('read',$_REQUEST['action']) ) {
	/* read and return net.json */
	$settingsExisting=file_get_contents("net.json");
	echo $settingsExisting;
}

if ( in_array('reboot',$_REQUEST['action']) ) {
	printf("\n\nnow we reboot");
}


?>
