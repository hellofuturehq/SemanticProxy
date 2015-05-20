<?php

namespace HelloFuture\SemanticProxy;

class TransformerFactory extends AbstractTransformer {

	static public function create($input, $options = array()) {
		return new static($input, $options);
	}

	public function transform($inputData) {
		return $inputData;
	}

}
