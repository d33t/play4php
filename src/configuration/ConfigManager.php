<?php

/**
 * Configuration manager provides access to the app system basic configuration
 *
 */
class ConfigManager {
	/**
	 * The singleton class instance
	 * 
	 * @var ConfigManager
	 */
	private static $_instance = null;
	
	/**
	 * The application mode. This could be dev or prod
     * @var string Environment
     */
	const ENVIRONMENT_KEY = "application.environment";

    /**
     * @var string Root path
     */
    const ROOT_KEY = "application.root";
    
    /**
     * @var string Libraries path relative from the application root
     */
    const LIBRARIES_KEY = "application.libraries";
    
    /**
     * @var string The path to the application mvc relative to the application root
     */
    const APP_KEY = "application.mvc";
    
    /**
     * @var string Temp path relative from the application root
     */
    const TEMP_KEY = "application.temp";
    
    /**
     * @var string Application config path relative from the application root
     */
    const CONFIG_KEY = "application.config";
    
    /**
     * @var string Data path relative from the application root
     */
    const DATA_KEY = "application.data";
    
    /**
     * @var string Views path relative from the application app folder
     */
    const VIEWS_KEY = "application.views";
    
    /**
     * @var string Models path relative from the application app folder
     */
    const MODELS_KEY = "application.models";
    
    /**
     * @var string Controllers path relative from the application app folder
     */
    const CONTROLLERS_KEY = "application.controllers";
    
    /**
     * The application log file path relative from the root
     * @var string
     */
    const LOGFILE_KEY = "application.logfile";
    
    const BASE_DOMAIN = "base.domain";
    
    /**
     * Development mode
     *
     * @var string
     */
    const ENV_DEVELOPMENT = "dev";
    
    /**
     * Production mode
     * @var string
     */
    const ENV_PRODUCTION = "prod";
    
	/**
	 * The application config
	 * 
	 * @var Properties The config file
	 */
    private $_properties;
    
    
	/**
     * Config Manager constructor.
     * 
     */
    private function __construct() {
    	try {
    		$this->_properties = new Properties(PATH_CONFIG . DS . "application.config.php");
    		$this->initEnvironment();
    	} catch(Exception $e) {
    		throw new InternalErrorException($e->getMessage());
    	}
    	
    	if($this->getValue(self::ENVIRONMENT_KEY) === "") {
    		throw new RuntimeException(self::ENVIRONMENT_KEY . " must be set in the application.config.php");
    	}
    }
    
    public static function getInstance() {
    	if(self::$_instance == null) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    }
    
    public function initEnvironment() {    	
    	//the next 3 paths are already checked for existance in Autoloader
    	$this->setRootPath(PATH_ROOT);
    	$this->setConfigPath(PATH_CONFIG);
    	$this->setLibrariesPath(PATH_LIBRARIES);
    	
    	if(defined('PATH_APP')) {
    		$this->setAppPath(PATH_APP);
    	} else {
    		$this->setAppPath($this->_properties->getProperty(self::APP_KEY));
    	}
    	
    	if(defined('PATH_TEMP')) {
    		$this->setTempPath(PATH_TEMP);
    	} else {
    		$this->setTempPath($this->_properties->getProperty(self::TEMP_KEY));
    	}
    	
    	if(defined('PATH_DATA')) {
    		$this->setDataPath(PATH_DATA);
    	} else {
    		$this->setDataPath($this->_properties->getProperty(self::DATA_KEY));
    	}
    	
    	if(defined('PATH_MODELS')) {
    		$this->setModelsPath(PATH_MODELS);
    	} else {
    		$this->setModelsPath($this->_properties->getProperty(self::MODELS_KEY));
    	}
    	
    	if(defined('PATH_VIEWS')) {
    		$this->setViewsPath(PATH_VIEWS);
    	} else {
    		$this->setViewsPath($this->_properties->getProperty(self::VIEWS_KEY));
    	}
    	
    	if(defined('PATH_CONTROLLERS')) {
    		$this->setControllersPath(PATH_CONTROLLERS);
    	} else {
    		$this->setControllersPath($this->_properties->getProperty(self::CONTROLLERS_KEY));
    	}
    }
    
    /**
     * Returns a value from the config file or empty string if nothing found.
     *
     * @param string $key The key that is going to be selected
     * @return string the value if the key exists, otherwise empty string
     */
    public function getValue($key) {
    	$value = $this->_properties->getProperty($key);
    	return $value == null ? "" : $value;
    }
    
	/**
     * Gets an environment.
     *
     * @return string
     */
    public function getEnvironment() {
        return $this->getValue(self::ENVIRONMENT_KEY);
    }
    
    public function isDev() {
    	return $this->getEnvironment() === self::ENV_DEVELOPMENT;	
    }
    
    public function isProd() {
    	return $this->getEnvironment() === 	self::ENV_PRODUCTION;
    }
    
	/**
     * Gets a local root path.
     *
     * @return string
     */
    public function getRootPath() {
        return $this->getValue(self::ROOT_KEY);
    }
    
    /**
     * Gets the path to the application root mvc directory.
     *
     * @return string
     */
    public function getAppPath() {
    	return $this->getValue(self::APP_KEY);
    }
    
    /**
     * Gets the path to the temp directory.
     *
     * @return string
     */
    public function getTempPath() {
    	return $this->getValue(self::TEMP_KEY);
    }
    
	/**
     * Gets a library base path.
     *
     * @return string
     */
    public function getLibrariesPath() {
        return $this->getValue(self::LIBRARIES_KEY);
    }
    
	/**
     * Gets a main configs path.
     *
     * @return string
     */
    public function getConfigPath() {
        return $this->getValue(self::CONFIG_KEY);
    }
    
	/**
	 * Gets data files path.
	 * 
	 * @return string
	 */
	public function getDataPath() {
		return $this->getValue(self::DATA_KEY);
	}
    
	/**
	 * Returns the basic path to the controller classes.
	 * 
	 * @return string
	 */
	public function getControllersPath() {
		return $this->getValue(self::CONTROLLERS_KEY);
	}
	
	public function getViewsPath() {
		return $this->getValue(self::VIEWS_KEY);
	}
	
	public function getModelsPath() {
		return $this->getValue(self::MODELS_KEY);
	}
	
	/**
	 * Returns the base url that can be used as prefix
	 * before each url.
	 *
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->_properties->getProperty("base.url");
	}
	
	/**
     * Sets an environment.
     *
     * @param string $environment
     * @return string
     */
    public function setEnvironment($environment) {
    	if($environment !== self::ENV_DEVELOPMENT || $environment !== self::ENV_PRODUCTION) {
    		throw new RuntimeException("Invalid environment mode. Check the config setting " . self::ENVIRONMENT_KEY);
    	}
    	$this->_properties->addProperty(self::ENVIRONMENT_KEY, $environment);
    }
    
	/**
     * Sets the path to the root directory
     *
     * @param string $rootPath
     * @return void
     */
    public function setRootPath($rootPath) {
    	$this->_properties->addProperty(self::ROOT_KEY, $rootPath);
    }
    
    /**
     * Sets config files path.
     *
     * @param string $configPath
     * @return void
     */
    public function setConfigPath($configPath) {
    	$this->_properties->addProperty(self::CONFIG_KEY, $configPath);
    }
    
    /**
     * Sets rooth path of libraries.
     *
     * @param string $libraryPath
     * @return void
     */
    public function setLibrariesPath($librariesPath) {
    	$this->_properties->addProperty(self::LIBRARIES_KEY, $librariesPath);
    }
    
    /**
     * Sets the path to the app mvc directory
     *
     * @param string $rootPath
     * @return void
     */
    public function setAppPath($appPath) {
    	$this->setPropertyPath(self::APP_KEY, $appPath, self::ROOT_KEY);
    }
    
    /**
     * Sets the full path to the temp directory
     *
     * @param string $rootPath
     * @return void
     */
    public function setTempPath($tempPath) {
    	$tempPath = $this->setPropertyPath(self::TEMP_KEY, $tempPath, self::ROOT_KEY);
    	if(!file_exists($tempPath)) {
    		if (!mkdir($tempPath . "/views/cache", 0770, true) 
    				|| !mkdir($tempPath . "/views/templates_c", 0770, true)) {
    			die('Failed to create temporary folders...');
    		}
    	}
    }
	
	/**
	* Sets the path to the data files.
	 * 
	 * @param string $dataPath
	 * @return void
	 */
	public function setDataPath($dataPath) {
		$this->setPropertyPath(self::DATA_KEY, $dataPath, self::ROOT_KEY);
	}
	
	/**
	 * Sets the path to the controller files.
	 *
	 * @param string $dataPath
	 * @return void
	 */
	public function setControllersPath($controllersPath) {
		$this->setPropertyPath(self::CONTROLLERS_KEY, $controllersPath, self::APP_KEY);
	}
	
	/**
	 * Sets the path to the views files.
	 *
	 * @param string $viewsPath
	 * @return void
	 */
	public function setViewsPath($viewsPath) {
		$this->setPropertyPath(self::VIEWS_KEY, $viewsPath, self::APP_KEY);
	}
	
	/**
	 * Sets the path to the models files.
	 *
	 * @param string $modelsPath
	 * @return void
	 */
	public function setModelsPath($modelsPath) {
		$this->setPropertyPath(self::MODELS_KEY, $modelsPath, self::APP_KEY);
	}
	
	/**
	 * Sets the base url.
	 * 
	 * @param string $baseUrl i.e. /apps/xyz/foo/bar
	 * @return void
	 */
	public function setBaseUrl($baseUrl) {
		$this->_properties->addProperty("base.url", $baseUrl);
	}
	
	/**
	 * 
	 * @param string $propertyKey
	 * @return the new property value
	 */
	private function setPropertyPath($propertyKey, $propertyValue, $basePathKey) {
		if($propertyValue == null) { //reset property value
			$propertyValue = "";
		} else { 
			if(substr($propertyValue, 0, 1) === "/") {
				$propertyValue = substr($propertyValue, 1);
			}
			$propertyValue = $this->_properties->getProperty($basePathKey) . DS . $propertyValue;
		}
		
		$this->_properties->addProperty($propertyKey, $propertyValue);
		return $propertyValue;
	}
}