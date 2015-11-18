<?php

use HelloFuture\SemanticProxy\Transformer\Value;

require_once __DIR__ . '/../vendor/autoload.php';

class CallTest extends PHPUnit_Framework_TestCase {

	public function testBasic() {
		$transformer = new Value('foo42');
		$this->assertSame('foo42', $transformer->getData());

		$transformer = new Value(range(1,5));
		$this->assertSame([1, 2, 3, 4, 5], $transformer->getData());
	}

	public function testFactory() {
		$transformer1 = new Value('foo42');
		$transformer2 = Value::create('foo42');
		$this->assertEquals($transformer1, $transformer2);
	}

}
