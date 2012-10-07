<?php

class Properties {
	/**
	 * The path to the property file
	 * @var string
	 */
	private $_file;
	
	/**
	 * All properties read
	 * @var array
	 */
	private $_properties;	
	
	public function __construct($file = null) {
		if($file != null) {
			$this->load($file);
		}
	}
	
	public function getPropertyFile() {
		return $this->_file;
	}
	
	public function getProperties() {
		return $this->_properties;
	}
	
	/**
	 * Retrieves a value by its key
	 * 
	 * @param string $key the search key
	 * @return string if the key exists, the value associated with it, otherwise null
	 */
	public function getProperty($key) {
		return isset($this->_properties[$key]) ? $this->_properties[$key] : null;
	}
	
	/**
	 * Adds new property or updates the value of exisiting one. In case that a value
	 * already exists, if override is set to false, the old value will be not replaced. 
	 * 
	 * @param string $key the search key
	 * @param string $value the value to set
	 * @param boolean $override to replace the value if the key is already set. default to true
	 */
	public function addProperty($key, $value, $override = true) {
		if($override === false) {
			if(isset($this->_properties[$key]) === false) {
				$this->_properties[$key] = $value;
			}
		} else {
			$this->_properties[$key] = $value;
		}
	}
	
	public function load($file) {
		if(!file_exists($file)) {
			throw new RuntimeException("Property file does not exists: $file");
		}
		$this->_properties = array();
		$this->_file = $file;
		
		$lines = explode("\n", @file_get_contents($file));
		
		$entriesCount = count($lines);
		for($i = 0; $i < $entriesCount; $i++) {
			$line = trim($lines[$i]);
			// check if there is no comment on the line
			$charAt0 = substr($line, 0, 1);
			if($line !== '' && $charAt0 != '#' && $charAt0 != '<') {
				// trim any comments on the end of the line
				$commentPosition = stripos($line, "#");
				if($commentPosition !== false) {
					$line = substr($line, 0, $commentPosition-1);
				}
		
				$lineArray = array_map('trim', explode("=", $line));
				$key = isset($lineArray[0]) ? $lineArray[0] : "";
				$value = isset($lineArray[1]) ? $lineArray[1] : "";
				$this->_properties[$key] = $value;
			}
		}
	}
}