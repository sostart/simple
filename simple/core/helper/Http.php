<?php
namespace core\helper;

class Http{
	private $handel;

	public function __construct(){
		$this->handel = curl_init();
	}
	
	// 实例化
	public static function init(){
		return new \Http();
	}
	
	// 解压gzip
	public static function gzdecode($data){
		if(function_exists('gzdecode')){
			return gzdecode($data);
		}else{
			return gzinflate(substr($data,10,-8));			
		}
	}

	// 设置头信息
	public function headers($headers){
		curl_setopt($this->handel,CURLOPT_HTTPHEADER,$headers);
		return $this;
	}
	
	// 发送get请求
	public function get($url){
		curl_setopt($this->handel,CURLOPT_URL,$url);

		// 允许curl提交后,网页重定向  
		curl_setopt($this->handel, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($this->handel, CURLOPT_FOLLOWLOCATION, 1); 
		
		// 将curl提交后的header返回
		curl_setopt($this->handel,CURLOPT_HEADER,1);
		
		// 不直接输出返回信息
		curl_setopt($this->handel,CURLOPT_RETURNTRANSFER,TRUE);
		

		preg_match("/([\W\S]*)\r\n\r\n([\W\S]*)/", curl_exec($this->handel), $matches);
		$this->parse_response_header($matches[1]);
		
		if(preg_match('/Content-Encoding(.*)gzip/',$matches[1])){
			return \Http::gzdecode($matches[2]);
		}else{
			return $matches[2];
		}
	}
	
	private function parse_response_header($response_headers){
		$response_header_lines = explode("\r\n",$response_headers);
		
		preg_match('/ ([0-9]{3})/',array_shift($response_header_lines),$matches);
		$this->_response_headers['code'] = $matches[1];
		
		foreach ($response_header_lines as $header_line) {   
			list($k,$v) = explode(': ',$header_line,2);   
			if($k=='Set-Cookie') $v = $this->parse_cookie($v);
			$this->_response_headers[$k] = $v;   
		}
	}

	private function parse_cookie($str){
		$cookie = array();
		foreach(explode(';',$str) as $v){
			$tmp = explode('=',$v);
			$cookie[$tmp[0]] = $tmp[1];
		}
		return $cookie;
	}

	// 获取返回的头信息
	public function response_headers(){
		return $this->_response_headers;
	}
	
	public function __destruct(){
		curl_close($this->handel);
	}
}
