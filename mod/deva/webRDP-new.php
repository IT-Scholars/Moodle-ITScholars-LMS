<?php
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

?>

<html>
<head>
<title>webRDP</title>
<meta http-equiv='content-type' content='text/html; charset=iso-8859-1'>
<meta http-equiv='pragma' content='no-cache'>
<script type='text/javascript'>
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
</script>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="http://www.java.com/js/deployJava.js"></script>
<script>
    $( document ).ready(function() {
        console.log( "document loaded" );
		// launch the Java 2D applet on JRE version 1.6.0
		// or higher with one parameter (fontSize)
		var attributes = {
			code:'com.webinflection.webrdp.MainApplet',
        	archive:'webRDP.jar',
        	width:'<?php echo $_GET["frameWidth"]; ?>', 
			height:'<?php echo $_GET["frameHeight"]; ?>'
		};
    	var parameters = {
			host:'<?php echo $_GET["hostName"]; ?>',
			port:'<?php echo $_GET["hostPort"]; ?>',
			// username:'<?php echo $_GET["username"]; ?>',
			username:'<?php echo $plain_login; ?>',
			// password:'<?php echo $_GET["password"]; ?>',
			password:'<?php echo $plain_password; ?>',
			domain:'<?php echo $_GET["domain"]; ?>',
			pflags:'0',
			bpp:'<?php echo $_GET["frameBpp"]; ?>',
			clipboard:'1',
			onlogout:'javascript:rdpOnLogout();'
		};
    	var version = '1.6' ;
    	deployJava.runApplet(attributes, parameters, version);
		deployJava.
		document.getElementsByName("password").value = "CryptPassword";
    });
 
    $( window ).load(function() {
        console.log( "window loaded" );
	});
    </script>
</head>
<body bgcolor="#DFEFFC">
</body>
</html>
