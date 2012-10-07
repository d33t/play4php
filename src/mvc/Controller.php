<?php

/**
 * Implements all basic controller functions
 * 
 */
class Controller {
	protected $_config;
	
	protected $_content;
	
	protected $_router;
	
	protected $logger;
	
	private $_defaultTemplate;
	
	public function __construct() {
		$this->logger = new Logger(get_class($this));
		$this->_config = ConfigManager::getInstance();
		$this->_defaultTemplate = null;
	}
	
	public function setDefaultTemplate($templateName = "main.tpl") {
		$this->_defaultTemplate = $templateName;
	}
	
	public function render($arg = null, array $params = array(), $returnResult = false) {
		if(is_array($arg) === true && empty($params) === true) {
			$params = $arg;
			$arg = null;
		}
		$template = null;
		if($arg != null) {
			$template = $arg;
		} else if($this->_defaultTemplate != null) {
			$template = $this->_defaultTemplate;
		}
		$result = TemplateLoader::getInstance()->load($template, $params);
		if(!$returnResult) {
			echo $result;
		} else {
			return $result;
		}
		exit();
	}
	
	/**
	 * Renders 404 page
	 * @param string $why
	 */
	protected function notfound($why) {
		throw new NotFoundException($why);
	}
	
	/**
	 * Redirects to the given destination. The destinatio can be of the form Controller.action
	 * or full (http://somehost.com/path/to/resource) or relative path (/some/public/path/to/resource) url.
	 * 
	 * @param string $destination the destination
	 * @param array $params the parameters needed to resolve the url if the destination is in the form Controller.action
	 * @param int $status the http status to sent. Default is 302
	 * @throws RedirectException
	 */
	protected function redirect($destination, array $params = array(), $status = null) {
		throw new RedirectException($destination, $params, $status);
	}
}