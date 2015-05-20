<?php

use HelloFuture\SemanticProxy\AbstractTransformer;
use HelloFuture\SemanticProxy\TransformerFactory;

require_once __DIR__ . '/vendor/autoload.php';

class LinkTransformer extends AbstractTransformer {

	public function validate($inputData) {
		return is_string($inputData);
	}

	public function transform($inputData) {
		return '<a href="' . $inputData . '">' . $inputData . '</a>';
	}

}

class FooHashTransformer extends AbstractTransformer {

	public function validate($inputData) {
		return is_string($inputData);
	}

	protected function transform($inputData) {
		return $inputData . '#' . $this->getOption('hash', 'foo');
	}

}

class HtmlEncodeTransformer extends AbstractTransformer {


	public function validate($inputData) {
		return is_string($inputData);
	}

	protected function transform($inputData) {
		return htmlspecialchars($inputData);
	}

}

/*

$data = (
	new HtmlEncodeTransformer(
		new LinkTransformer(
			new FooHashTransformer('http://example.org', ['hash' => 'bar2'])
		)
	)
);

*/

$data = TransformerFactory::create('http://example.org')
	->to('FooHashTransformer', ['hash' => 'bar2'])
	->to('LinkTransformer')
	->to('HtmlEncodeTransformer')
;


echo '<p>' . $data->getData() . '</p>';
echo '<p>' . $data->getInner()->getInner()->getInputData() . '</p>';
var_dump($data->getScent());
echo '<p>md5: ' . md5(json_encode($data->getScent())) . '</p>';
