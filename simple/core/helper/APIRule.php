<?php
namespace core\helper;

class APIRule
{
	// 接口规则类型
	const SIGN  = 'SIGN';  // 需要签名
	const LOGIN = 'LOGIN'; // 需要登录
	const PARAM = 'PARAM'; // 需要验证参数
	const NOREPEAT = 'NOREPEAT'; // 不能发送重复请求
	const RSIGN = 'RSIGN'; // 返回时带上签名

	/**
	 * 读取接口规则
	 *
	 * @param array $name   接口类名
	 * @param array $method 接口方法名
	 * @return array 接口规则
	 */
	public static function get($name,$method)
	{
		if(!method_exists("\\{$name}Rule",$method))
		{
			if(\Config::get('mode')===1)
			{
				\Response::error( \Response::ERR_MISAPI, "接口规则未定义:{$name}/{$method}" );		
			}
			else
			{
				return false;
			}
		}

		return call_user_func(array("{$name}Rule",$method),NULL);
	}

	/**
	 * 校验请求
	 *
	 * 校验未通过直接返回错误信息
	 *
	 * @param array $name   接口类名
	 * @param array $method 接口方法名
	 * @return null 
	 */
	public static function Check($name,$method,&$param)
	{
		// 检查API接口是否存在
		if(\Config::get('mode')===1) \APIRule::ExistsCheck($name,$method);

		// 检查接口逻辑是否已实现
		if(!method_exists(\Logic::get($name),$method)) \Response::error( \Response::ERR_MISAPI, "接口逻辑未实现:{$name}/{$method}" );		

		// 读取接口规则 并校验
		if($rules = \APIRule::get($name,$method))		
		{
			if(isset($rules[\APIRule::PARAM]))
			{
				$paramRule = $rules[\APIRule::PARAM];
				unset($rules[\APIRule::PARAM]);
			}

			// 签名校验
			if(in_array(\APIRule::SIGN,$rules)) \APIRule::SignCheck($param);

			// 登录校验
			if(in_array(\APIRule::LOGIN,$rules)) \APIRule::LoginCheck($param);
			
			// 重复请求校验
			if(in_array(\APIRule::NOREPEAT,$rules))
			{
				if(!in_array(\APIRule::SIGN,$rules)) \Response::error(ERR_SYSTEM,"重复请求校验依附于签名校验,接口规则未定义签名校验");

				\APIRule::RepeatCheck($param);
			}

			// 参数校验
			if($paramRule) 
			{	
				$rs = \APIRule::ParamCheck($paramRule,$param);
				if($rs!==true) \Response::error($rs[0],$rs[1]);
			}
		}
	}

	/**
	 * 检测API接口是否存在
	 *
	 * @param array $name   接口类名
	 * @param array $method 接口方法名
	 * @return null 
	 */
	public static function ExistsCheck(&$name,&$method)
	{
		if( !method_exists("\\{$name}API",$method) )
		{
			\Response::error( \Response::ERR_MISAPI, "请求接口不存在:{$name}/{$method}" );			
		}
	}

	/**
	 * 签名校验
	 *
	 * @param array $name   接口类名
	 * @param array $method 接口方法名
	 * @return null 
	 */
	public static function SignCheck(&$param)
	{
		$copy = $param;

		$sign = $copy['_s'];
		unset($copy['_s']);

		if( !\Common::SignCheck($sign, $copy) )
		{
			\Response::error( \Response::ERR_SIGN, '签名错误' );
		}
	}
	
	/**
	 * 登录校验
	 */
	public static function LoginCheck(&$param)
	{
		\APIRule::Check('User','loginCheck',$param);

		if( !\Logic::get('User')->loginCheck($param) )
		{
			\Response::error( \Response::ERR_NOTLOGIN, '未登录' );
		}
	}

	/**
	 * 重复请求验证
	 */
	public static function RepeatCheck(&$param)
	{
		// 必须带,必须带签名 timestamp _s
		$rs = \APIRule::ParamCheck(array(
			'timestamp'=>'required|uint',
		),$param);
		if($rs!==true) \Response::error($rs[0],$rs[1]);
		
		$time = time();
		
		// 客户端与服务端 允许5分钟时差(左右共10分钟) // 时间校准放在了登录里
		if(abs($time-$param['timestamp'])>300)
		{
			\Response::error( \Response::ERR_TIMELAG, '客户端需要与服务端进行时间校准' );
		}

		// 验证是否重复
		if(\AR::get('Norepeat')->find(array(
			'sign' => $param['_s'], // 签名已校验过了,直接查库对比
			'timestamp' => $param['timestamp']
		)))
		{
			\Response::error( \Response::ERR_REPEAT, '重复请求' );
		}

		// 清理5分钟之前的数据
		\AR::get('Norepeat')->remove(array('create_time'=>array('<',$time-300)));

		\AR::get('Norepeat')->add(array(
			'sign' => $param['_s'],
			'timestamp' => $param['timestamp'],
			'create_time'=>$time
		));
	}

	/**
	 * 参数校验
	 *
	 * @param string $rule 校验规则
	 * @param array  $param  API请求参数
	 */
	public static function ParamCheck($rules,&$param)
	{
		$instance = \Singleton::get(__CLASS__);

		foreach($rules as $field=>$rule)
		{
			if(empty($rule)) continue;
			$fieldRules = explode('|', $rule);
			
			// 没有传值或者值为空,并且不是必须字段  则跳过
			if( ( (!isset($param[$field])) || (trim($param[$field])==='') ) && !in_array('required',$fieldRules) ) continue;

			$p =& $param[$field];
			
			foreach($fieldRules as $row)
			{
				if(preg_match("/(.*?)\[(.*)\]/", $row, $match))
				{
					$ruleName = $match[1];
					$ruleSet  = $match[2];
				}
				else
				{
					$ruleName = $row;
					$ruleSet  = null;
				}
				
				if(is_callable($ruleName))
				{
					$_t = array(); $_t[] = $p; if($ruleSet) $_t[] = $ruleSet;
					
					if(!call_user_func_array($ruleName,$_t))
					{
						return array(\Response::ERR_PARAM, '参数校验错误'."{$field} - {$ruleName}" );
					}
				}
				elseif(method_exists($instance,$ruleName))
				{
					if(!$instance->$ruleName($p,$ruleSet))
					{
						return array( \Response::ERR_PARAM, '参数校验错误 '."{$field} - {$ruleName}" );
					}
				}
				else
				{
					return array( \Response::ERR_PARAM, '参数校验错误-system' );
				}
			}
		}

		return true;
	}

	// --------------------------

	/**
	 * 必须验证
	 *
	 * @param string $str 
	 * @param boolean
	 */
	public function required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}

	/**
	 * 正则验证
	 *
	 * @param string $str 
	 * @param boolean
	 */
	public function match($str, $regex)
	{
		if ( ! preg_match($regex, $str))
		{
			return FALSE;
		}
		return  TRUE;
	}
	
	/**
	 * 长度验证
	 *
	 * @param string $str 
	 * @param boolean
	 */
	public function length($str,$val)
	{
		if(!is_string($str)) return false;
		
		list($min,$max) = explode(',',$val);
		$strlen = mb_strlen($str,'UTF8');

		if($max==NULL)
		{
			return $strlen==$min;
		}
		else
		{
			return ($strlen>=$min) && ($strlen<=$max);
		}
	}

	/**
	 * 邮件验证
	 *
	 * @param string $str 
	 * @param boolean
	 */
	public static function email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	/**
	 * 手机验证
	 *
	 * @param string $str 
	 * @param boolean
	 */
	public static function mobile($str)
	{
		return preg_match('/^((1[3,5][0-9])|(14[5,7])|(18[^4]))\d{8}$/',$str);
	}
	 
	/**
	 * 整数验证
	 *
	 * @param string $str 
	 * @param boolean
	 */
	public static function int($str)
	{
		return is_numeric($str) && ceil($str)==$str;
	}

	/**
	 * 无符号整数验证
	 *
	 * @param string $str 
	 * @param boolean
	 */
	public static function uint($str)
	{
		return is_numeric($str) && ceil(abs($str))==$str;
	}

	/**
	 * 枚举验证
	 *
	 * @param string $str 
	 * @param boolean
	 */
	public static function enum($str,$val)
	{
		return in_array($str,explode(',',$val));
	}

}
