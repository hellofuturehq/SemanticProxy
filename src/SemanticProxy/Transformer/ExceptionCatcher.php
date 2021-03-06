<?php

namespace HelloFuture\SemanticProxy\Transformer;

use HelloFuture\SemanticProxy\Exceptions\Exception;

class ExceptionCatcher extends AbstractTransformer {

	public function getInnerData() {
		try {
			$inputData = parent::getInnerData();
		} catch(Exception $e) {
			$inputData = null;
			$this->setMetaValue('exception', $e);
		}
		return $inputData;
	}

	protected function transform($input) {
		return $input;
	}

}
