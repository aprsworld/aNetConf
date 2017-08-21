# aNetConf

interface
	mode
		enum of of {"ap","client","disabled"} /* AP only used with wireless interfaces */
	configuration
		enum of {"static","dhcp"} /* do not care if mode is "ap" or "disabled" */
	gateway
		ip address /* do not care if configuration is "dhcp" */
	ip
		ip addres /* do not care if configuration is "dhcp" */s
	netmask
		ip addres /* do not care if configuration is "dhcp" */s
	nameserver
		array of ip addresse /* do not care if configuration is "dhcp" */s

	psk
		string /* do not care if interface is not wireless */
	ssid 
		string /* do not care if interface is not wireless */
