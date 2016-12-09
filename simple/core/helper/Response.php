<?php
namespace core\helper;
/**
 * 数据返回接口类
 *
 *
 * @author LM <sostart.net@gmail.com>
 * @package system
 * @since 1.0
 */
class Response
{
	const ERR_NOTLOGIN = 1; // 未登录
	const ERR_SIGN   = 2; // 签名错误
	const ERR_PARAM  = 3; // 参数错误
	const ERR_MISAPI = 4; // 接口不存在
	const ERR_SYSTEM = 5; // 服务端开发阶段错误
	const ERR_REPEAT = 6; // 重复请求
	const ERR_TIMELAG= 7; // 重复请求 时间校正 客户端与服务端时间不一致
	
	public static $data=array( 
		'_c'=>0, // 状态码
		'_d'=>''
	);
	
	public static $sign = false;
	
	// 格式化返回错误信息
	public static function error($code,$msg)
	{
		\Component::get('response')->send(array('_c'=>$code,'_m'=>$msg));
	}
	
	// 接口返回数据需要签名
	public static function needSign()
	{
		\Response::$sign = true;
	}
	
	// 设置返回数据
	public static function setData($data)
	{
		\Response::$data['_d'] = $data;
	}
	
	// 设置返回消息
	public static function setMsg($msg)
	{
		\Response::$data['_m'] = $msg;
	}

	// 返回调试信息
	public static function debug()
	{
		if(\Config::get('debug')) \Response::$data['_debug'][] = func_get_args();
	}
	
	// 获取返回信息
	public static function getData()
	{
		
		// \Response::$sign  \APIRule::RSIGN
		// 暂时关闭数字签名  \Common::Sign(\Response::$data),

		return \Response::$data;
	}
	
	public static function send()
	{
		if(func_num_args()) \Response::setData(func_get_arg(0));
		\Component::get('response')->send( \Response::getData() );
	}
}