<?php
/**
 *
 * @created 17.05.2012
 * @author Rusi Rusev <Sociographic UG>
 */
class RedirectException extends RuntimeException {
	
	/**
	 * Can be in the form of Controller.action, full or relative path url
	 * 
	 * @var string
	 */
	private $destination;
	
	/**
	 * The parameters needed to resolve the route
	 * 
	 * @var array
	 */
	private $params;
	
	/**
	 * The response status to sent back to the client
	 * 
	 * @var int
	 */
	private $status;
	
	/**
	 * Fires redirect event (301, 302, 303)
	 * 
	 * @param string $destination the destination url
	 * @param array $routeArgs the route args in case that the url should be resolved from the config
	 * @param int $status the http status code. default is 302
	 */
	public function __construct($destination, array $params = array(), $status = 302) {
		parent::__construct();
		$this->destination = $destination;
		$this->params = $params;
		$this->status = $status;
	}
	
	/**
	 * Returns the url (or controller.action) to redirect to
	 * 
	 * @return string
	 */
	public function getDestination() {
		return $this->destination;
	}
	
	/**
	 * Returns the http status code.
	 * 
	 * @return number
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * The route args
	 * @return array:
	 */
	public function getParams() {
		return $this->params;
	}
}