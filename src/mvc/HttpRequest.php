<?php
/**
 *
 * @created 03.02.2012
 * @author Rusi Rusev <Sociographic UG>
 */
class HttpRequest {
	
	/**
	 * Request path
	 * 
	 * @var string
	 */
	public $path;
	
	/**
	 * QueryString
	 * 
	 * @var string
	 */
	public $querystring;
	
	/**
	 * Full url
	 * 
	 * @var string
	 */
	public $url;
	
	/**
	 * HTTP method
	 * 
	 * @var string
	 */
	public $method;
	
	/**
	 * Server domain
	 * 
	 * @var string
	 */
	public $domain;
	
	/**
	 * Client address
	 * 
	 * @var string
	 */
	public $remoteAddress;
	
	/**
	 * Request content-type
	 * 
	 * @var string
	 */
	public $contentType;
	
	/**
	 * This is the encoding used to decode this request.
	 * If encoding-info is not found in request, then utf-8 is used
	 * 
	 * @var string
	 */
	public $encoding = "utf-8";
	
	/**
	 * Controller to invoke
	 * 
	 * @var string
	 */
	public $controller;
	
	/**
	 * Action within the controller to invoke
	 *  
	 * @var string
	 */
	public $actionMethod;
	
	/**
	 * HTTP port
	 * 
	 * @var int
	 */
	public $port;
	
	/**
	 * is HTTPS ?
	 * 
	 * @var bool
	 */
	public $secure = false;
	
	/**
	 * HTTP Headers
	 * 
	 * @var array
	 */
	public $headers = null;
	
	/**
	 * HTTP Cookies
	 * 
	 * @var array
	 */
	public $cookies = null;

	/**
	 * Additional HTTP params extracted from route
	 * 
	 * @var array
	 */
	public $routeArgs = array();
	
	/**
	 * Format (html,xml,json,text)
	 * 
	 * @var string
	 */
	public $format = null;
	
	/**
	 * Free space to store your request specific data
	 * 
	 * @var array
	 */
	public $args;
	
	/**
	 * When the request has been received
	 * 
	 * @var date
	 */
	public $date;
	
	/**
	 * New request or already submitted
	 * 
	 * @var bool
	 */
	public $isNew = true;
	
	/**
	 * HTTP Basic User
	 * 
	 * @var string
	 */
	public $user;
	
	/**
	 * HTTP Basic Password
	 * 
	 * @var string
	 */
	public $password;
	
	/**
	 * Request comes from loopback interface
	 * 
	 * @var bool
	 */
	public $isLoopback;

	/**
	 * The responsible route entry
	 * 
	 * @var RouteEntry
	 */
	public $routeEntry = null;
	
	public function __construct($domain) {
		$this->domain = $domain;
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->date = getdate();
		preg_match('@^(?P<path>[^\?]+)(\?(?P<query>.*))?$@i', $_SERVER['REQUEST_URI'], $matches);
		$this->path = $matches['path'];
		$this->querystring = isset($matches['query']) ? $matches['query'] : '';
		$this->secure = StringUtils::startsWith($_SERVER['SERVER_PROTOCOL'], "HTTPS");
	}
	
	/**
	 * Should the request be handled by static resource handler or by controller
	 * 
	 * @return bool true if the requested resource is static, otherwise false
	 */
	public function isStaticResource() {
		return $this->routeEntry != null && $this->routeEntry->isStatic();
	}
	
	public function estimateDefaultTemplateLocation() {
		$charAt0 = substr($this->controller, 0, 1);
		if($charAt0 == "\\") { // the controller is namespaced
			$parts = explode("\\", $this->controller);
			// count($parts) -2 means that the resulting array should have 2 elements less than the original
			// the element at 0 is empty and the element at the end is the controller name. The rest is namespace.
			return implode("/", array_slice($parts, 1, count($parts) -2)) . DS . $this->actionMethod . ".tpl";
		}
		return $this->actionMethod . ".tpl";
	}
}