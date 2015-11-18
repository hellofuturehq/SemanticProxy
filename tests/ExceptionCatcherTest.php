<?php

use HelloFuture\SemanticProxy\Exceptions\DomException;
use HelloFuture\SemanticProxy\Source\Value;

require_once __DIR__ . '/../vendor/autoload.php';

class ExceptionCatcherTest extends PHPUnit_Framework_TestCase {

	const XML_TO_DOM = 'HelloFuture\\SemanticProxy\\Transformer\\XmlToDom';
	const NULLIFY_EXCEPTION = 'HelloFuture\\SemanticProxy\\Transformer\\ExceptionCatcher';

	public function testInvalidDom() {

		$xml = '<root><row><row></cell>';
		$transformer = Value::create($xml)->to(self::XML_TO_DOM)->to(self::NULLIFY_EXCEPTION);

		$this->assertNull($transformer->getData());

		$exception = $transformer->getMetaValue('exception');
		$this->assertInstanceOf('HelloFuture\\SemanticProxy\\Exceptions\\DomException', $exception);
		$this->assertSame(DomException::PARSE_ERROR, $exception->getCode());
	}

}
