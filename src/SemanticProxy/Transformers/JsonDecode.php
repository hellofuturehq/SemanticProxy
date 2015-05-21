<?php

namespace HelloFuture\SemanticProxy\Transformers;

use HelloFuture\SemanticProxy\AbstractTransformer;

use HelloFuture\SemanticProxy\Exceptions\EmptyException;
use HelloFuture\SemanticProxy\Exceptions\JsonException;

class JsonDecode extends AbstractTransformer {

	public function transform($input) {
		if ((is_string($input) && trim($input) == '') || is_null($input)) {
			throw new EmptyException;
		}

		$output = json_decode($input);

		if (json_last_error() != JSON_ERROR_NONE) {
			throw new JsonException('invalid', JsonException::PARSE_ERROR);
		}

		return $output;
	}

}
