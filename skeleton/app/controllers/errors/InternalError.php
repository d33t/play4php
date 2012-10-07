<?php
namespace errors;

class InternalError extends \Controller {
	public function render($templateName = "errors/500.tpl") {
		header("HTTP/1.1 500 Internal Server Error");
		parent::setDefaultTemplate($templateName);
		parent::render();
	}
}