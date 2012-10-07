<?php

/**
 * Routing manager provides access to the app route paths
 *
 */
class Routing {
	/**
	 * 
	 * @var string The routes file
	 */
    protected $_routesFile;
    
    /**
     * 
     * @var array
     */
    protected $_routes;
    
    /**
     * 
     * @var ConfigManager The config manager
     */
    protected $_config;
    
    /**
     * The class logger
     * @var Logger
     */
    private $logger;
    
	/**
     * Routing constructor.
     */
    public function __construct() {
    	$this->logger = new Logger(get_class($this));
    	$this->_config = ConfigManager::getInstance();
    	$this->_routesFile = $this->_config->getConfigPath() . "/route.config.php";
    	
    	if(!file_exists($this->_routesFile)) {
    		throw new RuntimeException("File does not exist: '{$this->_routesFile}'");
    	}
    	
    	$this->parse();
    }
    
    public function getRoutes() {
    	return $this->_routes;
    }
    
    public function getRoutesFile() {
    	return $this->_routesFile;
    }
    
    public function getConfig() {
    	return $this->_config;
    }
	
	/**
	 * Parses the configuration file and initilizes the
	 * internal structure of the class.
	 * 
	 * @return void
	 */
	protected function parse() {
		$this->_routes = array();
		$lines = explode("\n", @file_get_contents($this->_routesFile));
		
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
				try {
					$this->_routes[] = new RouteEntry($line, $i+1); //$method, $address, $controllerWithAction);
				} catch(RouterException $e) {
					$this->logger->warn("Invalid route entry. Exception raised: " . $e->getMessage());
				}
			}
		}
	}
	
	public function __toString() {
		$output = $this->_routesFile . ":\n";
		foreach($this->_routes as $route) {
			$output .= $route . "\n";
		}
		return $output;
	}
}