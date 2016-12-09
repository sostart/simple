<?php
namespace core\helper;
/**
 * 公共方法类
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Common
{
	// 递归合并数组
	public static function array_merge_recursive()
	{
		$arrays = func_get_args();
		$base = array_shift($arrays);
		
		foreach ($arrays as $array)
		{
			reset($base);
			while(list($key, $value) = @each($array))
			{
				if (is_array($value) && @is_array($base[$key]))
				{
					$base[$key] = \Common::array_merge_recursive($base[$key], $value);
				}
				else
				{
					$base[$key] = $value;
				}
			}
		}
		
		return $base;
	}
	
	// 获取数组中的某个值,方便连贯操作
	public static function array_value($k,$arr)
	{
		return $arr[$k];
	}
	
	// 404
	public static function show_404()
	{
		\View::render('/404',array(),true,CORE_PATH); exit;
	}
	
	// 页面跳转
	public static function redirect($url,$param=array())
	{
		header("Location: ".\Common::url($url,$param)); exit;
	}
	
	// 生成url
	public static function url($url,$param=array())
	{
		if($url=='/') return "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}";

		$c = \Config::get('Component.controller.c');
		$a = \Config::get('Component.controller.a');
		
		$t = explode('/',$url);

		foreach($param as $k=>$v) $x .= "&{$k}={$v}";
		
		return "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?{$c}={$t[0]}&{$a}={$t[1]}{$x}";
	}
	
	// 
	public static function apiUrl($url,$param=array())
	{
		$l = \Config::get('Component.logic.l');
		$m = \Config::get('Component.logic.m');

		$t = explode('/',$url);

		foreach($param as $k=>$v) $x .= "&{$k}={$v}";
		
		return "http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}?{$l}={$t[0]}&{$m}={$t[1]}{$x}";
	}
	
	// 生成数字签名
	public static function Sign($data)
	{
		ksort($data);
		return md5(json_encode($data).\Config::get('secret'));
	}

	// 数字签名验证
	public static function SignCheck($sign,$param)
	{
		ksort($param);
		return $sign == md5(json_encode($param).\Config::get('secret'));
	}
	
	// 连贯操作
	public static function with($obj)
	{
		return $obj;
	}

	/**
	* 去除隐形字符
	*
	* This prevents sandwiching null characters
	* between ascii characters, like Java\0script.
	*
	* @param	string
	* @return	string
	*/
	public static function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();
		
		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}

	/**
	* 获取客户端IP
	*
	* @return	string
	*/
	public static function client_ip()
	{
		$clientip = '';

		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
		{
			$clientip = getenv('HTTP_CLIENT_IP');
		} 
		else if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
		{
			$clientip = getenv('HTTP_X_FORWARDED_FOR');
		} 
		else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
		{
			$clientip = getenv('REMOTE_ADDR');
		}
		elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
		{
			$clientip = $_SERVER['REMOTE_ADDR'];
		}

		preg_match("/[\d\.]{7,15}/", $clientip, $clientipmatches);
		
		if($clientipmatches[0])
		{
			return bindec( decbin( ip2long( $clientipmatches[0] ) ) );
		}
		else
		{
			return 0;
		}
	}

	/*
	* php5.4以下使用此方法使中文不被编码
	*/
	public static function JsonEncode($str,$op=0)
	{
		// @fix SAE-BUG iconv
		// return preg_replace('/\\\u([0-9a-f]{4}+)/ie', "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", json_encode($str));
		
		if(is_array($str))
		{
			$return = array();
			foreach($str as $k=>$v)
			{
				$return[static::JsonEncode($k,1)] = static::JsonEncode($v,1);
			}
			return urldecode(json_encode($return));
		}
		else
		{
			return is_string($str) ? ($op ? urlencode($str) : urldecode(json_encode(urlencode($str)))) : $str;
		}
	}

	/*
	* 功能详见php手册 php5.5以下实现array_column
	*/
	public static function array_column($input,$column_key,$index_key=NULL)
	{
		if(function_exists('array_column')) return $index_key?array_column($input,$column_key,$index_key):array_column($input,$column_key);
		
		$return = array();
		foreach($input as $row)
		{
			$index_key ? ($return[$row[$index_key]] = $row[$column_key]) : ($return[] = $row[$column_key]);
		}
		return $return;
	}
}