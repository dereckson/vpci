<?php

class Config
{
	// Relative file name of the config file that will be used
	public static $file = "config_data.json";

	// The default distributed config file.  Only to be used to restore the config file
	// back to its original settings
	public static $default = "config_data.default.json";

	/**
	 * Retrieves and returns all config data in the config JSON file
	 * @return Array An associative array of all data in the config JSON file
	 */
	public static function get_all()
	{
		if(!file_exists(__DIR__."/".self::$file)) {
			self::reset_defaults();
		}
		
		$json_data = file_get_contents(__DIR__."/".self::$file);
		
		if(empty($json_data))
		{
			self::reset_defaults();
		}
		$data = json_decode($json_data, true);
		return $data;
	}

	/**
	 * Retrieves a specific data value from the JSON config file with a given key
	 * @param  string $key The key that is associated with the desired config data
	 * @return string      The data value paired with the given key
	 */
	public static function get($key)
	{
		return self::get_all()[$key];
	}

	/**
	 * Determines if a key exists within the config file
	 * @param  string  $key The $key being checked
	 * @return boolean      true if $key exisits in the config file - false otherwise
	 */
	public static function is_key($key)
	{
		return array_key_exists($key, self::get_all());
	}

	/**
	 * Sets the value for a given key or adds the key value pair if the key
	 * did not previously exsist.
	 * @param string $key   The key that is associated with the desired config data
	 * @param string $value The value to be paired with $key
	 */
	// public static function set($key, $value)
	// {
	// 	$data = self::get_all();
	// 	$data[$key] = $value;
	// 	file_put_contents(self::$file, json_encode($data, JSON_PRETTY_PRINT));
	// }

	/**
	 * Restores the config file ("config_data.json") to its release settings
	 */
	public static function reset_defaults()
	{
		$json_data = file_get_contents(__DIR__."/".self::$default);
		file_put_contents(__DIR__."/".self::$file, $json_data);
	}

	/**
	 * Function returns a memcache server
	 * 		Used in MCCache object creation
	 * @return Memcache A memcache object to be used by the MCCache interface
	 */
	public static function getMemcache()
	{
		$cache = new Memcache();
		$servers = self::get("memcacheServers");

		foreach ($servers as $s) {
			$cache->addServer($s['host'], $s['port']);
		}
		return $cache;
	}


	//////////////////////////////////////////////////
	// Object Data 
	//////////////////////////////////////////////////

	/**
	 * Config data that will be used at runtime
	 * @var array
	 */
	private $config;
	private $persistent;

	/**
	 * Loads the config data out of config_data.json into
	 * a new config object.  This is used to reduce reads
	 * writes to file.
	 * @param Array $config_data Optional config data to add or overwrite default 
	 *                           WARNING: This data will be saved on object destruction
	 */
	public function __construct($config_data = [], $persistent = true)
	{
		$this->config = self::get_all();

		foreach ($config_data as $key => $value) {
			$this->config[$key] = $value;
		}

		$this->persistent = $persistent;
	}

	/**
	 * Writes any changes made durring the object's lifespan to file
	 * 
	 * WARNING: Data in the config_data.json file will be overwritten to
	 * 			represent the current state of this data object.
	 */
	public function __destruct()
	{
		if($this->persistent) {
			file_put_contents(__DIR__."/".self::$file, json_encode($this->config, JSON_PRETTY_PRINT));
		}
	}

	/**
	 * Returns all runtime config data
	 * @return array an associative array of all config data
	 */
	public function getConfigData()
	{
		return $this->config;
	}

	/**
	 * Returns a specific config data value
	 * @param  string $key the config data key
	 * @return string      the runtime config data value
	 */
	public function getConfigVal($key)
	{
		if(!$this->isConfigKey($key)) {
			return null;
		}
		return $this->config[$key];
	}

	/**
	 * Checks if a given key exists within the runtime config data
	 * @param  string  $key the config data key
	 * @return boolean      true if the key exists, false otherwise
	 */
	public function isConfigKey($key)
	{
		return array_key_exists($key, $this->config);
	}

	/**
	 * Sets the value for a given key or adds the key value pair if the key
	 * did not previously exsist.
	 *
	 * WARNING: Data will not be saved to the file until object destruction
	 * 
	 * @param string $key   The key that is associated with the desired config data
	 * @param string $value The value to be paired with $key
	 */
	public function setConfigValue($key, $value)
	{
		$this->config[$key] = $value;
	}
}