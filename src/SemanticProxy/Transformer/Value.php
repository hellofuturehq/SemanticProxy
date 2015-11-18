<?php

namespace HelloFuture\SemanticProxy\Transformer;

use HelloFuture\SemanticProxy\Source\AbstractSource;

class Value extends AbstractSource {

	public function __construct($mixed) {
		parent::__construct(['value' => $mixed]);
	}

	public function getData() {
		return $this->getOption('value');
	}

}
