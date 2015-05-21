<?php

use HelloFuture\SemanticProxy\Transformer\JsonDecode;
use HelloFuture\SemanticProxy\Transformer\Value;

require_once __DIR__ . '/../vendor/autoload.php';

class JsonDecodeTest extends PHPUnit_Framework_TestCase {

	const JSON_DECODE = 'HelloFuture\\SemanticProxy\\Transformer\\JsonDecode';

	public function testValidJson() {

		$transformer = Value::create('13')->to(self::JSON_DECODE);
		$this->assertSame(13, $transformer->getData());

		$transformer = Value::create('"glacier"')->to(self::JSON_DECODE);
		$this->assertSame('glacier', $transformer->getData());

		$transformer = Value::create('[1,2]')->to(self::JSON_DECODE);
		$this->assertSame([1,2], $transformer->getData());

		$json = '{"temperature":{"value":-43.7,"unit":"°C"}}';
		$transformer = Value::create($json)->to(self::JSON_DECODE);
		$this->assertSame(-43.7, $transformer->getData()->temperature->value);
		$this->assertSame('°C',  $transformer->getData()->temperature->unit);
	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\JsonException
	 * @expectedExceptionCode 3
	 */
	public function testInvalidJson() {

		Value::create('{{')->to(self::JSON_DECODE)->getData();

	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\EmptyException
	 * @expectedExceptionCode 1
	 */
	public function testEmptyJson() {

		Value::create('   ')->to(self::JSON_DECODE)->getData();

	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\EmptyException
	 * @expectedExceptionCode 1
	 */
	public function testNullJson() {

		Value::create(null)->to(self::JSON_DECODE)->getData();

	}

}
