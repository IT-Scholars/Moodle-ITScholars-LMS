<?php



require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/user/profile/lib.php');

require_once($CFG->libdir .'/ddllib.php');

ini_set("soap.wsdl_cache_enabled", "0");

header("Access-Control-Allow-Origin: *");

$wsdl="http://ita-provisioner.cis.fiu.edu:8100/axis2/services/VirtualLabs?wsdl";
$location="http://ita-provisioner.cis.fiu.edu:8100/axis2/services/VirtualLabs";

		
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

//echo $arr;


//*****************************************************************************************

if($action == 'getState'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['instanceId']) && isset($_POST['vmName'])){
		$instanceId = $_POST['instanceId']; 
		$vmName = $_POST['vmName'];
	
		echo instanceCmdRequest($instanceId, $vmName, 'getstate','');
	}
	
}else if ($action=='powerOff'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['instanceId']) && isset($_POST['vmName'])){
		$instanceId = $_POST['instanceId']; 
		$vmName = $_POST['vmName'];
		
		instanceCmdRequest($instanceId, $vmName, 'stop', 'hard');
	}
	
}else if ($action=='shutdown'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['instanceId']) && isset($_POST['vmName'])){
		$instanceId = $_POST['instanceId']; 
		$vmName = $_POST['vmName'];
		
		instanceCmdRequest($instanceId, $vmName, 'stop', 'soft');
	}
	
}else if ($action=='suspend'){
	header('Content-Type: text/x-json');	
	
	if (isset($_POST['instanceId']) && isset($_POST['vmName'])){
		$instanceId = $_POST['instanceId']; 
		$vmName = $_POST['vmName'];
	
		instanceCmdRequest($instanceId, $vmName, 'suspend', 'soft');
	}

}else if ($action=='powerOn'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['instanceId']) && isset($_POST['vmName'])){
		$instanceId = $_POST['instanceId']; 
		$vmName = $_POST['vmName'];
		
		instanceCmdRequest($instanceId, $vmName, 'start', 'soft');	
	}
	
}else if ($action=='restart'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['instanceId']) && isset($_POST['vmName'])){
		$instanceId = $_POST['instanceId']; 
		$vmName = $_POST['vmName'];
		
		instanceCmdRequest($instanceId, $vmName, 'reset', 'hard');	
	}
	
}else if ($action=='refresh'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['instanceId']) && isset($_POST['vmName'])){
		$instanceId = $_POST['instanceId']; 
		$vmName = $_POST['vmName'];
	
		refreshInstanceRequest($instanceId, $vmName);
	}
		
}else if ($action=='getAppointmentTimer'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['instanceId'])){
		$instanceId = $_POST['instanceId']; 
	
		getAppointmentTimer($instanceId);	
		
	}
	
}else if ($action=='isRDPReady'){
	header('Content-Type: text/x-json');
	// Edited: JAM 03.21.2012
	if (isset($_POST['hostName']) && isset($_POST['hostPort']) && isset($_POST['userid'])){
		$hostName = $_POST['hostName']; 
		$hostPort = $_POST['hostPort'];
		$userid = $_POST['userid'];
		$defaultHeight = $_POST['defaultHeight'];
		
		isRDPReady($hostName, $hostPort, $userid, $defaultHeight);	// Edited: JAM 03.21.2012
		
	}
}else if ($action=='getUserCurAppId'){
	header('Content-Type: text/x-json');
	
	if (isset($_POST['resourceType']) && isset($_POST['course'])){
		$username = $_POST['username']; 
		$course = $_POST['course'];
		$resourceType = $_POST['resourceType'];
	
		getUserCurAppId($username, $course, $resourceType);
		
	}
}else if ($action=='getBpp'){
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


//*****************************************************************************************

function instanceCmdRequest($devaInsId, $vmName, $cmd1, $cmd2){

	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	//$devaInsId = 'c9bfaa50-2036-4b93-a701-848a6671864f';
	//$vmName = 'xp-4';
	
	//$devaInsId = 'c9bfaa50-2036-4b93-a701-848a6671864f';
	//$vmName = 'Laptop 1 (laptop1)';
	
	try {
					
		$params = array('devaInsId' => $devaInsId,
						'vmName' => $vmName,
						'cmdParam1' => $cmd1,
						'cmdParam2' => $cmd2 ); 			

		$client=new SoapClient($wsdl,array('location'=>$location));

		$result = $client->RunVMCmd($params);
		

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}	
	return $result->returnValue;
}



function refreshInstanceRequest($devaInsId, $vmName){

	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	//$devaInsId = 'c9bfaa50-2036-4b93-a701-848a6671864f';
	//$vmName = 'Laptop 1 (laptop1)';
	
	try {

		$encryptedPassword = $_COOKIE["encrypted_password_4_moodle"];

		$params = array('devaInsId' => $devaInsId,
						'vmName' => $vmName,
						'encryptedPassword' => $encryptedPassword); 			

		$client=new SoapClient($wsdl,array('location'=>$location));

		$result = $client->refreshVMWithEncryptedPassword($params);
		

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}	
	return $result;
}


function getAppointmentTimer($devaInsId){

	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	//$devaInsId = 'c9bfaa50-2036-4b93-a701-848a6671864f';
	
	try {
					
		$params = array('devaInsId' => $devaInsId); 			

		$client=new SoapClient($wsdl,array('location'=>$location));

		$result = $client->getEndDate4CurrentDevaIns($params);
		

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}	
	
	echo json_encode($result);
	//return $result;
}

function isRDPReady($hostName, $hostPort, $userid, $defaultHeight){	// Edited: JAM 03.21.2012

	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	try{
					
		$params = array('hostName' => $hostName,
				'hostport' => $hostPort ); 			

		$client=new SoapClient($wsdl,array('location'=>$location));

		$result = $client->isRDPReady($params);
		$result->bpp = getBppValue($userid);
		$result->height = getResolutionHeight($userid,$defaultHeight);
		$result->width = getResolutionWidth($userid);

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}	
	
	echo json_encode($result);
	//return $result;
}

function getUserCurAppId($username, $course, $resourceType){

	$wsdl="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs?wsdl";
	$location="http://ita-provisioner.cis.fiu.edu:8080/axis2/services/VirtualLabs";
	
	try{
					
		$params = array('username' => $username,
						'course' => $course,
						'resourceType' => $resourceType ); 			

		$client=new SoapClient($wsdl,array('location'=>$location));

		$result = $client->getUserCurAppId($params);
		

	} catch (Exception $e) {
	
		echo $e->getMessage();
	
	}catch (SoapFault $soapfault) {
	
		echo $soapfault->getMessage();
	}	
	
	echo json_encode($result);
	//return $result;
}

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
    //return $bpp;
    $bppString = "16";
    if($bpp != false){
        $bppString = $bpp;
    }
    return $bppString;
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
function getResolutionHeight($userid,$defaultHeight){
    $resString = getResolutionValue($userid);
    $pos = strrpos($resString, "x");
    if ($pos === false) { // Not Found
        //echo 'width="100%" height="'.$defaultHeight.'%"';
	$height = $defaultHeight."%";
    }else{
        //$w = substr($resString, 0, $pos);
        $h = substr($resString, $pos+1);
        //echo 'width="'.$w.'px" height="'.$h.'px"';
	$height = $h."px";
    }
    return $height;
}
function getResolutionWidth($userid){
    $resString = getResolutionValue($userid);
    $pos = strrpos($resString, "x");
    if ($pos === false) { // Not Found
	$width = "100%";
    }else{
        $w = substr($resString, 0, $pos);
        $width = $w."px";
    }
    return $width;
}
//bottomFrameHeightPercentage

//*****************************************************************************************


?>