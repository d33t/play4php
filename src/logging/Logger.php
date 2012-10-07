<?php
define("DEBUG", "DEBUG");
define("INFO", "INFO");
define("WARN", "WARN");
define("ERROR", "ERROR");

class Logger {
	protected $_file;
	protected $_className;
	protected $_level;
	
	public function __construct($className, $level = null){
		$this->_className = $className;
		
		$config = ConfigManager::getInstance();
		
		$this->_level = $level == null ? ($config->isDev() ? DEBUG : INFO) : $level;
		$this->_file = $config->getRootPath() . DS . $config->getValue(ConfigManager::LOGFILE_KEY);
		date_default_timezone_set(LOCAL_TIME_ZONE);
	}
	
	public function debug($message) {
		if($this->_level == DEBUG) {
			$this->write(DEBUG, $message);
		}
	}
	
	public function info($message) {
		if($this->_level == DEBUG || $this->_level == INFO) {
			$this->write(INFO, $message);
		}
	}
	
	public function warn($message) {
		if($this->_level == DEBUG || $this->_level == INFO || $this->_level == WARN) {
			$this->write(WARN, $message);
		}
	}
	
	public function error($message) {
		$this->write(ERROR, $message);
	}
	
	private function write($level, $message) {
		$fileHandle = fopen($this->_file, "a");
		fwrite($fileHandle, date('d-m-Y H:i:s') . " [" . $this->_className . ":" . $level ."] " . $message . "\n");
		fclose($fileHandle);
	}
}