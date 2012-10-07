<?php

/**
 * Provides basic autoloading features
 *
 */
class Autoloader {
	/**
	 * The files extensions containing classes to autoload
	 *
	 * @var string
	 */
	const CLASS_EXTENSIONS = "php|class.php";
	
	/**
     * @var Autoloader
     */
    protected static $_instance = null;

    /**
     * defines all directories that include source code
     * @var array
     */
    protected $_paths = null;
    
    /**
     * 
     * @var array
     */
    private $_classExtensions;
    
	/**
     * Private constructor for a singelton implementation
     */
    private function __construct() {
    	if(!defined('PATH_ROOT')) {
    		throw new RuntimeException("The constant PATH_ROOT is not defined!");
    	}
    	if(!defined('PATH_CONFIG')) {
    		throw new RuntimeException("The constant PATH_CONFIG is not defined!");
    	}
    	if(!defined('PATH_LIBRARIES')) {
    		throw new RuntimeException("The constant PATH_LIBRARIES is not defined!");
    	}
    	
    	require_once (PATH_LIBRARIES . DS . "play4php" . DS . "configuration" . DS . "ConfigManager.php");
    	require_once (PATH_LIBRARIES . DS . "play4php" . DS . "configuration" . DS . "Properties.php");
    	$config = ConfigManager::getInstance();
    	$this->_paths = array($config->getControllersPath(), $config->getLibrariesPath(), $config->getModelsPath());
    	$this->checkPaths($this->_paths);
    	$this->_classExtensions = explode("|", self::CLASS_EXTENSIONS);
    }
    
	/**
     * Gets Autoloader instance
     *
     * @return Autoloader
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function addSourceDirs(array $paths = array()) {
    	$this->checkPaths($paths);
    	$this->_paths = array_merge($this->_paths, $paths);
    }
    
	/**
     * Registers autoloader (SPL)
     * 
     * @param array $paths
     */
    public function registerAutoloader() {
        spl_autoload_register(array($this, 'autoload'));
    }
    
    /**
     * Do the autoloading
     *
     * @param string $class
     * @return bool
     */
    private function autoload($class) {
        if($this->_paths != null) {
        	// case sensitive
        	foreach($this->_paths as $path) {
        		// see in the current directory
        		$classFile = $this->findClass($path, $class);
        		if($classFile != null) {
        			require_once $classFile;
        			return true;
        		}
        	}
        }
        throw new RuntimeException("Cannot autoload a class of type " . $class);
    }
    
    private function findClass($root, $class) {
    	//check if class file is in the current directory
    	foreach ($this->_classExtensions as $extension) {
    		$classFile = $root . DS . $class . "." . $extension;
    		if(file_exists($classFile)) {
    			return $classFile;
    		}
    		// case insensitive
    		$classFile = $root . DS . strtolower($class) . "." . $extension;
    		if(file_exists($classFile)) {
    			return $classFile;
    		}
    	}
    	
    	// list all subdirectories and check if the file exists there
    	if ($handle = opendir($root)) {
    		while (false !== ($entry = readdir($handle))) {
    			if (is_dir($root . DS . $entry) && substr($entry, 0, 1) != ".") {
    				$classFileFound = $this->findClass($root . DS . $entry, $class);
    				if($classFileFound != null) {
    					closedir($handle);
    					return $classFileFound;
    				}
    			}
    		}
    		closedir($handle);
    	}
    	return null;
    }
    
    /**
     * Check if the given paths exists and are valid directories
     * 
     * @param array $paths
     * @throws RuntimeException if path doesn't exists or doesn't point to directory
     */
    private function checkPaths(array $paths) {
    	foreach($paths as $path) {
    		if(!file_exists($path) || !is_dir($path)) {
    			throw new RuntimeException("The given directory '{$path}' does not exists or is not a directory.");
    		}
    	}
    }
}