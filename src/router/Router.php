<?php

define("STATIC_RESSOURCES_CACHE_EXPIRE_TIME", 60*60*3); // 3 hours
/**
 * The Router class dispatches all requests and 
 * creates controller object accordingly. 
 * 
 */
class Router {
	/**
	 * The application config
	 * @var ConfigManager
	 */
	protected $_config;
	
	/**
	 * The routing defined for the application
	 * @var Routing
	 */
	protected $_routing;
	
	/**
	 * Class logger instance
	 * @var Logger
	 */
	private $logger;
	
	/**
	 * The properties containing the file extensions as key and the mime type as value.
	 * 
	 * @var Properties
	 */
	private $_contentTypeProperties;
	
	/**
	 * The properties containing the http status codes as key the status code and the status code description as value.
	 *
	 * @var Properties
	 */
	private $_statusCodeProperties;
	
	/**
     * @var Router
     */
    protected static $_instance = null;
	
    /**
     * The name of the file containing the file extensions to their mime types.
     * This file should be located under config directory.
     * 
     * @var string
     */
    const MIME_TYPES_FILE_NAME = "mime-types.properties";
    
    /**
     * The name of the file containing the http status codes and their descriptions
     * This file should be located under config directory.
     *
     * @var string
     */
    const HTTP_STATUS_CODES_FILE_NAME = "http-status-codes.properties";
    
	protected function __construct() {		
		$this->logger = new Logger(get_class($this));
		$this->_config = ConfigManager::getInstance();
		$this->_routing = new Routing();
		$this->_contentTypeProperties = new Properties($this->_config->getConfigPath() . DS . self::MIME_TYPES_FILE_NAME);
		$this->_statusCodeProperties = new Properties($this->_config->getConfigPath() . DS . self::HTTP_STATUS_CODES_FILE_NAME);
		//var_dump($this->_routing->getRoutes());
	}
	
	/**
     * Gets Router instance.
     *
     * @return Router
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
	public function dispatch() {
		try {			
			$request = self::resolveRoute($this->_routing);
			if($request->isStaticResource()) {
				$this->handleStaticFiles($request);
			} else {
				$controllerRelativePath = ClassUtils::convertToPath($request->controller);
				
				$this->logger->debug("Trying to load controller: " . $controllerRelativePath);
				self::loadController($controllerRelativePath);
				
				$controllerClass = $request->controller;
				$controller = new $controllerClass();
				if(!method_exists($controller,$request->actionMethod)){
					throw new RouterException("The action " . $request->actionMethod . " doesn't exists.");
				}
				$action = new ReflectionMethod($request->controller, $request->actionMethod);
				if($action->isPublic() === false) {
					throw new NotFoundException();
				}
				$this->setHeaders($request);
				// set default template in case that render is called without arguments
				$controller->setDefaultTemplate($request->estimateDefaultTemplateLocation());
				$action->invoke($controller, $request->args);
			}
		} catch(NotFoundException $e) {			
			$this->handle404($e);
		} catch(InternalErrorException $e) {
			$this->handle500($e);
		} catch(RedirectException $e) {
			$this->handleRedirect($e);
		} catch (RouterException $e) {
			$this->handle500($e);
		} catch(Exception $e) {
			$this->handle500($e);
		}
	}
	
	public function handleStaticFiles(HttpRequest $request) {
		if($request->isStaticResource() === false) {
			throw new InternalErrorException("Tried to handle no static request as static resource. Entry: " . var_dump($request, true));
		}
		$staticResource = PATH_ROOT . str_replace("/", DS, StringUtils::replaceFirst($request->path, $request->routeEntry->getPath(), $request->actionMethod));
		if(file_exists($staticResource) === true) {
			$this->setHeaders($request);
			readfile($staticResource);
		} else {
			throw new NotFoundException("Resource not found: " . $staticResource);
		}
	}
	
	public function handle404($exception = null) {
		if($exception != null) {
			$this->logger->error("Exception raised: " . $exception->getMessage());
		}
		try {
			// try to load 404 page
			self::loadController("errors/NotFound");
			$o = new \errors\NotFound();
			$o->render();
		} catch (RouterException $ex404) {
			// try to load internal error page
			$this->handle500($ex404);
		}
	}
	
	public function handle500($exception = null) {
		if($exception != null) {
			$this->logger->error("Exception raised: " . $exception->getMessage());
		}
		try {
			self::loadController("errors/InternalError");
			$o = new \errors\InternalError();
			$o->render();
		} catch(RouterException $ex500) {
			$this->logger->error("Error pages cannot be handled. Please check the configuration: " . $ex500->getMessage());
			exit();
		}
	}
	
	/**
	 * 
	 * @param RedirectException $exception
	 */
	public function handleRedirect($exception = null) {
		if($exception != null) {
			$this->logger->debug("Redirect requested: " . $exception->getDestination());
			$isControllerAction = preg_match('@^[^\\.]+[\\.][a-zA-Z_]+$@i', $exception->getDestination()) === 1;
			$url = $isControllerAction ? self::reverse($exception->getDestination(), array(), $this->_routing) : $exception->getDestination();
			if($url == null) {
				$this->handle500(new InternalErrorException("Cannot redirect to non existing route entry : " . $exception->getDestination()));
			}
			header("HTTP/1.1 " . $exception->getStatus() . " " . $this->_statusCodeProperties->getProperty($exception->getStatus()));
			header("Location:" . $url);
		}
	}

	// should be moved to the controller ?
	public function setHeaders(HttpRequest $request) {
		$contentType = self::resolveContentType($this->_contentTypeProperties);
		if($request->isStaticResource()) {
			header('Cache-Control: max-age=' . STATIC_RESSOURCES_CACHE_EXPIRE_TIME); // must-revalidate
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + STATIC_RESSOURCES_CACHE_EXPIRE_TIME).' GMT');
		} else { // set non static resources headers
		}
	
		if($contentType == null) { //if content type cannot be resolved set default to html
			$contentType = $this->_contentTypeProperties->getProperty("html");
		}
	
		header('Content-type: ' . $contentType .'; charset=utf-8');
	}
	
	public static function loadController($path) {
		$controllerFile = self::getControllerFile($path);
		self::getInstance()->logger->debug("Loading controller file: " . $controllerFile);
		self::loadControllerFile($controllerFile);
		
		if(class_exists(ClassUtils::convertToClass($path), false) === false) {
			throw new RouterException(__CLASS__."::".__FUNCTION__.": class definition not found: '{$path}', $controllerFile");
       }
	}
	
	public static function getControllerFile($className) {
		return self::getInstance()->_config->getControllersPath() . "/" . $className . ".php";
	}
	
	/**
	 * Loads dynamically a needed controller class
	 *
	 * @param unknown_type $className
	 * @throws Exception
	 */
	public static function loadControllerFile($classFile) {
		// check for file existence
		if(isset($classFile) === false || !file_exists($classFile)) {
			throw new RouterException(__CLASS__."::".__FUNCTION__.": class not found: '{$classFile}'");
		}
	
		require_once($classFile);
	}
	
	/**
	 * 
	 * @param string $controllerAction
	 * @param array $params
	 * @return NULL
	 */
	public static function reverse($controllerAction, array $params = array(), Routing $routing = null) {
		if($routing == null) {
			$routing = new Routing();
		}
		$serverName = self::resolveDomain($routing);
		$url = null;
		foreach($routing->getRoutes() as $route) {
			$url = $route->reverse($controllerAction, $params);
			if($url != null) {
				break;
			}
		}
		return $url;
	}
	
	/**
	 * Resolves the http request controller and action and the parameters from the routes files
	 * @param Routing $routing
	 * @return HttpRequest the resolved http request
	 * @throws NotFoundException if requested action is not found
	 */
	public static function resolveRoute(Routing $routing) {
		$request = new HttpRequest(self::resolveDomain($routing));
		foreach($routing->getRoutes() as $route) {
			if($route->matches($request) === true) {
				$request->routeEntry = $route;
				break;
			}
		}
		if($request->routeEntry == null) {
			throw new NotFoundException("No route entry defined for request " . $request->method . " " . $request->domain . $request->path);
		}
		$request->actionMethod = $request->routeEntry->getAction();
		$request->controller = $request->routeEntry->getController();
		if(strpos($request->actionMethod, '{') !== false || strpos($request->controller, '{') !== false) {
			foreach($request->routeArgs as $routeKey => $routeValue) {
				$action = $request->actionMethod;
				$controller = $request->controller;
				$request->actionMethod = str_replace('{' . $routeKey . '}', $routeValue, $action);
				$request->controller = str_replace('{' . $routeKey . '}', $routeValue, $controller);
				if($action !== $request->actionMethod || $controller !== $request->controller) {
					unset($request->routeArgs[$routeKey]);
				}
			}	
		}
		if(strpos($request->actionMethod, '{') !== false || strpos($request->controller, '{') !== false) {
			throw new InternalErrorException("Invalid route entry. Undefined controller or action for route entry at line " . $request->routeEntry->getLine());
		}
		$request->args = array_merge($request->routeEntry->getStaticArgs(), $request->routeArgs, $_GET);
		//var_dump($request);
		return $request;
	}
	
	public static function resolveDomain(Routing $routing = null) {
		if($routing == null) {
			$routing = new Routing();
		}
		$serverName = $_SERVER['SERVER_NAME'];
		if($routing->getConfig()->isDev() && $routing->getConfig()->getValue(ConfigManager::BASE_DOMAIN) !== "") {
			$serverName = $routing->getConfig()->getValue(ConfigManager::BASE_DOMAIN);
		}
		return $serverName;
	}
	
	public static function resolveContentType(Properties $contentTypes = null) {
		if($contentTypes == null) {
			$contentTypes = new Properties(ConfigManager::getInstance()->getConfigPath() . DS . self::MIME_TYPES_FILE_NAME);
		}
		$extension = strrchr($_SERVER['REQUEST_URI'], '.');
		if($extension !== false) {
			$contentType = $contentTypes->getProperty(substr($extension, 1));
			return $contentType;
		}
		return null;
	}
	
	public static function resolveHttpStatusMessage($status, Properties $httpStatusCodes = null) {
		if($httpStatusCodes == null) {
			$httpStatusCodes = new Properties(ConfigManager::getInstance()->getConfigPath() . DS . self::HTTP_STATUS_CODES_FILE_NAME);
		}
	 	return $httpStatusCodes->getProperty($status);
	}
}