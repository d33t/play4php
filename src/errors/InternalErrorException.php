<?php
class InternalErrorException extends RuntimeException {
	/**
	 * Fires internal error event (500)
	 * @param string $message the message shown on the internal error page
	 */
	public function __construct($message)
	{
		parent::__construct($message);
	}
}