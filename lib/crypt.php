<?php
class Crypt {
	
	private static $key = "1234567890123456"; //16 Character Key

	public static function encrypt($decryptedData) {
		// echo "<br/>Inside encrypt()!";
		$encryptedData = NULL;
		// echo "<br/>\$decryptedData: $decryptedData";
		if (!empty($decryptedData)) {	
			// echo "<br/>\$decryptedData is not empty!";
			// echo "<br/>MCRYPT_RIJNDAEL_128: ";
			// print_r(MCRYPT_RIJNDAEL_128);
			// echo "<br/>MCRYPT_MODE_ECB: ";
			// print_r(MCRYPT_MODE_ECB);
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB); 
			// echo "<br/>\$size: $size";
			$encryptedData = Crypt::pkcs5_pad($decryptedData, $size); 
			// echo "<br/>\$encryptedData: $encryptedData";
			$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, ''); 
			// echo "<br/>\$td: $td";
			$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND); 
			// echo "<br/>\$iv: $iv";
			mcrypt_generic_init($td, Crypt::$key, $iv); 
			$encryptedData = mcrypt_generic($td, $encryptedData); 
			// echo "<br/>\$encryptedData: $encryptedData";
			mcrypt_generic_deinit($td); 
			mcrypt_module_close($td); 
			$encryptedData = base64_encode($encryptedData); 
			// echo "<br/>\$encryptedData: $encryptedData";
		}
		
		// echo "<br/>Ready to get out of encrypt()!";
		return $encryptedData; 
	
	} 

	private static function pkcs5_pad ($text, $blocksize) { 
		$pad = $blocksize - (strlen($text) % $blocksize); 
		return $text . str_repeat(chr($pad), $pad); 
	} 

	public static function decrypt($encryptedData) {
		// echo "<br/>Inside decrypt()!";
		$decryptedData= NULL;
		// echo "<br/>\$encryptedData: $encryptedData";
		if (!empty($encryptedData)) {	
			// echo "<br/>\$encryptedData is not empty!";
			// echo "<br/>MCRYPT_RIJNDAEL_128: ";
			// print_r(MCRYPT_RIJNDAEL_128);
			// echo "<br/>MCRYPT_MODE_ECB: ";
			// print_r(MCRYPT_MODE_ECB);
			// echo "<br/>Crypt::\$key: ";
			// print_r(Crypt::$key);
			$decryptedData = mcrypt_decrypt(
				MCRYPT_RIJNDAEL_128,
				Crypt::$key, 
				base64_decode($encryptedData), 
				MCRYPT_MODE_ECB
			);
			$dec_s = strlen($decryptedData); 
			// echo "<br/>\$dec_s: $dec_s";
			$padding = ord($decryptedData[$dec_s-1]); 
			// echo "<br/>\$padding: $padding";
			$decryptedData = substr($decryptedData, 0, -$padding);
			// echo "<br/>\$decryptedData: $decryptedData";
		}
		return $decryptedData;
	}	
}
?>
