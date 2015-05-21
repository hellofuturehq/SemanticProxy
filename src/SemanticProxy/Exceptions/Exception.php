<?php

namespace HelloFuture\SemanticProxy\Exceptions;

class Exception extends \Exception {

	/**
	 * do not even think of using the integer values instead of constants, they may change anytime!
	 */

	const EMPTY_INPUT   = 1;
	const NOT_FOUND     = 2;
	const PARSE_ERROR   = 3;
	const TIMEOUT       = 4;
	const UNKNOWN_ERROR = 5;

}
