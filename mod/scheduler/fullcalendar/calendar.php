<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/user/profile/lib.php');

require_once($CFG->libdir."/crypt.php");

require_once($CFG->libdir .'/ddllib.php');

require_once('parser.php');
require_once 'xmlserializer/serialize.php';
require_once 'xmlserializer/classes.php';

ini_set("soap.wsdl_cache_enabled", "0");


$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";

		
if (isset($_POST['action'])){
	$action = $_POST['action'];   
}else{
	$action = "";
}


if (isset($_POST['arr'])){
	$arr = $_POST['arr'];   
}else{
	$arr = "";
} 
		  
echo $arr;

//echo "isadmin: ".isadmin(162);
	
//************************************************************************************************
 
  if ($action=='getHostList'){
  	header("Content-type: text/x-json");
  	//header('Content-Type: text/xml');
	
	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		}
  
	try {
				
			$active = true;		
			
			$params1 = array( 'requestingUser' => $requestingUser,
							  'active' => $active );
			
			$client=new SoapClient($wsdl,array('location'=>$location));
	
			$result = $client->getHostList($params1);
	
			//$xml = new array2xml('hosts');
			
			//$xml->createNode( $result->host );
			
			//echo $xml;
						
		
			//$hosts = $result->host;
			echo json_encode($result->host);
	
	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}
 
 
 }else if ($action=='getConfiguration'){		
	header("Content-type: text/x-json");
		
	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		}

		try {		
			$params = array('requestingUser' => $requestingUser );
			
			$client=new SoapClient($wsdl,array('location'=>$location));
			
			$result = $client->getConfiguration($params);
			
			echo json_encode($result);
	
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}
		
		
}

else if ($action=='setConfiguration'){	

	header("Content-type: text/x-json");

	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		}
	if (isset($_POST['startUser'])){
		$startUser = $_POST['startUser']; 
		}
	if (isset($_POST['endUser'])){
		$endUser = $_POST['endUser']; 
		}
	if (isset($_POST['startAdmin'])){
		$startAdmin = $_POST['startAdmin']; 
		}
	if (isset($_POST['endAdmin'])){
		$endAdmin = $_POST['endAdmin']; 
		}

		
		try {
			$params = array('requestingUser' => $requestingUser,
							'userStartTime' => $startUser,
							'userEndTime' => $endUser,
							'adminStartTime' =>$startAdmin,
							'adminEndTime' =>$endAdmin);

			$client=new SoapClient($wsdl,array('location'=>$location));
			
			$result = $client->setConfiguration($params);

			echo json_encode($result);
	
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}

}else if ($action=='addHost'){	

	header("Content-type: text/x-json");

	if (isset($_POST['requestingUser'])){
	$requestingUser = $_POST['requestingUser']; 
	}
	if (isset($_POST['name'])){
	$name = $_POST['name']; 
	}
	if (isset($_POST['sshPort'])){
	$sshPort = $_POST['sshPort']; 
	}
	if (isset($_POST['numberCap'])){
	$numberCap = $_POST['numberCap']; 
	}
	if (isset($_POST['firstFreePort'])){
	$firstFreePort = $_POST['firstFreePort']; 
	}
	if (isset($_POST['username'])){
	$username = $_POST['username']; 
	}
	if (isset($_POST['password'])){
	$password = $_POST['password']; 
	}
	if (isset($_POST['active'])){
	$active = $_POST['active']; 
	}
	if (isset($_POST['portNumber'])){
	$portNumber = $_POST['portNumber']; 
	}
	
	
	$act = 0;
	if($active=='true')
		$act = 1;
	
		try {
			$host = array(  'id' => '',
							'name' => $name,
							'sshPort' =>$sshPort,
							'username' => $username,
							'password' =>$password,
							'veNumCap'=>$numberCap,
							'veFirstFreePort' => $firstFreePort,
							'vePortNum'=>$portNumber,
							'active'=>$act	);
							
			$params = array('requestingUser' => $requestingUser,
							'host' => $host);

			$client=new SoapClient($wsdl,array('location'=>$location));
			
			$result = $client->addHost($params);

			echo json_encode($result);
	
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}


}else if ($action=='deleteHost'){	

	header("Content-type: text/x-json");

	if (isset($_POST['requestingUser'])){
	$requestingUser = $_POST['requestingUser']; 
	}
	if (isset($_POST['id'])){
	$id = $_POST['id']; 
	}
	
	try {
						
		$params = array('requestingUser' => $requestingUser,
						'id' => $id);

		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->delHost($params);

		echo json_encode($result);

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}

}else if ($action=='getHost'){	

	header("Content-type: text/x-json");
	
	if (isset($_POST['requestingUser'])){
	$requestingUser = $_POST['requestingUser']; 
	}
	if (isset($_POST['id'])){
	$id = $_POST['id']; 
	}
	
	try {
						
		$params = array('requestingUser' => $requestingUser,
						'id' => $id);

		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->getHost($params);

		echo json_encode($result);

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}
}else if ($action=='setHost'){	

	header("Content-type: text/x-json");

	if (isset($_POST['requestingUser'])){
	$requestingUser = $_POST['requestingUser']; 
	}
	if (isset($_POST['id'])){
	$id = $_POST['id']; 
	}

	if (isset($_POST['name'])){
	$name = $_POST['name']; 
	}
	if (isset($_POST['sshPort'])){
	$sshPort = $_POST['sshPort']; 
	}
	if (isset($_POST['numberCap'])){
	$numberCap = $_POST['numberCap']; 
	}
	if (isset($_POST['firstFreePort'])){
	$firstFreePort = $_POST['firstFreePort']; 
	}
	if (isset($_POST['username'])){
	$username = $_POST['username']; 
	}
	if (isset($_POST['password'])){
	$password = $_POST['password']; 
	}
	if (isset($_POST['active'])){
	$active = $_POST['active']; 
	}
	if (isset($_POST['portNumber'])){
	$portNumber = $_POST['portNumber']; 
	}

	$act = 0;
	if($active=='true')
		$act = 1;
	
	
	try {
						
		$host = array( 'id' => $id,
						'name' => $name,
						'sshPort' =>$sshPort,
						'username' => $username,
						'password' =>$password,
						'veNumCap'=>$numberCap,
						'veFirstFreePort' => $firstFreePort,
						'vePortNum'=>$portNumber,
						'active'=>$act	);
						
		$params = array('requestingUser' => $requestingUser,
						'host' => $host);

		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->setHost($params);

		echo json_encode($result);

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}		

}else if ($action=='getUserResourceTypes'){

	header('Content-Type: text/xml');


	if (isset($_POST['requestingUser'])){
			$requestingUser = $_POST['requestingUser']; 
			$username = $_POST['username'];   
		}else{
			$action = "";
		}

		try {
			$params = array( 'requestingUser' => $requestingUser, 'userName' => $username );
			
			$client=new SoapClient($wsdl,array('location'=>$location));
			
			$result = $client->getUserResourceTypes($params);
	
			$xml = new array2xml('resources');
			
			$xml->createNode( $result );
			
			echo $xml;
		
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}

}else if ($action=='getAppointments'){
/*header('Content-Type: text/x-json');
$file = file_get_contents('calendarEvents4.xml');
		echo $file; 

*/
	header('Content-Type: text/x-json');

	if (isset($_POST['requestingUser'])){
			$requestingUser = $_POST['requestingUser']; 
			$username = $_POST['username'];
			$start = $_POST['start']; 
			$end = $_POST['end']; 
			
			$requestType = $_POST['requestType']; 
		}else{
			$action = "";
		}
		
		try {
		
			$params = array( 'requestingUser' => $requestingUser,
							 'userName' => $username,
							 'start' => $start,
							 'end' => $end );
			
			$client=new SoapClient($wsdl,array('location'=>$location));
			
			
			if($requestType == "User"){
				$result = $client->getUserAppointments($params);
			}else if($requestType == "Mentor"){
				$result = $client->getMentorAppointments($params);			
			}else if($requestType == "Host"){
				$result = $client->getHostAppointments($params);			
			}
			
			echo json_encode($result->appointments);
			
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}
		
		
}else if ($action=='scheduleAppointments'){
	header('Content-Type: text/x-json');

	if (isset($_POST['requestingUser'])){
			$id = $_POST['id'];
			$requestingUser = $_POST['requestingUser']; 
			$username = $_POST['username'];
			$start = $_POST['start']; 
			$end = $_POST['end']; 
			$resourceType = $_POST['resourceType']; 
			$course = $_POST['course']; 
			$affiliationId = $_POST['affiliationId'];
			$availabilityStatus = $_POST['availabilityStatus'];
			
			$requestType = $_POST['requestType']; 
		}
		
		try {
		
			$appointment = array('id' => $id,
								'userName' => $username,
								'start' => $start,
								'end' => $end,
								'resourceType' => $resourceType,
								'course' => $course,
								'affiliationId' => $affiliationId,
								'availabilityStatus' => $availabilityStatus,
								'action' => NULL);
								
			$params = array('requestingUser' => $requestingUser,
							'appointment' => $appointment);
							
			//print_r($params);
			$client=new SoapClient($wsdl,array('location'=>$location));
			
			
			
			if($requestType == "User"){
				$result = $client->scheduleUserAppointments($params);
			}else if($requestType == "Mentor"){
				$result = $client->scheduleMentorAppointments($params);	
			}else if($requestType == "Host"){
				$result = $client->scheduleHostAppointments($params);			
			}
			
			
			echo json_encode($result->appointment);
			//print_r($result->appointment);

			
			
		}catch (SoapFault $soapfault) {
		
			//echo $soapfault->getMessage();
			echo $soapfault->getTraceAsString();
		}catch (Exception $e) {
		
			echo $e->getMessage();
		
		
	
		}
		


} else if ($action == 'scheduleAppointmentsWithEncryptedPassword') {
	header('Content-Type: text/x-json');
	
	if (isset($_POST['requestingUser'])) {
			$id = $_POST['id'];
			$requestingUser = $_POST['requestingUser']; 
			$username = $_POST['username'];
			$encryptedPassword = $_POST['encryptedPassword'];
			$start = $_POST['start']; 
			$end = $_POST['end']; 
			$resourceType = $_POST['resourceType']; 
			$course = $_POST['course']; 
			$affiliationId = $_POST['affiliationId'];
			$availabilityStatus = $_POST['availabilityStatus'];
			
			$requestType = $_POST['requestType']; 
		}
	
	try {
			$appointment = array('id' => $id,
								'userName' => $username,
								'start' => $start,
								'end' => $end,
								'resourceType' => $resourceType,
								'course' => $course,
								'affiliationId' => $affiliationId,
								'availabilityStatus' => $availabilityStatus,
								'action' => NULL);
			$encryptedCredential = array(
								'userName' => $username,
								'encryptedPassword' => $encryptedPassword);
			
			$params = array('requestingUser' => $requestingUser,
							'appointment' => $appointment,
							'encryptedCredential' => $encryptedCredential);
							
			$client = new SoapClient($wsdl, array('location' => $location));
			
			if ($requestType == "User") {
				$result = $client->scheduleUserAppointmentsWithEncryptedPassword($params);
			} else if($requestType == "Mentor") {
				$result = $client->scheduleMentorAppointments($params);	
			} else if($requestType == "Host") {
				$result = $client->scheduleHostAppointments($params);			
			}
			
			echo json_encode($result->appointment);
						
		} catch (SoapFault $soapfault) {
			//echo $soapfault->getMessage();
			echo $soapfault->getTraceAsString();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
}else if ($action=='scheduleRecurringAppointments'){
	header('Content-Type: text/x-json');

	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		$username = $_POST['username'];
		$appointments = $_POST['appointments'];
		
		$requestType = $_POST['requestType'];
	}
	
	/*
	$recurrAppointments=array();
	foreach ($appointments as $appointment) {
		array_push($recurrAppointments,"appointment"=>appointment);
	}
	*/
	try {
											
		$client=new SoapClient($wsdl,array('location'=>$location));
														  
		if($requestType == "User"){
			$result = $client->scheduleUserAppointments(array('requestingUser' => $requestingUser,
														  'appointment' => $appointments));
		}else if($requestType == "Mentor"){
			$result = $client->scheduleMentorAppointments(array('requestingUser' => $requestingUser,
														  'appointment' => $appointments));
		}else if($requestType == "Host"){
			$result = $client->scheduleHostAppointments(array('requestingUser' => $requestingUser,
														  'appointment' => $appointments));
		}
	
		echo json_encode($result->appointment);
		//print_r($result->appointment);

		
		
	}catch (SoapFault $soapfault) {
	
		//echo $soapfault->getMessage();
		echo $soapfault->getTraceAsString();
	}catch (Exception $e) {
	
		echo $e->getMessage();
	
	

	}


} else if ($action=='scheduleRecurringAppointmentsWithEncryptedPassword') {
	header('Content-Type: text/x-json');

	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		$username = $_POST['username'];
		$encryptedPassword = $_POST['encryptedPassword'];
		$appointments = $_POST['appointments'];
		
		$requestType = $_POST['requestType'];
	}
	
	/*
	$recurrAppointments=array();
	foreach ($appointments as $appointment) {
		array_push($recurrAppointments,"appointment"=>appointment);
	}
	*/
	$encryptedCredential = array(
		'userName' => $username,
		'encryptedPassword' => $encryptedPassword);
	
	try {
											
		$client=new SoapClient($wsdl,array('location'=>$location));
														  
		if($requestType == "User"){
			$result = $client->scheduleUserAppointmentsWithEncryptedPassword(
				array(
					'requestingUser' => $requestingUser,
					'appointment' => $appointments,
					'encryptedCredential' => $encryptedCredential));
		}else if($requestType == "Mentor"){
			$result = $client->scheduleMentorAppointments(array('requestingUser' => $requestingUser,
														  'appointment' => $appointments));
		}else if($requestType == "Host"){
			$result = $client->scheduleHostAppointments(array('requestingUser' => $requestingUser,
														  'appointment' => $appointments));
		}
	
		echo json_encode($result->appointment);
		//print_r($result->appointment);

		
		
	}catch (SoapFault $soapfault) {
	
		//echo $soapfault->getMessage();
		echo $soapfault->getTraceAsString();
	}catch (Exception $e) {
	
		echo $e->getMessage();
	
	

	}


}else if ($action=='cancelAppointment'){
	header('Content-Type: text/xml');

	if (isset($_POST['id'])){
			$requestingUser = $_POST['requestingUser']; 
			$id = $_POST['id'];
			
			$requestType = $_POST['requestType'];
		}else{
			$action = "";
		}
		
		try {
			$params = array( 'requestingUser' => $requestingUser, 'id' => $id );
			
			$client=new SoapClient($wsdl,array('location'=>$location));
			
			if($requestType == "User"){
				$result = $client->cancelUserAppointment($params);
			}else if($requestType == "Mentor"){
				$result = $client->cancelMentorAppointment($params);
			}else if($requestType == "Host"){
				$result = $client->cancelHostAppointment($params);
			}
			
			$xml = new array2xml('results');
			
			$xml->createNode( $result );
			
			echo $xml;
	
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}		

	
}else if ($action=='cancelAllAppointments'){
	header('Content-Type: text/xml');

	if (isset($_POST['affiliationId'])){
			$requestingUser = $_POST['requestingUser'];
			$affiliationId = $_POST['affiliationId'];
			
			$requestType = $_POST['requestType'];
		}else{
			$action = "";
		}
		
		try {
			$params = array( 'requestingUser' => $requestingUser, 'affiliationId' => $affiliationId );
			
			$client=new SoapClient($wsdl,array('location'=>$location));
			
			if($requestType == "User"){
				$result = $client->cancelAllUserAppointments($params);
			}else if($requestType == "Mentor"){
				$result = $client->cancelAllMentorAppointments($params);
			}else if($requestType == "Host"){
				$result = $client->cancelAllHostAppointments($params);
			}
			
			$xml = new array2xml('results');
			
			$xml->createNode( $result );
			
			echo $xml;
	
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}		

	
}else if ($action=='extendAppointment'){
	header('Content-Type: text/xml');

	if (isset($_POST['requestingUser'])){
			$requestingUser = $_POST['requestingUser']; 
			$username = $_POST['username'];
			$id = $_POST['id'];
			$minutes = $_POST['minutes']; 
			//$newstart = $_POST['newstart']; 
			//$newend = $_POST['newend']; 
			//$resourceType = $_POST['resourceType']; 
			//$course = $_POST['course']; 
			//$orderCode = $_POST['orderCode'];
			
			$requestType = $_POST['requestType']; 
		}else{
			$action = "";
		}
		
		try {
		/*
			$params = array( 'orderCode' => $orderCode,
							 'userName' => $username,
							 'start' => $start,
							 'end' => $end,
							 'newStart' => $newstart,
							 'newEnd' => $newend,
							 'resourceType' => $resourceType,
							 'course' => $course );
		*/
			$params = array('requestingUser' => $requestingUser,
							'id' => $id,
							'minutes' => $minutes );
				

			$client=new SoapClient($wsdl,array('location'=>$location));
			
			if($requestType == "User"){
				$result = $client->extendUserAppointment($params);
			}else if($requestType == "Mentor"){
				$result = $client->extendMentorAppointment($params);
			}else if($requestType == "Host"){
				$result = $client->extendHostAppointment($params);
			}
			
			$xml = new array2xml('results');
			
			$xml->createNode( $result );
			
			echo $xml;
			
			//echo $result->success;
	
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}
		
}else if ($action=='modifyAppointment'){
	header('Content-Type: text/xml');

	if (isset($_POST['requestingUser'])){
			$requestingUser = $_POST['requestingUser']; 
			$username = $_POST['username'];
			$id = $_POST['id'];
			$start = $_POST['start']; 
			$end = $_POST['end']; 
			//$newstart = $_POST['newstart']; 
			//$newend = $_POST['newend']; 
			//$resourceType = $_POST['resourceType']; 
			//$course = $_POST['course']; 
			//$orderCode = $_POST['orderCode'];
			
			$requestType = $_POST['requestType']; 
		}else{
			$action = "";
		}
		
		try {
		/*
			$params = array( 'orderCode' => $orderCode,
							 'userName' => $username,
							 'start' => $start,
							 'end' => $end,
							 'newStart' => $newstart,
							 'newEnd' => $newend,
							 'resourceType' => $resourceType,
							 'course' => $course );
		*/
			$params = array('requestingUser' => $requestingUser,
							'id' => $id,
							'start' => $start,
							'end' => $end );
				

			$client=new SoapClient($wsdl,array('location'=>$location));
			
			if($requestType == "User"){
				$result = $client->modifyUserAppointment($params);
			}else if($requestType == "Mentor"){
				$result = $client->modifyMentorAppointment($params);
			}else if($requestType == "Host"){
				$result = $client->modifyHostAppointment($params);
			}
			
			$xml = new array2xml('results');
			
			$xml->createNode( $result );
			
			echo $xml;
			
			//echo $result->success;
	
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}
		
}else if ($action=='getCourses'){		// Internal Function Call
	header('Content-Type: text/xml');


	if (isset($_POST['username'])){
			$username = $_POST['username'];   
		}else{
			$action = "";
		}

		try {
			
			$result = getAvailCourses($username);
	
			$xml = new array2xml('courses');
			
			$xml->createNode( $result );
			
			echo $xml;
		
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}
	

}else if ($action=='GetAvailableTimeZoneIds'){
	header('Content-Type: text/xml');


	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		$username = $_POST['username'];   
	}else{
		$action = "";
	}

	try {
		$params = array( 'requestingUser' => $requestingUser, 'userName' => $username );
		
		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->GetAvailableTimeZoneIds();

		$xml = new array2xml('zones');
		
		$xml->createNode( $result );
		
		echo $xml;
	
	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}

}else if ($action=='GetUserDefaultTimeZoneId'){
	header('Content-Type: text/xml');


	if (isset($_POST['requestingUser'])){
			$requestingUser = $_POST['requestingUser']; 
			$username = $_POST['username'];   
		}else{
			$action = "";
		}

		try {
			$params = array( 'requestingUser' => $requestingUser, 'userName' => $username );
			
			$client=new SoapClient($wsdl,array('location'=>$location));
			
			$result = $client->GetUserDefaultTimeZoneId($params);
	
			$xml = new array2xml('zone');
			
			$xml->createNode( $result );
			
			echo $xml;
		
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}

}else if ($action=='SetUserDefaultTimeZoneId'){
	header('Content-Type: text/xml');


	if (isset($_POST['requestingUser'])){
			$requestingUser = $_POST['requestingUser']; 
			$username = $_POST['username'];   
			$timeZoneId = $_POST['timezone'];
		
			$user = get_record('user','username',$username);
			
			
	
	/*
			$user = profile_user_record($USER->id);
			if (!empty($user->zone)) {
				$timezone = $user->zone;
			}else{
				$timezone = "None";
			}
			
			echo "timezone: ".$timezone."<br/>";
			
			$user->state = "FL";
			$user->companyName = "None";
			$user->website = "None";
			$user->zone = "GMT-05:00 America/Nassau";
			
			//print_r($user);
			
			$theuser = clone($USER);
		
			update_profile_fields($theuser, $user);
	*/

		try {
		
			$params = array( 'requestingUser' => $requestingUser,
							 'userName' => $username,
							 'timeZoneId' => $timeZoneId );
			
			$client=new SoapClient($wsdl,array('location'=>$location));
			
			$result = $client->SetUserDefaultTimeZoneId($params);
			
			setUserTimeZone($user->id, $timeZoneId);	// Set after webservice is called
			
			$xml = new array2xml('results');
			
			$xml->createNode( $result );
			
			echo $xml;
		
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}
		
	}else{
		$action = "";
	}
	
}else if ($action=='getUserFilterOptions'){
	
	header('Content-Type: text/x-json');
		


	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		$username = $_POST['username'];
		
		$user = get_record('user','username',$username);
		
		$filter = getUserFilterOptions($user->id);
		
		echo json_encode($filter);
		
	}else{
		$action = "";
	}		
	

}else if ($action=='setUserFilterOptions'){

	header('Content-Type: text/x-json');


	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		$username = $_POST['username'];
		$filter = $_POST['filter'];
		
		$user = get_record('user','username',$username);
		
		setUserFilterOptions($user->id, $filter);
		
		
	}else{
		$action = "";
	}

}else if ($action=='getUserView'){
	
	header('Content-Type: text/xml');
		


	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		$username = $_POST['username'];
		
		$user = get_record('user','username',$username);
		
		$view = getUserView($user->id);
		
		$xml = new array2xml('view');
		$xml->createNode( $view );
		echo $xml;
		
		
	}else{
		$action = "";
	}		
	

}else if ($action=='setUserView'){

	header('Content-Type: text');


	if (isset($_POST['requestingUser'])){
		$requestingUser = $_POST['requestingUser']; 
		$username = $_POST['username'];
		$view = $_POST['view'];
		
		$user = get_record('user','username',$username);
		
		setUserView($user->id, $view);
		
		
	}else{
		$action = "";
	}


}else if ($action=='isValidAminUser'){
	header('Content-Type: text/xml');


	if (isset($_POST['username'])){
			$username = $_POST['username']; 
		}else{
			$action = "";
		}
	
		try {
	
			$result = isValidAminUser($username);
			
			
			$xml = new array2xml('results');
			
			$xml->createNode( $result );
			
			echo $xml;
		
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}

}else if ($action=='isValidMentorUser'){
	header('Content-Type: text/xml');


	if (isset($_POST['username'])){
			$username = $_POST['username']; 
		}else{
			$action = "";
		}
	
		try {
	
			$result = isValidMentorUser($username);
			
			
			$xml = new array2xml('results');
			
			$xml->createNode( $result );
			
			echo $xml;
		
		} catch (Exception $e) {
		
			echo $e->getMessage();
		
		}catch (SoapFault $soapfault) {
		
			echo $soapfault->getMessage();
		}

}else if ($action=='emailUserAppointmentInfo'){
	header('Content-Type: text/x-json');
	
	$success = false;

	if (isset($_POST['username'])){
		$emailtype = $_POST['emailtype']; 
		$resource = $_POST['resource']; 
		$course = $_POST['course']; 
		$username = $_POST['username']; 
		//$appointments = $_POST['appointments'];
		//$oldappointments = $_POST['oldappointments']; 
		
		$appointments = json_decode(str_replace("\\", "", $_POST['appointments']));
		$oldappointments = json_decode(str_replace("\\", "", $_POST['appointments']));

	}else{
		$action = "";
	}

	$user = get_record('user','username',$username);
	
	if($emailtype == "confirm"){
		$title = "Appointment Confirmation";
		$message = "An email was sent to you with your confirmed appointment information.";
		appointment_confirmation_email($user, $course, $resource, $appointments);
		$success = true;
	}else if($emailtype == "modify"){
		$title = "Appointment Modification";
		$message = "An email was sent to you with your modified appointment information.";
		appointment_modification_email($user, $oldappointments, $appointments);
		$success = true;
	}else if($emailtype == "cancel"){
		$title = "Appointment Cancellation";
		$message = "An email was sent to you with your cancelled appointment information.";
		appointment_cancellation_email($user, $appointments);
		$success = true;
	}
	
	echo '{"success": '.$success.', "title": "'.$title.'", "message": "'.$message.'"}';
	/*
	$json = '{"foo-bar": 12345}';

	$obj = json_decode($json);
	print $obj->{'foo-bar'}; // 12345
	*/
}
/*else if ($action=='getBpp'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['userid'])){
		$userid = $_POST['userid'];
		
		$bpp = getBpp($userid);
		echo json_encode($bpp);
	}else{
		$action = "";
	}
}else if ($action=='setBpp'){
	header('Content-Type: text');

	if (isset($_POST['userid'])){
		$userid = $_POST['userid'];
		$bpp = $_POST['bpp'];
		
		setBpp($userid,$bpp);
	}else{
		$action = "";
	}
}else if ($action=='getResolution'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['userid'])){
		$userid = $_POST['userid'];
		
		$resolution = getResolution($userid);
		echo json_encode($resolution);
	}else{
		$action = "";
	}
}else if ($action=='setResolution'){
	header('Content-Type: text');

	if (isset($_POST['userid'])){
		$userid = $_POST['userid'];
		$resolution = $_POST['resolution'];
		
		setResolution($userid, $resolution);
	}else{
		$action = "";
	}
}

*/
//************************************************************************************************

function addVLCourse($course){						//**** addCourse *****
	
	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	$result = false;
	
	try {
	
		$coursenew = array('id' => $course->id,
						'shortname' => $course->shortname,
						'fullname' => $course->fullname,
						'promoCode' => $course->promocode );
						
		//$params = array('course' => $coursenew);

		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->addCourse($coursenew);

		$result = true;
   
    } catch (Exception $e) {
    	$result = false;
    } catch (SoapFault $soapfault) {
    	$result = false;
    }

	return $result;
}

function modifyVLCourse($course){						//**** modifyCourse *****

	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	$result = false;
	
	try {
						
		$coursenew = array('id' => $course->id,
						'shortname' => $course->shortname,
						'fullname' => $course->fullname,
						'promoCode' => $course->promocode );
						
		//$params = array('course' => $coursenew);
						
		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->modifyCourse($coursenew);

		$result = true;
   
    } catch (Exception $e) {
    	$result = false;
    } catch (SoapFault $soapfault) {
    	$result = false;
    }

	return $result;
}

function deleteVLCourse($course){					//**** deleteCourse *****

	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	$result = false;
	
	try {
		
		//$params = array('id' => $course->id);

		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->deleteCourse($course->id);

		$result = true;
   
    } catch (Exception $e) {
    	$result = false;
    } catch (SoapFault $soapfault) {
    	$result = false;
    }

	return $result;

}


function setWSUserDefaultTimeZone($requestingUser, $usernew){

	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";

	$zone = get_record('user_info_data','userid',$usernew->id,'fieldid',4);
	if (!empty($zone->data)) {
		$timezone = $zone->data;
	}else{
		$timezone = "";
	}

	try {
		
		$params = array( 'requestingUser' => $requestingUser,
						 'userName' => $usernew->username,
						 'timeZoneId' => $timezone );
		
		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->SetUserDefaultTimeZoneId($params);
		
		//$success = $result->success;
	
	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}
	
	
}


function update_profile_fields($user, $data){
	
	profile_load_data($user);
	
	$user->profile_field_state = $data->state;
	$user->profile_field_companyName = $data->companyName;
	$user->profile_field_website = $data->website;
	$user->profile_field_zone = $data->zone;
	
	//echo "<br/>-".$data->zone."-";
	
	profile_save_data($user);	
	
}

function isValidAminUser($username){

	$user = get_record('user','username',$username);
	
	$userRole = get_record('role_assignments', 'userid', $user->id, 'contextid', 1);
	$role = get_record('role', 'id', $userRole->roleid);
	
	//echo "userRole: ".$userRole->roleid."<br/>";
	//echo "Role: ".$role->name." - ".$role->shortname."<br/>";
	
	if($role->shortname == "admin"){
		$result = true;
	}else{
		$result = false;
	}
	//return isadmin($user->id);
	
	return array($result);
}

function isValidMentorUser($username){

	$user = get_record('user','username',$username);
	
	$userRole = get_record('role_assignments', 'userid', $user->id, 'contextid', 1);
	$role = get_record('role', 'id', $userRole->roleid);
	
	//echo "userRole: ".$userRole->roleid."<br/>";
	//echo "Role: ".$role->name." - ".$role->shortname."<br/>";
	
	if($role->shortname == "mentor"){
		$result = true;
	}else{
		$result = false;
	}
	//return isadmin($user->id);
	
	return array($result);
}

// --- Called from user/editadvanced.php - line: 105
// --- Called from login/confirm.php - line: 53 - commentted out
// --- Called from admin/user.php - line: 53 - commented out
function createUserProfile($requestingUser, $usernew, $isSignup){
	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	$success = false;
	// Get the user timezone information
	$user = profile_user_record($usernew->id);
	//$zone = getUserTimeZone($userold->id);
	/*
	$zone = get_record('user_info_data','userid',$userold->id,'fieldid',4);
	if (!empty($zone->data)) {
		$timezone = $zone->data;
	}else{
		//$timezone = "GMT-05:00 America/New_York";
		$timezone = "";
	}*/
	
	//$timezone = $usernew->profile_field_zone->inputname;
	
	//$newfield = 'profile_field_zone';
	//$formfield = new $newfield(4, $usernew->id);
	//$formfield->
	
	/*
	//profile_load_data($theuser);
	$myuser = new object();
	//profile_load_data($myuser);
	$myuser = profile_user_record($usernew->id);
	if (!empty($myuser->zone)) {
		$timezone = $myuser->zone;
	}*/

	
	//Get the user role
	$userRole = get_record('role_assignments', 'userid', $userold->id, 'contextid', 1);
	$role = get_record('role', 'id', $userRole->roleid);
	
	if (!empty($role->name)) {
		$newrole = $role->name;
	}
	
	// Admin account screation password param is newpassword and student signup is password.
	if($isSignup){
		$password = $usernew->password;
	}else{
		$password = $usernew->newpassword;
	}
	
	echo "<br/>\$password: $password";
	$encryptedPassword = Crypt::encrypt($password);
	echo "<br/>\$encryptedPassword: $encryptedPassword";
	
	try {
			
		$params = array('requestingUser' => $requestingUser,
						'userName' => $usernew->username,
						// 'password' => $password,		// password is already hashed.
						'encryptedPassword' => $encryptedPassword,		
						'firstName' => $usernew->firstname,
						'lastName' => $usernew->lastname,
						'emailAddress' => $usernew->email,
						'userRole' => $newrole );
						//'timeZone' => $timezone );
		
		 $client = new SoapClient($wsdl,array('location'=>$location));
		 $result = $client->createUserProfileWithEncryptedPassword($params);
		
		
		
		$success = $result->success;
		
		
	}catch (Exception $e) {
		 //echo $e->getMessage();
		 $success = false;

	}catch (SoapFault $soapfault) {
		 //echo $soapfault->getMessage();
		 $success = false;
	}
	
	return $success;
}


// --- Called from user/editadvanced.php - line: 125
// --- Called from user/edit.php - line: 192
// --- Called from login/change_password.php - line: 
function editUserProfile($requestingUser, $userold){
	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";

	// Get the user timezone information
	$user = profile_user_record($userold->id);
	//$zone = getUserTimeZone($userold->id);
	$zone = get_record('user_info_data','userid',$userold->id,'fieldid',4);
	if (!empty($zone->data)) {
		$timezone = $zone->data;
	}else{
		//$timezone = "GMT-05:00 America/New_York";
		$timezone = "";
	}
	
	//Hash the password before saving
	//$usernew->password = hash_internal_user_password($userold->newpassword);
	
	//Get the user role
	$userRole = get_record('role_assignments', 'userid', $userold->id, 'contextid', 1);
	$role = get_record('role', 'id', $userRole->roleid);
	
	if (!empty($role->name)) {
		$newrole = $role->name;
	}else{
		$newrole = "Student";
	}

	require_once($CFG->libdir."/crypt.php");
	echo "<br/>\$userold->newpassword: $userold->newpassword";
	$encryptedPassword = Crypt::encrypt($userold->newpassword);
	echo "<br/>\$encryptedPassword: $encryptedPassword";
	
	try {
		
		$params = array('requestingUser' => $requestingUser,
						'userName' => $userold->username,
						// 'password'=>  $userold->newpassword,
						'encryptedPassword'=>  $encryptedPassword,
						'firstName' => $userold->firstname,
						'lastName' => $userold->lastname,
						'emailAddress' => $userold->email,
						'userRole' => $newrole,
						'timeZone' => $timezone,
						'contactInfo' => $userold->phone1 );
		
		$client = new SoapClient($wsdl,array('location'=>$location));
		$result = $client->editUserProfileWithEncryptedPassword($params);
	
		
		$success = $result->success;

	}catch (Exception $e) {
		 echo $e->getMessage();
		 $success = false;

	}catch (SoapFault $soapfault) {
		 echo $soapfault->getMessage();
		 $success = false;
	}
	
	return $success;
}

// --- Called from login/change_password.php - line: 72
function editUserProfilePassword($requestingUser, $username, $password){
	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	$user = get_record('user','username',$username);
	$zone = get_record('user_info_data','userid',$user->id,'fieldid',4);
	if (!empty($zone->data)) {
		$timezone = $zone->data;
	}else{
		//$timezone = "GMT-05:00 America/New_York";
		$timezone = "";
	}
	
	$userRole = get_record('role_assignments', 'userid', $user->id, 'contextid', 1);
	$role = get_record('role', 'id', $userRole->roleid);
	
	if (!empty($role->name)) {
		$newrole = $role->name;
	}else{
		$newrole = "Student";
	}

	require_once($CFG->libdir."/crypt.php");
	echo "<br/>\$password: $password";
	$encryptedPassword = Crypt::encrypt($password);
	echo "<br/>\$encryptedPassword: $encryptedPassword";
	
	try {
						
		$params = array('requestingUser' => $requestingUser,
						'userName' => $username,
						'encryptedPassword' => $encryptedPassword,	
						'firstName' => $user->firstname,
						'lastName' => $user->lastname,
						'emailAddress' => $user->email,
						'userRole' => $newrole,
						'timeZone' => $timezone,
						'contactInfo' => $userold->phone1 );
		
		$client = new SoapClient($wsdl,array('location'=>$location));
		$result = $client->editUserProfileWithEncryptedPassword($params);
	
		
		$success = $result->success;

	}catch (Exception $e) {
		 echo $e->getMessage();
		 $success = false;

	}catch (SoapFault $soapfault) {
		 echo $soapfault->getMessage();
		 $success = false;
	}
	
	return $success;
}

// --- Called from admin/roles/assign.php - line: 72
function editUserProfileRole($requestingUser, $userid, $roleid,$addingRole){
	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	$isAdmin = false;
	$isMentor = false;
	//$username = get_field('user', 'username', 'id', $userid);
	
	$user = get_record('user','id',$userid);
	$zone = get_record('user_info_data','userid',$user->id,'fieldid',4);
	if (!empty($zone->data)) {
		$timezone = $zone->data;
	}else{
		//$timezone = "GMT-05:00 America/New_York";
		$timezone = "";
	}
	
	//$role = get_record('role', 'id', $roleid);
	//$rolename = get_field('role', 'name', 'id', $roleid);
	
	$records = get_records('role_assignments', 'userid', $userid);
	foreach ($records as $record) {
		$role = get_record('role', 'id', $record->roleid);
		if($role->shortname == "admin"){
			$isAdmin = true;
		}else if($role->shortname == "mentor"){
			$isMentor = true;
		}
	}
	
	if ($isAdmin) {
		$newrole = "Admin";
	}else if ($isMentor) {
		$newrole = "Mentor";
	}else{
		$newrole = "Student";
	}

	try {
						
		$params = array('requestingUser' => $requestingUser,
						'userName' => $user->username,	
						'firstName' => $user->firstname,
						'lastName' => $user->lastname,
						'emailAddress' => $user->email,
						'userRole' => $newrole,
						'timeZone' => $timezone,
						'contactInfo' => $userold->phone1 );
		
		$client = new SoapClient($wsdl,array('location'=>$location));
		$result = $client->editUserProfile($params);
	
		
		$success = $result->success;

	}catch (Exception $e) {
		 echo $e->getMessage();
		 $success = false;

	}catch (SoapFault $soapfault) {
		 echo $soapfault->getMessage();
		 $success = false;
	}
	
	return $success;
}

// --- Called from admin/user.php - line: 82
function deleteUserProfile($requestingUser, $userold){
	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";

	try {
		
		$params = array('requestingUser' => $requestingUser,
						'userName' => $userold->username );
		
		$client = new SoapClient($wsdl,array('location'=>$location));
		$result = $client->delUserProfile($params);
	
		
		//$success = $result->success;
		$success = true;
		

	}catch (Exception $e) {
		 echo $e->getMessage();
		 $success = false;

	}catch (SoapFault $soapfault) {
		 echo $soapfault->getMessage();
		 $success = false;
	}
	
	return $success;
}

// --- Called from course/enrol.php - line: 92
// --- Called from course/unenrol.php - line: 66, 77
function enrollUserInCourse($requestingUser, $username, $courseName, $enrolled){
	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";

	try {
		
		$params = array('requestingUser' => $requestingUser,
						'userName' => $username,
						'courseName' => $courseName,
						'flag' => $enrolled );
		
		$client = new SoapClient($wsdl,array('location'=>$location));
		$result = $client->enrollUserInCourse($params);
	
		$success = $result->success;		

	}catch (Exception $e) {
		 echo $e->getMessage();
		 $success = false;

	}catch (SoapFault $soapfault) {
		 echo $soapfault->getMessage();
		 $success = false;
	}
	
	return $success;

}

function getAvailCourses($username){
/*
// context definitions
define('CONTEXT_SYSTEM', 10);
define('CONTEXT_PERSONAL', 20);
define('CONTEXT_USER', 30);
define('CONTEXT_COURSECAT', 40);
define('CONTEXT_COURSE', 50);
define('CONTEXT_GROUP', 60);
define('CONTEXT_MODULE', 70);
define('CONTEXT_BLOCK', 80);
*/

	$stack = array();
	// Modified by SMS: 8/7/2011
	// To support ALL_STUDENTS as username
	if ($username == 'ALL_STUDENTS') {
		$courses = get_records('course');
		foreach ($courses as $course) {
			if($course->id > 1){
				array_push($stack, $course->fullname);	
			}
		}
	} else {
		$user = get_record('user','username',$username);
		//$courses = get_my_courses($user->id, 'visible DESC,sortorder ASC', '*', false, 0);
		
		$roles = get_records('role_assignments','userid',$user->id,'timemodified ASC');
		
		foreach ($roles as $role)
		{
			$context = get_record('context','id',$role->contextid,'contextlevel',50);
			$course =  get_record('course','id',$context->instanceid);
			if($course->id > 1){
				array_push($stack, $course->fullname);	
			}
		}
	}
	/*
	$user = get_record('user','username',$username);
	//$courses = get_my_courses($user->id, 'visible DESC,sortorder ASC', '*', false, 0);
	
	$roles = get_records('role_assignments','userid',$user->id,'timemodified ASC');
	
	foreach ($roles as $role)
	{
		$context = get_record('context','id',$role->contextid,'contextlevel',50);
		$course =  get_record('course','id',$context->instanceid);
		if($course->id > 1){
			array_push($stack, $course->fullname);	
		}
	}
	*/
	// SMS
	
	/*
	foreach ($courses as $course)
	{
		array_push($stack, $course->fullname);		
	}*/
	
	//$course_str = implode(",", $stack);
	
	return $stack;
}

function isEnrolledInCourse($username, $courseId){

	$isEnrolled = false;

	$user = get_record('user','username',$username);
	
	$roles = get_records('role_assignments','userid',$user->id,'timemodified ASC');
	
	foreach ($roles as $role)
	{
		$context = get_record('context','id',$role->contextid,'contextlevel',50);
		$course =  get_record('course','id',$context->instanceid);
		if($course->id == $courseId){
			$isEnrolled = true;
		}
	}
	
	return $isEnrolled;
}

function enrollUsersAvailCourses($requestingUser, $username){

	$user = get_record('user','username',$username);
	
	$roles = get_records('role_assignments','userid',$user->id,'timemodified ASC');
	
	foreach ($roles as $role)
	{
		$context = get_record('context','id',$role->contextid,'contextlevel',50);
		$course =  get_record('course','id',$context->instanceid);
		if($course->id > 1){
			enrollUserInCourse($requestingUser, $username, $course->fullname, true);
		}
	}
}

function getAppointmentXML(){
/*
	$array = array(
		array('monkey', 'banana', 'Jim'),
		array('hamster', 'apples', 'Kola'),
		array('turtle', 'beans', 'Berty'),
	);
	$xml = new XmlWriter();
	$xml->push('zoo');
	foreach ($array as $animal) {
		$xml->push('animal', array('species' => $animal[0]));
		$xml->element('name', $animal[2]);
		$xml->element('food', $animal[1]);
		$xml->pop();
	}
	$xml->pop();

	return $xml->getXml();
*/

}




function object_2_array($result)

{

    $array = array();

    foreach ($result as $key=>$value)

    {

        if (is_object($value))

        {

            $array[$key]=object_2_array($value);

        }

        elseif (is_array($value))

        {

            $array[$key]=object_2_array($value);

        }

        else

        {

            $array[$key]=$value;

        }

    }

    return $array;

} 

function getUserTimeZone($userId){
   	try {  		
   		$sql = "SELECT data FROM mdl_user_info_data WHERE userid = ".$userId." and fieldid = 4";
	    $zone = get_record_sql($sql);
        return $zone;
    } catch (Exception $e) {
        echo $e->getMessage();
    }	
}

function setUserTimeZone($userId, $timeZoneId){
   	try {
   		
   		$sql = "UPDATE mdl_user_info_data SET data ='".$timeZoneId."' WHERE userid = ".$userId." and fieldid = 4";
	    execute_sql($sql,false);
	    
    } catch (Exception $e) {
        echo $e->getMessage();
    }	
}

function getUserFilterOptions($userId){
   	try {  		
   		$sql = "SELECT data FROM mdl_user_info_data WHERE userid = ".$userId." and fieldid = 10";
	    $filter = get_record_sql($sql);
        return $filter;
    } catch (Exception $e) {
        echo $e->getMessage();
    }	
}

function setUserFilterOptions($userId, $options){
   	try {
   		
   		
	    //echo execute_sql($sql,false);
		if(record_exists('user_info_data', 'userid', $userId, 'fieldid', 10)){
		//if(!count_records_sql(sql)){
			$sql = "UPDATE mdl_user_info_data SET data ='".$options."' WHERE userid = ".$userId." and fieldid = 10";
			execute_sql($sql,false);
			//echo $sql;
		}else{
			$sql = "INSERT INTO mdl_user_info_data (userid, fieldid, data) VALUES (".$userId.", 10, '".$options."')";
			execute_sql($sql,false);
			//echo $sql;
		}
	    
    } catch (Exception $e) {
        echo $e->getMessage();
    }	
}

function getUserView($userId){
   	try {  		
   		$sql = "SELECT data FROM mdl_user_info_data WHERE userid = ".$userId." and fieldid = 12";
	    $filter = get_record_sql($sql);
        return $filter;
    } catch (Exception $e) {
        echo $e->getMessage();
    }	
}

function setUserView($userId, $view){
   	try {
   		
   		
	    //echo execute_sql($sql,false);
		if(record_exists('user_info_data', 'userid', $userId, 'fieldid', 12)){
		//if(!count_records_sql(sql)){
			$sql = "UPDATE mdl_user_info_data SET data ='".$view."' WHERE userid = ".$userId." and fieldid = 12";
			execute_sql($sql,false);
			//echo $sql;
		}else{
			$sql = "INSERT INTO mdl_user_info_data (userid, fieldid, data) VALUES (".$userId.", 12, '".$view."')";
			execute_sql($sql,false);
			//echo $sql;
		}
	    
    } catch (Exception $e) {
        echo $e->getMessage();
    }	
}

function isValidUsername($username){
	$valid = false;
	
	if(!preg_match('/[\/\\ \[\]:;\|=,\+\*\?<>"@&!]/', $username)){
	//	\"/\\[]:;|=,+*?<> 
		//echo "valid username";
		if(strlen($username) >0 && strlen($username) <= 20){
			$valid = true;
		}
	}
	return $valid;
}

function isValidPassword($password){
	$valid = false;
	
	if(!preg_match('/[\/\\ \[\]:;\|=,\+\?<>"@&!]/', $password)){
		if(strlen($password) > 0 && strlen($password) <= 10) {
			$valid = true;
		}
	}
	return $valid;
}

function removeSpecialChars($password) {
	$valid = false;
	
	$password = preg_replace('/[\/\\ \[\]:;\|=,\+\*\?<>"@&!]/', '', $password);

	return $password;
}

/*
 function getBpp($userId){
    try {  		
   	$sql = "SELECT data FROM mdl_user_info_data WHERE userid = ".$userId." and fieldid = 19";
	$bpp = get_record_sql($sql);
    } catch (Exception $e) {
        //echo $e->getMessage();
	$bpp = null;
    }
    return $bpp;
}
function getBppValue($userId){
    $bpp =  get_field('user_info_data','data','userid',$userId,'fieldid',19);
    return $bpp;
}

function setBpp($userId, $bpp){
   	try {
		if(record_exists('user_info_data', 'userid', $userId, 'fieldid', 19)){
			$sql = "UPDATE mdl_user_info_data SET data ='".$bpp."' WHERE userid = ".$userId." and fieldid = 19";
			execute_sql($sql,false);
		}else{
			$sql = "INSERT INTO mdl_user_info_data (userid, fieldid, data) VALUES (".$userId.", 19, '".$bpp."')";
			execute_sql($sql,false);
		}
		echo "changed";
    } catch (Exception $e) {
        echo $e->getMessage();
    }	
}

function getResolution($userId){
    try {  		
   	$sql = "SELECT data FROM mdl_user_info_data WHERE userid = ".$userId." and fieldid = 20";
	$resolution = get_record_sql($sql);
    } catch (Exception $e) {
        //echo $e->getMessage();
	$resolution = null;
    }
    return $resolution;
}
function getResolutionValue($userId){
    $resolution =  get_field('user_info_data','data','userid',$userId,'fieldid',20);
    return $resolution;
}
function setResolution($userId, $resolution){
   	try {
		if(record_exists('user_info_data', 'userid', $userId, 'fieldid', 20)){
			$sql = "UPDATE mdl_user_info_data SET data ='".$resolution."' WHERE userid = ".$userId." and fieldid = 20";
			execute_sql($sql,false);
		}else{
			$sql = "INSERT INTO mdl_user_info_data (userid, fieldid, data) VALUES (".$userId.", 20, '".$resolution."')";
			execute_sql($sql,false);
		}
		echo "changed";
    } catch (Exception $e) {
        echo $e->getMessage();
    }	
}

*/
/*
// 29.06.2011 - jam
function cancelCertAppointment($requestingUser, $id){
	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location ="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";

	
	try {
		$params = array( 'requestingUser' => $requestingUser, 'id' => $id );
		
		$client=new SoapClient($wsdl,array('location'=>$location));
		
		$result = $client->cancelUserAppointment($params);
		
		//$xml = new array2xml('results');
		//$xml->createNode( $result );
		//echo $xml;

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}	
}
*/

?>


