<?php

use HelloFuture\SemanticProxy\Transformer\JsonDecode;
use HelloFuture\SemanticProxy\Transformer\Value;

require_once __DIR__ . '/../vendor/autoload.php';

class HtmlToDomTest extends PHPUnit_Framework_TestCase {

	const HTML_TO_DOM = 'HelloFuture\\SemanticProxy\\Transformer\\HtmlToDom';

	public function testValidDom() {

		$html = '<p>Snö';

		$transformer = Value::create($html)->to(self::HTML_TO_DOM);
		$data        = $transformer->getData();
		$this->assertInstanceOf('DomDocument', $data);
		$this->assertRegExp('/<p>Snö<\/p>/', $data->saveXml());
	}

}
