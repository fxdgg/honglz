<?php
class BLH_Des
{
	/**
	 *
	 * 3des的key
	 * @var string
	 */
	private static $key = 'ejGT9rTe';
	/**
	 *
	 * 偏移量
	 * @var string
	 */
	private static $iv = 0;

	/**
	 *
	 * @param string $key
	 * @param string $iv
	 */
	function __construct($key = '', $iv = 0)
	{
		!empty($key) && self::$key = $key;
		if ($iv == 0)
		{
			self::$iv = $key;
		}
		else
		{
			self::$iv = $iv;
		}
	}

	/**
	 *
	 * 3des 加密
	 * @param string $str
	 */
	public static function encrypt($str)
	{
		$size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
		$str = self::pkcs5Pad($str, $size);
		$data = mcrypt_cbc(MCRYPT_DES, self::$key, $str, MCRYPT_ENCRYPT, self::$iv);
		return base64_encode($data);
	}
	/**
	 *
	 * 3des 解密
	 * @param string $str
	 */
	public function decrypt($str)
	{
		$str = base64_decode($str);
		$str = mcrypt_cbc(MCRYPT_DES, self::$key, $str, MCRYPT_DECRYPT, self::$iv);
		$str = self::pkcs5Unpad($str);
		return $str;
	}
	public function decrypt_bak($str)
	{
		$result = FALSE;
		if($str){
			$str = mcrypt_cbc(MCRYPT_DES, self::$key, $str, MCRYPT_DECRYPT, self::$iv);
			$result = self::pkcs5Unpad($str);
		}
		return $result;
	}
	private function hex2bin($hexData)
	{
		$binData = "";
		for ($i = 0; $i < strlen($hexData); $i += 2)
		{
			$binData .= chr(hexdec(substr($hexData, $i, 2)));
		}
		return $binData;
	}
	private static function pkcs5Pad($text, $blocksize)
	{
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}
	private function pkcs5Unpad($text)
	{
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text ))
			return false;
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return false;
		return substr ( $text, 0, - 1 * $pad );
	}
}