<?
header('Content-type: text/plain');


$settingsExisting=file_get_contents("net.json");


if ( ! isset($_REQUEST['action']) ) {
	$_REQUEST['action']='';
}

if ( $_REQUEST['action'] == "save_reboot" ) {

	$net = json_decode($settingsExisting,TRUE);

	print_r($_REQUEST);

	print_r($_net);
} 

/* read and return net.json */
echo $settingsExisting;

?>
