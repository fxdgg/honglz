<?php
/**
 * 提供Base62的封装
 */
class BLH_Base62
{
	/**
	 * base62_dict
	 * @var array
	 */
	private static $base62_dict = array(
		 '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
		 'a', 'b', 'c', 'd', 'e', 'f', 'g',
		 'h', 'i','j', 'k', 'l', 'm', 'n',
		 'o', 'p', 'q','r', 's', 't',
		 'u', 'v', 'w', 'x', 'y','z',
		 'A', 'B', 'C', 'D', 'E', 'F', 'G',
		 'H', 'I', 'J', 'K', 'L', 'M', 'N',
		 'O', 'P', 'Q', 'R', 'S', 'T',
		 'U', 'V', 'W', 'X', 'Y', 'Z'
	);

	/**
	 * base62编码
	 * @param {String} id，如 "201110410216293360"
	 * @return {String} code，如 "wr4mOFqpbO"
	 */
	public static function encode($id)
	{
		$out = '';
		//从最后往前以7字节为一组读取mid
	 	for ($i = strlen($id) - 7; $i > -7; $i -= 7)
	 	{
	    	$offset1 = $i < 0 ? 0 : $i;
	  	  	$offset2 = $i + 7;
	  	  	$num = substr($id, $offset1, $offset2 - $offset1);
		    $k = substr($num, 0, 1);
		    $num = self::int10to62($num);
			$k == '0' && strlen($num) < 4 && $num = str_pad($num, 4, '0', STR_PAD_LEFT);;
			$out = $num . $out;
		}
	    return $out;
	}

	/**
	 * base62解码
	 * @param {String} code，如 "wr4mOFqpbO"
	 * @return {String} id，如 "201110410216293360"
	 */
	public static function decode($code)
	{
		$out = '';
		//用于第四位为0时的转换
		$k = substr($code, 3, 1);
		$code_len = strlen($code);
		if ($k != '0')
		{
			for ($i = $code_len - 4; $i > -4; $i -= 4) //从最后往前以4字节为一组读取URL字符
			{
				$offset1 = $i < 0 ? 0 : $i;
				$offset2 = $i + 4;
				$str = substr($code, $offset1, $offset2 - $offset1);
				$str = self::str62to10($str);
				if ($offset1 > 0) //若不是第一组，则不足7位补0
				{
					while (strlen($str) < 7)
					{
						$str = '0' . $str;
					}
				}
		  		$out = $str . $out;
			}
		}else{
			//从最后往前以4字节为一组读取URL字符
			for ($i = $code_len - 4; $i > -4; $i -= 4)
			{
				$offset1 = $i < 0 ? 0 : $i;
				$offset2 = $i + 4;
				if ($offset1 > -1 && $offset1 < 1 || $offset1 > 4)
				{
					$str = substr($code, $offset1, $offset2 - $offset1);
					$str = self::str62to10($str);
					//若不是第一组，则不足7位补0
					if ($offset1 > 0)
					{
						while (strlen($str) < 7)
						{
							$str = '0' . $str;
						}
					}
					$out = $str . $out;
				}else{
					$str = substr($code, $offset1 + 1, $offset2 - $offset1 - 1);
					$str = self::str62to10($str);
					//若不是第一组，则不足7位补0
					if ($offset1 > 0)
					{
						while (strlen($str) < 7)
						{
							$str = '0' . $str;
						}
					}
					$out = $str . $out;
				}
			}
		}
	 	return $out;
	}

	/**
	* 10进制值转换为62进制
	* @param {String} int10 10进制值
	* @return {String} 62进制值
	*/
	public static function int10to62($int10)
	{
		$s62 = '';
		$r = 0;
		while ($int10 != 0)
		{
			$r = $int10 % 62;
			$s62 = self::$base62_dict[$r] . $s62;
			$int10 = floor($int10 / 62);
		}
		return $s62;
	}

	/**
	* 62进制值转换为10进制
	* @param {String} str62 62进制值
	* @return {String} 10进制值
	*/
	public static function str62to10($str62)
	{
		$i10 = '0';
		$c = 0;
		$str62_length = strlen($str62);
		$base62_dict_cnt = count(self::$base62_dict);
		for ($i = 0; $i < $str62_length; $i++)
		{
			$n = $str62_length - $i - 1;
			$s = substr($str62, $i, 1);
			for ($k = 0; $k < $base62_dict_cnt; $k++)
			{
				if ($s == self::$base62_dict[$k])
				{
					$h = $k;
					$c += (int)($h * pow(62, $n));
					break;
				}
			}
			$i10 = $c;
		}
		return $i10;
	}
}