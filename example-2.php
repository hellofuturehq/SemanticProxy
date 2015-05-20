<?php

use HelloFuture\SemanticProxy\AbstractTransformer;
use HelloFuture\SemanticProxy\TransformerFactory;
use HelloFuture\SemanticProxy\ValidationException;

require_once __DIR__ . '/vendor/autoload.php';

class WebUrlToContent extends AbstractTransformer {

	public function validate($inputData) {
		return is_string($inputData);
	}

	// @todo error handling, timeout handling
	public function transform($inputData) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$inputData);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}

}

class AsJson extends AbstractTransformer {

	public function transform($input) {
		$output = json_decode($input);
		if (json_last_error() != JSON_ERROR_NONE) {
			throw new ValidationException('invalid json');
		}
		return $output;
	}

}

class ToInvalidJsonString extends AbstractTransformer {

	public function transform($input) {
		return '{{{{{{{' . $input;
	}

}


$data = TransformerFactory::create('http://motor-development.helloumbra.com/current/api')
	->to('WebUrlToContent')
	#->to('ToInvalidJsonString')
	->to(__NAMESPACE__ . '\\AsJson')
;

echo json_encode($data->getData());
