<?php

namespace App\Containers\Geral\Helpers;

class EncodeHelper
{
	public $password = '4z9wt8f4a0';

	public $ivlen = 4;

	/*	simbols
	 a,b,c,d,e,f,g,h,2,3
	 k,m,n,p,q,r,s,t,4,5
	 w,x,y,z,i,j,u,v,6,7
	 8
	 9
	 */
	public $alfavit = [
		1 => ['a', 'x', 'c', 'z', 'e', 'f', 'u', 'h', '2', '3'],
		2 => ['k', 'm', 'y', 'p', 'q', 'j', 's', 't', '6', '5'],
		3 => ['w', 'b', 'n', 'd', 'i', 'r', 'g', 'v', '4', '7']
	];

	public function get_rnd_iv($iv_len)
	{
		$iv = '';
		while ($iv_len-- > 0) {
			$iv .= chr($iv_len & 0xff);
		}

		return $iv;
	}

	public function md5_encrypt($plain_text, $iv_len = '', $password = '')
	{
		$iv_len = !empty($iv_len) ? $iv_len : $this->ivlen;
		$password = !empty($password) ? $password : $this->password;
		$plain_text .= "\x13";
		$n = strlen($plain_text);
		if ($n % 16) {
			$plain_text .= str_repeat("\0", 16 - ($n % 16));
		}
		$i = 0;
		$enc_text = $this->get_rnd_iv($iv_len);
		$iv = substr($password ^ $enc_text, 0, 512);
		while ($i < $n) {
			$block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
			$enc_text .= $block;
			$iv = substr($block . $iv, 0, 512) ^ $password;
			$i += 16;
		}

		return base64_encode($enc_text);
	}

	public function md5_decrypt($enc_text, $iv_len = '', $password = '')
	{
		$iv_len = !empty($iv_len) ? $iv_len : $this->ivlen;
		$password = !empty($password) ? $password : $this->password;
		$enc_text = base64_decode($enc_text);
		$n = strlen($enc_text);
		$i = $iv_len;
		$plain_text = '';
		$iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
		while ($i < $n) {
			$block = substr($enc_text, $i, 16);
			$plain_text .= $block ^ pack('H*', md5($iv));
			$iv = substr($block . $iv, 0, 512) ^ $password;
			$i += 16;
		}

		return preg_replace('/\\x13\\x00*$/', '', $plain_text);
	}

	public function mcrypt_encrypt($text, $password)
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_IDEA, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$key = $password;

		$crypttext = mcrypt_encrypt(MCRYPT_IDEA, $key, $text, MCRYPT_MODE_ECB, $iv);

		return $crypttext;
	}

	public function mcrypt_decrypt($crypttext, $password)
	{
		$iv_size = mcrypt_get_iv_size(MCRYPT_RC2, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$key = $password;

		$text = mcrypt_decrypt(MCRYPT_RC2, $key, $crypttext, MCRYPT_MODE_ECB, $iv);

		return $text;
	}

	public function encodeArray($array)
	{
		$i = 0;
		$string = '';
		foreach ($array as $var => $val) {
			if ($i < count($array) - 1) {
				$add = '&';
			} else {
				$add = '';
			}
			$string .= $var . '=' . $val . $add;
			$i++;
		}

		return $this->md5_encrypt($string);
	}

	public function decodeArray($string)
	{
		$mainArray = [];
		$string = ($this->md5_decrypt($string));
		$array = explode('&', $string);
		for ($i = 0;$i < count($array);$i++) {
			$subArray = explode('=', $array[$i]);
			$subArray2 = [$subArray[0] => $subArray[1]];
			$mainArray = array_merge($mainArray, $subArray2);
		}

		return $mainArray;
	}

	public function encodeURLarray($array)
	{
		$i = 0;
		foreach ($array as $var => $val) {
			if ($i < count($array) - 1) {
				$add = '&';
			} else {
				$add = '';
			}
			$string .= $var . '=' . $val . $add;
			$i++;
		}

		return base64_encode($string);
	}

	public function decodeURLarray($string)
	{
		$mainArray = [];
		$string = base64_decode($string);
		$array = explode('&', $string);
		for ($i = 0;$i < count($array);$i++) {
			$subArray = explode('=', $array[$i]);
			$subArray2 = [$subArray[0] => $subArray[1]];
			$mainArray = array_merge($mainArray, $subArray2);
		}

		return $mainArray;
	}

	/**
	 * number length should be = 1
	 */
	public function number2simbol($number, $sibmol_types = 1)
	{
		return $this->alfavit[$sibmol_types][$number];
	}

	public function unqidId($number, $prefix = '')
	{
		$str_number = strval($number);
		$code = '';

		for ($i = 0; $i < strlen($str_number); $i++) {
			$sibmol_types = mt_rand(1, 3);
			$code .= $this->number2simbol(intval($str_number{$i}), $sibmol_types);
		}

		return $prefix . $code;
	}

	public function encodeCardNumber($number, $prefix = '***', $char_count = 4)
	{
		$res = substr($number, strlen($number) - $char_count, $char_count);
		$res = $prefix . $res;

		return $res;
	}

	public function encodeId($id)
	{
		$data = ['id' => $id];

		return base64_encode($this->md5_encrypt(serialize($data), $this->ivlen, $this->password));
	}

	public function decodeId($encodedId)
	{
		$data = unserialize($this->md5_decrypt(base64_decode($encodedId), $this->ivlen, $this->password));
		if ($data && $data['id']) {
			return $data['id'];
		} else {
			return false;
		}
	}

	public function encrypt($str, $password = '')
	{
		return $this->md5_encrypt($str, '', $password);
	}

	public function decode($enc_text, $password)
	{
		return $this->md5_decrypt($enc_text, '', $password);
	}

	public function decrypt($enc_text, $password)
	{
		return $this->md5_decrypt($enc_text, '', $password);
	}

	public function openSSLEncrypt($value, $key, $cipher = 'AES-128-CBC', $asBinary = true, $options = OPENSSL_RAW_DATA)
	{
		$ivlen = openssl_cipher_iv_length($cipher);
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($value, $cipher, $key, $options, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $asBinary);

		return base64_encode($iv . $hmac . $ciphertext_raw);
	}

	public function openSSLDecrypt($value, $key, $cipher = 'AES-128-CBC', $asBinary = true, $options = OPENSSL_RAW_DATA, $sha2len = 32)
	{
		$c = base64_decode($value);
		$ivlen = openssl_cipher_iv_length($cipher);
		$iv = substr($c, 0, $ivlen);
		$hmac = substr($c, $ivlen, $sha2len);
		$ciphertext_raw = substr($c, $ivlen + $sha2len);
		$original_value = openssl_decrypt($ciphertext_raw, $cipher, $key, $options, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $asBinary);
		if (hash_equals($hmac, $calcmac)) {//PHP 5.6+ timing attack safe comparison
			return $original_value;
		}

		return null;
	}
}
