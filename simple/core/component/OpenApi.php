<?php
namespace core\component;

class OpenApi
{
	public $appid;
	public $appkey;
	public $server_name;
	public $pf;
	public $params = array();

	public function init()
	{
		$this->sdk = new OpenApiV3($this->appid, $this->appkey);
		$this->sdk->setServerName($this->server_name);
		$this->setParams(array(
			'openid'=>\Session::get('openid'),
			'openkey'=>\Session::get('openkey'),
			'pfkey'=>\Session::get('pfkey'),
			'pf'=>$this->pf
		));
	}

	public function setParams($params)
	{
		$this->params = array_merge($this->params,$params);
	}

	public function User()
	{
		$this->script_name = '/v3/user/';
		return $this;
	}

	public function Relation()
	{
		$this->script_name = '/v3/relation/';
		return $this;
	}

	public function Pay()
	{
		return $this;
	}

	public function Sign($method, $url_path, $params)
	{
		$secret = $this->appkey . '&';
		return SnsSigCheck::makeSig($method, $url_path, $params, $secret);
	}

	public function verifySig($url_path, $params, $sig)
	{
		return SnsSigCheck::verifySig('GET', $url_path, $params, $this->appkey . '&', $sig);
	}

	/**
	 * CEE 执行支付API调用，返回结果数组
	 *
	 * @param array $params 调用支付API时带的参数 参考http://wiki.open.qq.com/wiki/v3/pay/buy_goods
	 * @return array 结果数组
	 */
	public function buyGoods($params)
	{
		$params["cee_extend"] = getenv("CEE_DOMAINNAME").'*'.getenv("CEE_VERSIONID").'*'.getenv("CEE_WSNAME");
		$params = array_merge($this->params,$params);
		return $this->sdk->api('/v3/pay/buy_goods',$params,'post','https');
	}
	
	// 通知腾讯 确认发货
	public function confirmDelivery($params)
	{
		$params = array_merge($this->params,$params);
		return $this->sdk->api('/v3/pay/confirm_delivery',$params,'post','https');
	}

	public function getInfo()
	{
		$info = $this->__call('getInfo',array());
		if($info['ret']==0)
		{
			$info['figureurl'] = substr($info['figureurl'],0,strrpos($info['figureurl'],'/')).'/100';
		}
		return $info;
	}

	public function __call($name,$arguments)
	{
		$script_name = $this->script_name.strtolower(implode('_', preg_split("/(?=[A-Z])/", $name, null, PREG_SPLIT_NO_EMPTY)));
		$arguments = array_merge($this->params,$arguments);
		return $this->sdk->api($script_name, $arguments,'post');
	}
}


/**
 * 错误码定义
 */
define('OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY', 1801); // 参数为空
define('OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID', 1802); // 参数格式错误
define('OPENAPI_ERROR_RESPONSE_DATA_INVALID', 1803); // 返回包格式错误
define('OPENAPI_ERROR_CURL', 1900); // 网络错误, 偏移量1900, 详见 http://curl.haxx.se/libcurl/c/libcurl-errors.html

/**
 * 提供访问腾讯开放平台 OpenApiV3 的接口
 */
class OpenApiV3
{
	private $appid  = 0;
	private $appkey = '';
	private $server_name = '';
	private $format = 'json';
	private $stat_url = "apistat.tencentyun.com";
	private $is_stat = true;
	
	/**
	 * 构造函数
	 *
	 * @param int $appid 应用的ID
	 * @param string $appkey 应用的密钥
	 */
	function __construct($appid, $appkey)
	{
		$this->appid = $appid;
		$this->appkey = $appkey;
	}
	
	public function setServerName($server_name)
	{
		$this->server_name = $server_name;
	}
	
	public function setStatUrl($stat_url)
	{
		$this->stat_url = $stat_url;
	}
	
	public function setIsStat($is_stat)
	{
		$this->is_stat = $is_stat;
	}
	
	/**
	 * 执行API调用，返回结果数组
	 *
	 * @param string $script_name 调用的API方法，比如/v3/user/get_info，参考 http://wiki.open.qq.com/wiki/API_V3.0%E6%96%87%E6%A1%A3
	 * @param array $params 调用API时带的参数
	 * @param string $method 请求方法 post / get
	 * @param string $protocol 协议类型 http / https
	 * @return array 结果数组
	 */
	public function api($script_name, $params, $method='post', $protocol='http')
	{
		// 检查 openid 是否为空
		if (!isset($params['openid']) || empty($params['openid']))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'openid is empty');
		}
		// 检查 openid 是否合法
		if (!static::isOpenId($params['openid']))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
				'msg' => 'openid is invalid');
		}
		
		// 无需传sig, 会自动生成
		unset($params['sig']);
		
		// 添加一些参数
		$params['appid'] = $this->appid;
		$params['format'] = $this->format;
		
		// 生成签名
		$secret = $this->appkey . '&';
		$sig = SnsSigCheck::makeSig( $method, $script_name, $params, $secret);
		$params['sig'] = $sig;
	
		$url = $protocol . '://' . $this->server_name . $script_name;
		$cookie = array();
        
		//记录接口调用开始时间
		$start_time = SnsStat::getTime();
		
		//通过调用以下方法，可以打印出最终发送到openapi服务器的请求参数以及url，默认为注释
		//static::printRequest($url,$params,$method);
		
		
		// 发起请求
		$ret = \SnsNetwork::makeRequest($url, $params, $cookie, $method, $protocol);
		
		if (false === $ret['result'])
		{
			$result_array = array(
				'ret' => OPENAPI_ERROR_CURL + $ret['errno'],
				'msg' => $ret['msg'],
			);
		}
		
		$result_array = json_decode($ret['msg'], true);
		
		// 远程返回的不是 json 格式, 说明返回包有问题
		if (is_null($result_array)) {
			$result_array = array(
				'ret' => OPENAPI_ERROR_RESPONSE_DATA_INVALID,
				'msg' => $ret['msg']
			);
		}

		// 统计上报
		if ($this->is_stat)
		{
			$stat_params = array(
					'appid' => $this->appid,
					'pf' => $params['pf'],
					'rc' => $result_array['ret'],
					'svr_name' => $this->server_name,
					'interface' => $script_name,
					'protocol' => $protocol,
					'method' => $method,
			);
			SnsStat::statReport($this->stat_url, $start_time, $stat_params);
		}
		
		//通过调用以下方法，可以打印出调用openapi请求的返回码以及错误信息，默认注释
		//static::printRespond($result_array);
		
		return $result_array;
	}
	
	/**
	 * 执行上传文件API调用，返回结果数组
	 *
	 * @param string $script_name 调用的API方法，比如/v3/user/get_info， 参考 http://wiki.open.qq.com/wiki/API_V3.0%E6%96%87%E6%A1%A3
	 * @param array $params 调用API时带的参数，必须是array
	 * @param array $array_files 调用API时带的文件，必须是array，key为openapi接口的参数，value为"@"加上文件全路径的字符串
	 *															  举例 array('pic'=>'@/home/xxx/hello.jpg',...);
	 * @param string $protocol 协议类型 http / https
	 * @return array 结果数组
	 */
	public function apiUploadFile($script_name, $params, $array_files, $protocol='http')
	{
		// 检查 openid 是否为空
		if (!isset($params['openid']) || empty($params['openid']))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_EMPTY,
				'msg' => 'openid is empty');
		}
		// 检查 openid 是否合法
		if (!static::isOpenId($params['openid']))
		{
			return array(
				'ret' => OPENAPI_ERROR_REQUIRED_PARAMETER_INVALID,
				'msg' => 'openid is invalid');
		}
		
		// 无需传sig, 会自动生成
		unset($params['sig']);
		
		// 添加一些参数
		$params['appid'] = $this->appid;
		$params['format'] = $this->format;
		
		// 生成签名
		$secret = $this->appkey . '&';
		$sig = SnsSigCheck::makeSig( 'post', $script_name, $params, $secret);
		$params['sig'] = $sig;
		
		//上传文件,图片参数不能参与签名
		foreach($array_files as $k => $v)
		{
			$params[$k] = $v ;
		}
		
		$url = $protocol . '://' . $this->server_name . $script_name;
		$cookie = array();
        
		//记录接口调用开始时间
		$start_time = SnsStat::getTime();
		
		//通过调用以下方法，可以打印出最终发送到openapi服务器的请求参数以及url，默认注释
		//static::printRequest($url, $params,'post');
		
		// 发起请求
		$ret = \SnsNetwork::makeRequestWithFile($url, $params, $cookie, $protocol);
		
		if (false === $ret['result'])
		{
			$result_array = array(
				'ret' => OPENAPI_ERROR_CURL + $ret['errno'],
				'msg' => $ret['msg'],
			);
		}
		
		$result_array = json_decode($ret['msg'], true);
		
		// 远程返回的不是 json 格式, 说明返回包有问题
		if (is_null($result_array)) {
			$result_array = array(
				'ret' => OPENAPI_ERROR_RESPONSE_DATA_INVALID,
				'msg' => $ret['msg']
			);
		}

		// 统计上报
		if ($this->is_stat)
		{
			$stat_params = array(
					'appid' => $this->appid,
					'pf' => $params['pf'],
					'rc' => $result_array['ret'],
					'svr_name' => $this->server_name,
					'interface' => $script_name,
					'protocol' => $protocol,
					'method' => 'post',
			);
			SnsStat::statReport($this->stat_url, $start_time, $stat_params);
		}
		
		//通过调用以下方法，可以打印出调用openapi请求的返回码以及错误信息,默认注释
		//static::printRespond($result_array);
		
		return $result_array;
	}

	/**
	 * 打印出请求串的内容，当API中的这个函数的注释放开将会被调用。
	 *
	 * @param string $url 请求串内容
	 * @param array $params 请求串的参数，必须是array
	 * @param string $method 请求的方法 get / post
	 */
	private function printRequest($url, $params,$method)
	{
		$query_string = \SnsNetwork::makeQueryString($params);
		if($method == 'get')
		{
			$url = $url."?".$query_string;
		}
		echo "\n============= request info ================\n\n";
		print_r("method : ".$method."\n");
		print_r("url    : ".$url."\n");
		if($method == 'post')
		{
			print_r("query_string : ".$query_string."\n");
		}
		echo "\n";
		print_r("params : ".print_r($params, true)."\n");
		echo "\n";
	}
	
	/**
	 * 打印出返回结果的内容，当API中的这个函数的注释放开将会被调用。
	 *
	 * @param array $array 待打印的array
	 */
	private function printRespond($array)
	{
		echo "\n============= respond info ================\n\n";
		print_r($array);
		echo "\n";
	}
	
	/**
	 * 检查 openid 的格式
	 *
	 * @param string $openid openid
	 * @return bool (true|false)
	 */
	private static function isOpenId($openid)
	{
		return (0 == preg_match('/^[0-9a-fA-F]{32}$/', $openid)) ? false : true;
	}	
}


/**
 * 生成签名类
 *
 * @version 3.0.3
 * @author open.qq.com
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 * @ History:
 *               3.0.3 | nemozhang | 2012-08-28 16:40:20 | support cpay callback sig verifictaion.
 *               3.0.2 | sparkeli | 2012-03-06 17:58:20 | add statistic fuction which can report API's access time and number to background server
 *               3.0.1 | nemozhang | 2012-02-14 17:58:20 | resolve a bug: at line 108, change  'post' to  $method
 *               3.0.0 | nemozhang | 2011-12-12 11:11:11 | initialization
 */



/**
 * 生成签名类
 */
class SnsSigCheck
{
	/**
	 * 生成签名
	 *
	 * @param string 	$method 请求方法 "get" or "post"
	 * @param string 	$url_path 
	 * @param array 	$params 表单参数
	 * @param string 	$secret 密钥
	 */
    static public function makeSig($method, $url_path, $params, $secret) 
    {
        $mk = static::makeSource($method, $url_path, $params);
        $my_sign = hash_hmac("sha1", $mk, strtr($secret, '-_', '+/'), true);
        $my_sign = base64_encode($my_sign);

        return $my_sign;
    }
    
	static private function makeSource($method, $url_path, $params) 
    {
        $strs = strtoupper($method) . '&' . rawurlencode($url_path) . '&';

        ksort($params);
        $query_string = array();
        foreach ($params as $key => $val ) 
        { 
            array_push($query_string, $key . '=' . $val);
        }   
        $query_string = join('&', $query_string);

        return $strs . str_replace('~', '%7E', rawurlencode($query_string));
    }

	/**
	 * 验证回调发货URL的签名 (注意和普通的OpenAPI签名算法不一样，详见@refer的说明)
     *
	 * @param string 	$method 请求方法 "get" or "post"
	 * @param string 	$url_path 
	 * @param array 	$params 腾讯调用发货回调URL携带的请求参数
	 * @param string 	$secret 密钥
     * @param string 	$sig 腾讯调用发货回调URL时传递的签名
	 *
     * @refer 
     *  http://wiki.open.qq.com/wiki/%E5%9B%9E%E8%B0%83%E5%8F%91%E8%B4%A7URL%E7%9A%84%E5%8D%8F%E8%AE%AE%E8%AF%B4%E6%98%8E_V3
	 */
    static public function verifySig($method, $url_path, $params, $secret, $sig) 
    {
        unset($params['sig']);

        // 先使用专用的编码规则对value编码
        foreach ($params as $k => $v)
        {
            $params[$k] = static::encodeValue($v);
        }

        // 再计算签名
        $sig_new = static::makeSig($method, $url_path, $params, $secret);

        return $sig_new == $sig;
    }
    
	/**
	 * 回调发货URL专用的编码算法
	 *  编码规则为：除了 0~9 a~z A~Z !*()之外其他字符按其ASCII码的十六进制加%进行表示，例如"-"编码为"%2D"
     * @refer 
     *  http://wiki.open.qq.com/wiki/%E5%9B%9E%E8%B0%83%E5%8F%91%E8%B4%A7URL%E7%9A%84%E5%8D%8F%E8%AE%AE%E8%AF%B4%E6%98%8E_V3
	 */
    static private function encodeValue($value) 
    {
        $rst = '';

        $len = strlen($value);

        for ($i=0; $i<$len; $i++)
        {
            $c = $value[$i];
            if (preg_match ("/[a-zA-Z0-9!\(\)*]{1,1}/", $c))
            {
                $rst .= $c;
            }
            else
            {
                $rst .= ("%" . sprintf("%02X", ord($c)));                                                                                                                
            }   
        }   

        return $rst;
    } 
}


/**
 * 统计上报接口调用情况
 *
 * @version 3.0.2
 * @author open.qq.com
 * @copyright © 2011, Tencent Corporation. All rights reserved.
 * @ History:
 *               3.0.2 | sparkeli | 2012-03-06 15:33:04 | initialize statistic fuction which can report API's access time and number to background server
 */


class SnsStat
{
	/**
	 * 执行一个 统计上报
	 *
	 * @param string $stat_url 统计上报的URL
	 * @param float $start_time 统计开始时间
	 * @param array $params 统计参数数组
	 * @return 
	 */
	static public function statReport($stat_url, $start_time, $params)
	{   
		$end_time = static::getTime();
		$params['time'] = round($end_time - $start_time, 4);
		$params['timestamp'] = time();
		$params['collect_point'] = 'sdk-php-v3';
		$stat_str = json_encode($params);
		//发送上报信息
		$host_ip = gethostbyname($stat_url);
		if ($host_ip != $stat_url)
		{
			$sock = socket_create(AF_INET, SOCK_DGRAM, 0);
			if (false === $sock)
			{
				return;
			}
			socket_sendto($sock, $stat_str, strlen($stat_str), 0, $host_ip, 19888);
			socket_close($sock);	
		}
	}
	
	static public function getTime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}



// end of script
