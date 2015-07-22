<?php
	//Config file for the module

	$WEB_SERVICES_HOST = "http://ita-provisioner.cis.fiu.edu";
	$WEB_SERVICES_PORT = "8080";
	$WEB_SERVICES_URL  = $WEB_SERVICES_HOST . ":" . $WEB_SERVICES_PORT . "/axis2/services/VirtualLabs";
	$VIRTUAL_LABS_WSDL     = $WEB_SERVICES_URL . "?wsdl";
	$MENTOR_SCHEDULER_WSDL = $WEB_SERVICES_URL . "MentorScheduler?wsdl";
	$VE_SCHEDULER_WSDL     = $WEB_SERVICES_URL . "VEScheduler?wsdl";
	$QUOTA_SYSTEM_WSDL     = $WEB_SERVICES_URL . "QuotaSystem?wsdl";

	$GUACAMOLE_HOST = "http://vlabs.cis.fiu.edu";//"http://vlabs.cis.fiu.edu";
	$GUACAMOLE_PORT = "8080";
	$GUACAMOLE_URL  = $GUACAMOLE_HOST . ":" . $GUACAMOLE_PORT . "/guacamole/#/client/c/";

?>