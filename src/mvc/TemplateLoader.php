<?php
class TemplateLoader {
	/**
	 * The ingleton class instance
	 * @var TemplateLoader
	 */
	private static $_instance = null;
	
	/**
	 * The underlying template engine
	 * @var Smarty
	 */
	protected $_templateEngine;
	
	/**
	 * The application configuration manager
	 * 
	 * @var ConfigManager
	 */
	protected $_config;
	
	/**
	 * 
	 * @var Logger
	 */
	private $logger;
	
	private function __construct() {
		$this->logger = new Logger(get_class($this));
		$this->_config = ConfigManager::getInstance();
		$this->_templateEngine = new Smarty();
		
		if($this->_config->isDev()) {
			$this->_templateEngine->debugging = false;
			$this->_templateEngine->caching = Smarty::CACHING_OFF;
			$this->_templateEngine->force_compile = true;
		} else {
			$this->_templateEngine->debugging = false;
			$this->_templateEngine->caching = Smarty::CACHING_LIFETIME_CURRENT;
			$this->_templateEngine->cache_lifetime = -1;
		}
		
		$this->_templateEngine->setTemplateDir($this->_config->getViewsPath() . DS);
		$this->_templateEngine->setCompileDir($this->_config->getTempPath() . DS . "views" . DS . "templates_c" . DS);
		$this->_templateEngine->setCacheDir($this->_config->getTempPath() . DS . "views" . DS . "cache" . DS);
		$this->_templateEngine->setConfigDir($this->_config->getConfigPath() . DS . "templates" . DS);
		$this->_templateEngine->setPluginsDir(SMARTY_PLUGINS_DIR);
		//$this->_smarty->testInstall();
	}
	
	public static function getInstance() {
		if(self::$_instance == null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function load($template, array $params = array()) {
		if($template == null) {
			throw new TemplateException("No template set!");
		}
		$this->logger->debug(" ***LOADING " . $template . " *** ");
		if(empty($params) === false){
			foreach($params as $paramName => $paramValue) {
				$this->_templateEngine->assign($paramName, $paramValue);
			}
		}
		$this->_templateEngine->display($template);
	}
}