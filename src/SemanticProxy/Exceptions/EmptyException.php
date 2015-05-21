<?php

namespace HelloFuture\SemanticProxy\Exceptions;

class EmptyException extends Exception {

	public function __construct($message = 'empty input') {
		parent::__construct($message, self::EMPTY_INPUT);
	}

}
