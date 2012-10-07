<?php
namespace errors;

class NotFound extends \Controller {
	public function render($templateName = "errors/404.tpl") {
		header("HTTP/1.1 404 Not Found");
		parent::setDefaultTemplate($templateName);
		parent::render();
	}
}