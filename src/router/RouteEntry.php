<?php

DEFINE("RESERVED_WORD_STATIC_DIR", "staticDir");
DEFINE("RESERVED_WORD_STATIC_FILE", "staticFile");
class RouteEntry {
	/**
	 *
	 * @var int the line number of the entry in the routes file
	 */
	protected $_line;
	
	/** 
	 * GET, POST, DELETE, PUT or any of those '*' 
	 * 
	 * @var string http method
	 */
	protected $_method;

	/** 
	 * The precending domain 
	 * 
	 * @var string the domain read from route
	 */
	protected $_domain;

	/** 
	 * The actual url path like /my/custom/route. The url can be regular expression
	 * 
	 * @var string http address
	 */
	protected $_path;

	/** 
	 * The responsiable controller
	 * 
	 * @var string the controller to call
	 */
	protected $_controller;

	/** 
	 * The responsiable action in the specified controller
	 * 
	 * @var string the action inside a controller to invoke
	 */
	protected $_action;
	
	/**
	 * An associative array holding the static arguments
	 * passed to the action in the config file
	 * 
	 * @var array
	 */
	protected $_staticArgs = array();
	
	/**
	 * An associative array holding the dynamic arguments
	 * supplied in the route file for this route entry
	 * @var array
	 */
	protected $_routeArgs = array();
	
	/**
	 * Holds a boolean if routes resolve to static resource or to controller
	 * @var boolean
	 */
	protected $_static;
	
	/**
	 * The dynamically created path pathern based on the path specified in the routes file
	 * @var string
	 */
	protected $_pathPattern;
	
	const ALLOWED_METHODS = 'GET|POST|PUT|DELETE|OPTIONS|HEAD|*';
	const ROUTE_LINE_MATCHER = '@^(?P<method>[^\s]+)\s+(?P<domain>[^/]+)?(?P<path>[^\s]+)\s+(?P<controller>[^\\.:]+)[\\.|:](?P<action>[^\(]+)(\((?P<staticArgs>[^\)]+)\))?@i';
	
	public function __construct($routeLine, $lineNumber = -1) {
		$this->_line = $lineNumber;
		$this->validateAndSetProperties($routeLine);
	}
	
	public function getLine() {
		return $this->_line;	
	}
	
	public function getMethod() {
		return $this->_method;
	}
	
	public function getDomain() {
		return $this->_domain;
	}
	
	public function getPath() {
		return $this->_path;
	}
	
	public function getController() {
		return $this->_controller;
	}
	
	public function getAction() {
		return $this->_action;
	}
	
	public function getStaticArgs() {
		return $this->_staticArgs;
	}
	
	/**
	 * Tells if the route entry should be handled by the static handler or controller
	 * @return boolean
	 */
	public function isStatic() {
		return $this->_static;
	}
	
	private function validateAndSetProperties($routeLine) {
		preg_match(self::ROUTE_LINE_MATCHER, $routeLine, $matches);
		
		$this->validateAndSetMethod($matches['method']);
		
		$this->_domain = $matches['domain'] == "" ? null : $matches['domain'];
		$this->_path = $matches['path'] == "" ? "/" : $matches['path'];
		$this->_controller = $matches['controller'];
		$this->_action = $matches['action'];
		
		if(isset($matches['staticArgs'])) {
			foreach(explode(",", $matches['staticArgs']) as $staticArg) {
				$parts = array_map('trim', explode(":", $staticArg));
				if(count($parts) != 2) {
					throw new RouterException("Invalid static arg syntax on line " . $this->_line . " for input " . $staticArg);
				}
				$this->_staticArgs[$parts[0]] = $parts[1];
			}
		}
		$this->_pathPattern = preg_replace("/\{([^\}]+)\}/i", "(?P<$1>[^/]+)", $this->_path);
		$this->_pathPattern = preg_replace("/\//i", "\\/", $this->_pathPattern);
		$this->_pathPattern = preg_replace("/\./i", "\\.", $this->_pathPattern);
		if($this->_controller === RESERVED_WORD_STATIC_DIR) {
			$this->_pathPattern .= '/.*\.[a-zA-Z]+';
		}
		$this->_pathPattern = '^' . $this->_pathPattern . '$';
		
		if($this->_controller === RESERVED_WORD_STATIC_DIR || $this->_controller === RESERVED_WORD_STATIC_FILE) {
			$this->_static = true;
		}		
	}
	
	private function validateAndSetMethod($method) {
		if($this->isMethodDefined($method) === false) {
			throw new RouterException("The specified method $method is invalid at line $this->_line");
		}
		
		$this->_method = $method;
	}

	public function isMethodDefined($method) {
		$allowedMethods = explode("|", self::ALLOWED_METHODS);
		foreach($allowedMethods as $aMethod) {
			if($aMethod === $method) {
				return true;
			}
		}	
		return false;
	}
	
	/**
	 * Checks if parts of http request equals this route
	 * 
	 * @param HttpRequest $request
	 * @return an associative array with the parameters extracted from the url if any parameters were defined for this route, empty array if no parameters found, otherwise null if no route matches
	 */
	public function matches(HttpRequest $request) {
		//var_dump($this->_pathPattern);
		
		if ($request->method == null || $this->_method == "*" || $this->_method == $request->method || ($this->_method == "get" && $request->method == "head")) {
			$isPathMatching = preg_match('@' . $this->_pathPattern . '@i', $request->path, $matches) === 1;
			$isHostMatching = $this->_domain == null ? true : $this->_domain === $request->domain;
			//var_dump($isPathMatching);
			//var_dump($isHostMatching);
			foreach ($matches as $key => $value) {
				if(preg_match('@^[^0-9].*$@i', $key)) {
					$request->routeArgs[$key] = $value;
				}
			}
			return ($isPathMatching && $isHostMatching);
		}
		return false;
	}
	
	/**
	 * Reverses urls from route file of the form Controller.action with the specified parameters
	 * 
	 * @param string $controllerAction the controller with the action
	 * @param array $params params needed to resolve the action (if any)
	 * @return string the reversed url or null if not matched 
	 */
	public function reverse($controllerAction, array $params = array()) { //FIXME not implemented
		$url = "/";
		echo $this->_path;
		return $url;
	}
	
	public function __toString() {
		return "$this->_domain <-> $this->_method <-> $this->_path <-> $this->_controller <-> $this->_action";
	}
}