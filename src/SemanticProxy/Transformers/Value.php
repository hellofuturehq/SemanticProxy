<?php

namespace HelloFuture\SemanticProxy\Transformers;

use HelloFuture\SemanticProxy\AbstractTransformer;

class Value extends AbstractTransformer {

	public function transform($inputData) {
		return $inputData;
	}

}
