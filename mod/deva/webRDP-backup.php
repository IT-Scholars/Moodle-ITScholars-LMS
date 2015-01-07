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

function getUsername(){
	return "jose.tech";
}

function getPassword(){
	return "kaseya";
}
</script>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script>
    $( document ).ready(function() {
        console.log( "document loaded" );
		alert("document loaded");
		document.getElementById("myUsername").value("jose.tech");
    });
 
    $( window ).load(function() {
        console.log( "window loaded" );
    	alert("window loaded");
	});
    </script></head>
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
<!-- param name="username" value="< ?php echo $_GET["username"]; ?>" -->
<param id="myUsername" name="username" value="Encript_Username">

<!-- Password to authenticate to terminal server with -->
<!-- Optional SSO Parameter -->
<!-- param name="password" value="< ?php echo $_GET["password"]; ?>" -->
<param name="password" value="kaseya">

<!-- AD Domain name to authenticate to terminal server with -->
<!-- Optional SSO Parameter -->
<param name="domain" value="<?php echo $_GET["domain"]; ?>">

<!-- Application to start. This value should be url encoded.  In this example we are launching c:\windows\system32\notepad.exe-->
<!-- param name="program" value="<?php echo $_GET["appName"]; ?>" -->

<!-- Working directory for Application.  This value should be url encoded.  In this example the working directory will be set to c:\windows\system32\ -->
<!-- param name="directory" value="<?php echo $_GET["appDir"]; ?>" -->

<!-- In this example I used a site ( http://meyerweb.com/eric/tools/dencoder/ ) to encode the above values -->

<!-- This specifies a javascript method to be called after the user logs out of the RDP session -->
<!--  <param name="onlogout" value="javascript:rdpOnLogout();"> -->

<param name="pf" value="127">
<param name="bpp" value="<?php echo $_GET['frameBpp']; ?>">
<param name="onlogout" value="javascript:rdpOnLogout();">

</applet>
</center>
<?php
///*
echo '<br/>_COOKIE: ';
print_r($_COOKIE);
echo '<br/>_SESSION: ';
print_r($_SESSION);
echo '<br/>';
//*/
    # --- DECRYPTION ---

    # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
    # convert a string into a key
    # key is specified using hexadecimal
    $key = pack('H*', "5365796564204d61736f7564205361646a6164692040204954205363686f6c617273");
    
    # show key size use either 16, 24 or 32 byte keys for AES-128, 192
    # and 256 respectively
    $key_size =  strlen($key);
    // echo "Key size: " . $key_size . "\n";
    
    # create a random IV to use with CBC encoding
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    
    $cipher_login_base64 = $_COOKIE[cipher_login];
    $cipher_password_base64 = $_COOKIE[cipher_password];
    // echo  "cipher_login_base64: " . $cipher_login_base64 . "\n";
    // echo  "cipher_password_base64: " . $cipher_password_base64 . "\n";
	
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
    
    echo  "<br/>The login is " . $plain_login_dec . "\n";
    echo  "<br/>The password is " . $plain_password_dec . "\n";
?>

</body>
</html>
