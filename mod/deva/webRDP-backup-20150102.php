<?php
/**
* Decrypt data from a CryptoJS json encoding string
*
* @param mixed $passphrase
* @param mixed $jsonString
* @return mixed
*/
function cryptoJsAesDecrypt($passphrase, $jsonString) {
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv  = hex2bin($jsondata["iv"]);
    $concatedPassphrase = $passphrase.$salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}

/**
* Encrypt value to a cryptojs compatiable json encoding string
*
* @param mixed $passphrase
* @param mixed $value
* @return string
*/
function cryptoJsAesEncrypt($passphrase, $value) {
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx.$passphrase.$salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32,16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}

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
    
$cipher_login_base64 = $_COOKIE[cipher_login_4_moodle];
$cipher_password_base64 = $_COOKIE[cipher_password_4_moodle];

// echo "cipher_login_base64 is $cipher_login_base64 and cipher_password_base64 is $cipher_password_base64.";
	
$cipher_login_dec = base64_decode($cipher_login_base64);
$cipher_password_dec = base64_decode($cipher_password_base64);
    
# retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
$iv_login_dec = substr($cipher_login_dec, 0, $iv_size);
$iv_password_dec = substr($cipher_password_dec, 0, $iv_size);
    
# retrieves the cipher text (everything except the $iv_size in the front)
$cipher_login_dec = substr($cipher_login_dec, $iv_size);
$cipher_password_dec = substr($cipher_password_dec, $iv_size);

# may remove 00h valued characters from end of plain text
$plain_login_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $cipher_login_dec, MCRYPT_MODE_CBC, $iv_login_dec);
$plain_password_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $cipher_password_dec, MCRYPT_MODE_CBC, $iv_password_dec);
    
$plain_login = trim($plain_login_dec, "\0");
$plain_password = trim ($plain_password_dec, "\0");

// echo  "<br/>The login is " . $plain_login . "\n";
// echo  "<br/>The password is " . $plain_password . "\n";

$base64_encode_login = base64_encode($plain_login);
$base64_encode_password = base64_encode($plain_password);

// echo  "<br/>The base64_encode_login is " . $base64_encode_login . "\n";
// echo  "<br/>The base64_encodepassword is " . $base64_encode_password . "\n";

$base64_decode_login = base64_decode($base64_encode_login);
$base64_decode_password = base64_decode($base64_encode_password);

// echo  "<br/>The base64_decode_login is " . $base64_decode_login . "\n";
// echo  "<br/>The base64_decodepassword is " . $base64_decode_password . "\n";

$encrypted = cryptoJsAesEncrypt($key, $plain_login);
echo "encrypted is " . $encrypted;

$decrypted = cryptoJsAesDecrypt($key, $encrypted);
echo "decrypted is " . $decrypted;

?>

<html>
<head>
<title>webRDP</title>
<meta http-equiv='content-type' content='text/html; charset=iso-8859-1'>
<meta http-equiv='pragma' content='no-cache'>
<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/aes.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="application/javascript">
var CryptoJSAesJson = {
    stringify: function (cipherParams) {
        var j = {ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)};
        if (cipherParams.iv) j.iv = cipherParams.iv.toString();
        if (cipherParams.salt) j.s = cipherParams.salt.toString();
        return JSON.stringify(j);
    },
    parse: function (jsonStr) {
        var j = JSON.parse(jsonStr);
        var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(j.ct)});
        if (j.iv) cipherParams.iv = CryptoJS.enc.Hex.parse(j.iv)
        if (j.s) cipherParams.salt = CryptoJS.enc.Hex.parse(j.s)
        return cipherParams;
    }
}

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
		
		data = CryptoJS.AES.decrypt({"ct":"L0DvWC\/BxBB7PueftulYBQ==","iv":"09c282288fa6ba75ea92216cbff02315","s":"dec731e47eccaed3"}, "Seyed Masoud Sadjadi @ IT Scholars", {format: CryptoJSAesJson});
		alert(data);
    	var json_obj= eval("("+data+")"); //jQuery.parseJSON(data);

    	//just for check the the keys     
    	var keys = Object.keys(json_obj);
    	for (var i = 0; i < keys.length; i++) {
        	console.log(keys[i]);
    	};
		
		// var decrypted = JSON.parse(CryptoJS.AES.decrypt(<?php echo "$encrypted"; ?>, "<?php echo "$key"; ?>", {format: CryptoJSAesJson}).toString(CryptoJS.enc.Utf8));
		var decrypted = JSON.parse(CryptoJS.AES.decrypt({"ct":"L0DvWC\/BxBB7PueftulYBQ==","iv":"09c282288fa6ba75ea92216cbff02315","s":"dec731e47eccaed3"}, "Seyed Masoud Sadjadi @ IT Scholars", {format: CryptoJSAesJson}).toString(CryptoJS.enc.Utf8));
		console.log( "decrypted: " + decrypted);
			
		var encrypted = CryptoJS.AES.encrypt(JSON.stringify(<?php echo "$decrypted"; ?>), "<?php echo "$key"; ?>", {format: CryptoJSAesJson}).toString();
		console.log( "encrypted: " + encrypted);
		
		var base64_encode_login = "<?php echo "$base64_encode_login"; ?>";
		var plain_login = window.atob(base64_encode_login);
		var base64_encode_password = "<?php echo "$base64_encode_password"; ?>";
		var plain_password = window.atob(base64_encode_password);

		document.getElementById("usernameId").value = plain_login;
		document.getElementById("passwordId").value = plain_password;
    });
 
    $( window ).load(function() {
        console.log( "window loaded" );

		WaitForAppletLoad("rdp", 10, 2000, function () {
        	console.log( "rdp applet loaded" );

			document.getElementById("usernameId").value = "";
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
<param id="usernameId" name="username" value="">

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
