<?php
namespace core\component;

class BaseStorageComponent implements interfaces\StorageInterface
{
	public static $domain = './';
	public static $domain_url = 'http://www.example.com/';

	// 初始化
	public function init()
	{
		static::setDomain($this->domain,$this->domain_url);
	}
	
	// 设置domain
	public static function setDomain($domain,$domain_url=FALSE)
	{
		Static::$domain = $domain;
		Static::$domain_url = $domain_url;
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
		return static::$domain_url;
	}
	
	// 文件写入
	public static function write($destFileName, $content, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		
		if(static::_mkdir($destFileName, $domain))
		{
			if(@file_put_contents($domain.$destFileName, $content)!==FALSE)
			{
				return static::getCDNUrl($destFileName, $domain);
			}
		}

		return FALSE;
	}

	// 文件读取
	public static function read($destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return @file_get_contents($domain.$destFileName);
	}
	
	// 文件上传
	public static function upload($destFileName, $srcFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		if(static::_mkdir($destFileName, $domain))
		{
			if(@move_uploaded_file($srcFileName, $domain.$destFileName))
			{
				return static::getCDNUrl($destFileName, $domain);
			}
		}
		return FALSE;
	}
	
	// 获取文件URL
	public static function getUrl($destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return static::getDomainUrl($domain).$destFileName;
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
		if(static::fileExists($destFileName,$domain))
		{
			return @unlink($domain.$destFileName);
		}
		return true;
	}
	
	// 文件是否存在
	public static function fileExists($destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		return file_exists($domain.$destFileName);
	}

	// 移动文件
	public static function move($srcFileName, $destFileName, $domain=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		if(static::_mkdir($destFileName, $domain))
		{
			return @rename($domain.$srcFileName, $domain.$destFileName);
		}
		return FALSE;
	}

	// 自动创建文件目录
	protected static function _mkdir($destFileName, $domain=FALSE, $isdir=FALSE)
	{
		$domain = $domain ? $domain : static::$domain;
		$dir = $isdir ? $destFileName : substr($destFileName,0,strrpos($destFileName,'/'));
		return !is_dir($domain.$dir) ? (static::_mkdir(dirname($dir), $domain, TRUE) && @mkdir($domain.$dir, 0777)) : TRUE;
	}

	// 读取文件属性
	public static function getAttr($filename, $attrKey=array(),  $domain=FALSE)
	{
		return false; // @todo 
	}
}