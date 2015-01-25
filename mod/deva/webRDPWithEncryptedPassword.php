<?php
/**
* Decrypt data from a CryptoJS json encoding string
*
* @param mixed $passphrase
* @param mixed $jsonString
* @return mixed
*/
/*
# --- Decrypting the login and password ---

# the key should be random binary, use scrypt, bcrypt or PBKDF2 to
# convert a string into a key
# key is specified using hexadecimal
# the key is: "Seyed Masoud Sadjadi @ IT Scholars"
$key = pack('H*', "5365796564204d61736f7564205361646a6164692040204954205363686f6c617273");
    
# show key size use either 16, 24 or 32 byte keys for AES-128, 192
# and 256 respectively
$key_size =  strlen($key);
    
# create a random IV to use with CBC encoding
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    
// $encrypted_login_base64 = $_COOKIE[encrypted_login_4_moodle];
$plaintext_login = $_COOKIE[plaintext_login_4_moodle];
$encrypted_password_base64 = $_COOKIE[encrypted_password_4_moodle];

// $encrypted_login_dec = base64_decode($encrypted_login_base64);
$encrypted_password_dec = base64_decode($encrypted_password_base64);
    
# retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
// $iv_login_dec = substr($encrypted_login_dec, 0, $iv_size);
$iv_password_dec = substr($encrypted_password_dec, 0, $iv_size);
    
# retrieves the encrypted text (everything except the $iv_size in the front)
// $encrypted_login_dec = substr($encrypted_login_dec, $iv_size);
$encrypted_password_dec = substr($encrypted_password_dec, $iv_size);

# may remove 00h valued characters from end of plain text
// $plaintext_login_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted_login_dec, MCRYPT_MODE_CBC, $iv_login_dec);
$plaintext_password_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted_password_dec, MCRYPT_MODE_CBC, $iv_password_dec);
    
// $plaintext_login = trim($plaintext_login_dec, "\0");
$plaintext_password = trim ($plaintext_password_dec, "\0");

// echo  "<br/>The login is " . $plaintext_login . "\n";
// echo  "<br/>The password is " . $plaintext_password . "\n";
*/

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir . '/crypt.php');   
$decrypted_password = Crypt::decrypt($_COOKIE[encrypted_password_4_moodle]);
// echo "<br/>\$decrypted_password: $decrypted_password";

include 'GibberishAES.php';
$key = pack('H*', "5365796564204d61736f7564205361646a6164692040204954205363686f6c617273");
$key_size = 256;
GibberishAES::size($key_size);    // Also 192, 128
// $encrypted_login = GibberishAES::enc($plaintext_login, $key);
$encrypted_password = GibberishAES::enc($decrypted_password, $key);
// echo "<br/>\$encrypted_password: $encrypted_password";

?>

<html>
<head>
<title>webRDP</title>
<meta http-equiv='content-type' content='text/html; charset=iso-8859-1'>
<meta http-equiv='pragma' content='no-cache'>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="gibberish-aes.js"></script>
<script type="application/javascript">
	// This method is called after the user logs out of their RDP session. The method name is a configurable applet parameter.
	function rdpOnLogout() {
    	//alert ( ' User has Logged out ' );
	}

	function isActive(){
    	return document.applets[0].isActive();
	}

	function appletRunning(){
    	return document.getElementById('rdp').appletRunning();
	}

    $( document ).ready(function() {
        console.log( "document loaded" );
		
		var key = "<?php echo "$key"; ?>";
		var key_size = parseInt("<?php echo "$key_size"; ?>");
		// var encrypted_login = "< ?php echo "$encrypted_login"; ?>";
		var encrypted_password = "<?php echo "$encrypted_password"; ?>";
		GibberishAES.size(key_size);    // Also 192, 128
		// var plaintext_login = GibberishAES.dec(encrypted_login, key);
		// var plaintext_login = "<?php echo "$plaintext_login"; ?>";
		var plaintext_password = GibberishAES.dec(encrypted_password, key);
		
		// document.getElementById("usernameId").value = plaintext_login;
		document.getElementById("passwordId").value = plaintext_password;
    });
 
    $( window ).load(function() {
        console.log( "window loaded" );

		WaitForAppletLoad("rdp", 10, 2000, function () {
        	console.log( "rdp applet loaded" );

			// document.getElementById("usernameId").value = "";
			document.getElementById("passwordId").value = "";
    	}, function () {
      	  console.log( "rdp applet was not loaded after 10 attempts!" );
    	});
	});
	
	/* Attempt to load the applet up to "X" times with a delay. If it succeeds, then execute the callback function. */
	function WaitForAppletLoad(applet_id, attempts, delay, onSuccessCallback, onFailCallback) {
    	//Test
		console.log( "WaitForAppletLoad: attempts count down " + attempts );
		
    	var to = typeof (document.getElementById(applet_id));
    	if (to == 'function' || to == 'object') {
        	onSuccessCallback(); //Go do it.
        	return true;
    	} else {
        	if (attempts == 0) {
            	onFailCallback();
            	return false;
        	} else {
            	//Put it back in the hopper.
            	setTimeout(function () {
                	WaitForAppletLoad(applet_id, --attempts, delay, onSuccessCallback, onFailCallback);
            	}, delay);
        	}
    	}
	}
</script>

</head>

<body bgcolor="#DFEFFC">
<center>
<!-- <body bgcolor="#DFEFFC" onload='parent.selectTab("<?php echo $_GET["tab"]; ?>")' > -->
<!-- Loads the applet and utilizes 100% of browser window width and height.  Width and height could be hard coded to specific values -->
<applet name="rdp" id="rdp" code="com.webinflection.webrdp.MainApplet" archive="webRDP.jar" width="<?php echo $_GET["frameWidth"]; ?>" height="<?php echo $_GET["frameHeight"]; ?>">
<!-- Hostname or IP Address of Terminal Server -->
<!-- This is a required parameter -->
<param name="host" value="<?php echo $_GET["hostName"]; ?>">

<!-- Port that the Terminal Server -->
<!-- This is a required parameter -->
<param name="port" value="<?php echo $_GET["hostPort"]; ?>">

<!-- Username to authenticate to terminal server with -->
<!-- Optional SSO Parameter -->
<param id="usernameId" name="username" value="<?php echo $_GET["username"]; ?>">

<!-- Password to authenticate to terminal server with -->
<!-- Optional SSO Parameter -->
<param id="passwordId" name="password" value="">

<!-- AD Domain name to authenticate to terminal server with -->
<!-- Optional SSO Parameter -->
<param name="domain" value="<?php echo $_GET["domain"]; ?>">

<!-- Application to start. This value should be url encoded.  In this example we are launching c:\windows\system32\notepad.exe-->
<!-- param name="program" value="<?php echo $_GET["appName"]; ?>" -->

<!-- Working directory for Application.  This value should be url encoded.  In this example the working directory will be set to c:\windows\system32\ -->
<!-- param name="directory" value="<?php echo $_GET["appDir"]; ?>" -->

<!-- This specifies a javascript method to be called after the user logs out of the RDP session -->
<param name="onlogout" value="javascript:rdpOnLogout();">

<param name="pf" value="127">
<param name="clipboard" value="1">
<param name="bpp" value="<?php echo $_GET['frameBpp']; ?>">

</applet>
</center>

</body>
</html>
