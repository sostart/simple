<?php
namespace core\component;

class SaeStorageComponent implements interfaces\StorageInterface
{
	public static $domain = '';
	public static $domain_url = '';

	// 初始化
	public function init()
	{
		static::setDomain($this->domain);
	}
	
	// 设置domain
	public static function setDomain($domain,$domain_url=FALSE)
	{
		static::$domain = $domain;
		static::$domain_url = $domain_url ? $domain_url : \Singleton::Get('\SaeStorage')->getUrl(static::$domain,'');
		return true;
	}

	// 获取当前domain
	public static function getDomain()
	{
		return static::$domain;
	}
	
	// 获取domain url
	public static function getDomainUrl($domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return \Singleton::Get('\SaeStorage')->getUrl($domain,'');
	}
	
	// 文件写入
	public static function write($destFileName, $content, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return \Singleton::Get('\SaeStorage')->write($domain, $destFileName, $content);
	}

	// 文件读取
	public static function read($destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return \Singleton::Get('\SaeStorage')->read($domain, $destFileName);
	}
	
	// 文件上传
	public static function upload($destFileName, $srcFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return \Singleton::Get('\SaeStorage')->upload($domain, $destFileName, $srcFileName);
	}
	
	// 获取文件URL
	public static function getUrl($destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return \Singleton::Get('\SaeStorage')->getUrl($domain, $destFileName);
	}
	
	// 获取文件CDN URL
	public static function getCDNUrl($destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return static::getUrl($destFileName, $domain);
	}
	
	// 删除文件
	public static function delete($destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return \Singleton::Get('\SaeStorage')->delete($domain, $destFileName);
	}
	
	// 文件是否存在
	public static function fileExists($destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return \Singleton::Get('\SaeStorage')->fileExists($domain, $destFileName);
	}
	
	// 移动文件
	public static function move($srcFileName, $destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		 
		// @tip 读取后再写入, 看看是否有替代方法
		if(static::write($destFileName, static::read($srcFileName, $domain), $domain))
		{
			return static::delete($srcFileName, $domain);
		}

		return FALSE;
	}

	// 读取文件属性
	public static function getAttr($filename, $attrKey=array(), $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return \Singleton::Get('\SaeStorage')->getAttr($domain, $filename, $attrKey);
	}
}