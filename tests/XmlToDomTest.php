<?php

use HelloFuture\SemanticProxy\Source\Value;
use HelloFuture\SemanticProxy\Transformer\JsonDecode;

require_once __DIR__ . '/../vendor/autoload.php';

class XmlToDomTest extends PHPUnit_Framework_TestCase {

	const XML_TO_DOM = 'HelloFuture\\SemanticProxy\\Transformer\\XmlToDom';

	public function testValidDom() {

		$xml = '<root><row>rain</row><row>slush</row><row>snow</row></root>';

		$transformer = Value::create($xml)->to(self::XML_TO_DOM);
		$data        = $transformer->getData();
		$this->assertInstanceOf('DomDocument', $data);
		$this->assertSame(3, $data->documentElement->childNodes->length);
		$this->assertSame('snow', $data->documentElement->childNodes->item(2)->nodeValue);
	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\DomException
	 * @expectedExceptionCode 3
	 */
	public function testInvalidDom() {

		$xml = '<root><row><row></cell>';
		Value::create($xml)->to(self::XML_TO_DOM)->getData();

	}

}
