<?php

class NotFoundException extends RuntimeException {
	/**
	 * Fires page not found event
	 * 
	 * @param string $message the message shown on the not found page
	 */
	public function __construct($message = "Page not found")
	{
		parent::__construct($message);
	}
}