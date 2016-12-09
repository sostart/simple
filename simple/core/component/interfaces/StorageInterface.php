<?php
namespace core\component\interfaces;

interface StorageInterface
{

	// 初始化
	public function init();
	
	// 设置当前domain
	public static function setDomain($domain,$domain_url);
	
	// 获取当前domain
	public static function getDomain();
	
	// 获取domain url
	public static function getDomainUrl($domain);

	// 文件写入
	public static function write($destFileName, $content, $domain);

	// 文件读取
	public static function read($destFileName, $domain);
	
	// 文件上传
	public static function upload($destFileName, $srcFileName, $domain);
	
	// 获取文件URL
	public static function getUrl($destFileName, $domain);
	
	// 获取文件CDN URL
	public static function getCDNUrl($destFileName, $domain);
	
	// 删除文件
	public static function delete($destFileName, $domain);
	
	// 文件是否存在
	public static function fileExists($destFileName, $domain);
	
	// 移动文件
	public static function move($srcFileName, $destFileName, $domain);

	// 读取文件属性
	public static function getAttr($filename, $attrKey=array(), $domain);
}