<?php

class Application extends Controller {
	public function index() {
		parent::render(array(
				"firstname" => "Max",
				"lastname" => "Mustermann"
				));
	}
	
	public function license() {
		parent::render();
	}
}