<?php

namespace HelloFuture\SemanticProxy\Source;

class Value extends AbstractSource {

	public function __construct($mixed) {
		parent::__construct(['value' => $mixed]);
	}

	public function getData() {
		return $this->getOption('value');
	}

}
