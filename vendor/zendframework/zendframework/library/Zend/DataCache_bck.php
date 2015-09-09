<?php
/*
 * 
 * Copyright 2014 Ritesh <riteshk@clavax.us> Created on 15-04-2014
 * 
 * 
 */
 
namespace Zend;

use Zend\Cache\StorageFactory;

abstract class DataCache 
{
	private static $timeLine = 60;    // cache expire time (in secs)
	private static $name = 'dbtable';  // namespace for cache
	private static $adapter = 'memcache';   // Cache adapter to be used Apc
	public static $debug = false;
	
	private static function factory()
	{
		$cache   = \Zend\Cache\StorageFactory::factory(array(
			'adapter' => array(
				'name' => static::$adapter,
				'options' => array('namespace' => static::$name, 'ttl' => static::$timeLine)   // ttl is expire time of cache (time is in seconds)
			),
			'plugins' => array(
				// Throw exceptions on cache errors
				'exception_handler' => array(
					'throw_exceptions' => static::$debug
				),
				'Serializer'
			)
		));
		return $cache;
	}
	
	/* Function to fetch cached data */
	public static function getData($key)
	{
		if (is_array($key)) {
			$results = static::factory()->getItems($key);
			return (count($results)>0)?$results:false;
		} else {
			$results = static::factory()->getItem($key, $success);
			return (!$success)?false:$results;
		}
	}
	
	/* Function to update cached data */
	public static function updateData($key, $records)
	{
		if (count($records)>0) {
			$data = array();
			foreach ($records as $record) {
				$temp = array();
				$record = (is_object($record))?get_object_vars($record):$record;
				foreach ($record as $k=>$v) {
					$temp[$k] = $v;
				}
				$data[] = $temp;
			}
			(static::factory()->hasItem($key))?static::factory()->replaceItem($key, $data):static::factory()->setItem($key, $data);
		}
	}
	
	/* Function to remove cached data */
	public static function removeData($key)
	{
		return (is_array($key))?static::factory()->removeItems($key):static::factory()->removeItem($key);
	}
	
	/* Function to reset timeline of cached data */
	public static function resetData($key)
	{
		return (is_array($key))?static::factory()->touchItems($key):static::factory()->touchItem($key);
	}
	
	/* Function to check existence of cached data */
	public static function isExistData($key)
	{
		return (is_array($key))?static::factory()->hasItems($key):static::factory()->hasItem($key);
	}
	
	/* Function to set timeline of cached data */
	public static function setCacheTime($time)
	{
		static::$timeLine = $time;
	}
	
	/* Function to set namespace */
	public static function setCacheNameSpace($name)
	{
		static::$name = $name;
	}
	
	/* Function to change Cache adapter */
	public static function changeCacheAdapter($adapter)
	{
		static::$adapter = $adapter;
	}
}
