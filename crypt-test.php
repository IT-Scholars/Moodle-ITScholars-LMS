<?
	// phpinfo();
	echo dirname(__FILE__) . "/lib/crypt.php" ;
	require_once(dirname(__FILE__) . "/lib/crypt.php");
	$password = "Masoud";
	echo "<br />\$password: $password";
	$encrypted_password = Crypt::encrypt($password);
	echo "<br />\$encrypted_password: $encrypted_password";
	$decrypted_password = Crypt::decrypt($encrypted_password);;
	echo "<br />\$decrypted_password: $decrypted_password";
?>