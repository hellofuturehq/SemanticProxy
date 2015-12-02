<?php

use HelloFuture\SemanticProxy\Source\AbstractSource;
use HelloFuture\SemanticProxy\Source\Value;

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

	public function testMeta() {
		$transformer = new Value('crystal');
		$transformer->setMetaValue('edges', 6);
		$this->assertEquals(6, $transformer->getMetaValue('edges'));
		$this->assertNull($transformer->getMetaValue('unknown'));
		$this->assertEquals(7, $transformer->getMetaValue('unknown', 7));
	}

	public function testOptions() {
		$transformer = new OptionTest(['edges' => 8, 'magic' => 'WIZARD']);
		$expected    = ['default' => 'tohuwabohu', 'edges' => 8, 'magic' => 'WIZARD'];
		$this->assertEquals($expected, $transformer->getOptions());
		$this->assertEquals(8,            $transformer->getOption('edges'));
		$this->assertEquals('WIZARD',     $transformer->getOption('magic'));
		$this->assertEquals('tohuwabohu', $transformer->getOption('default'));
		$this->assertNull($transformer->getOption('default2'));
		$this->assertEquals('NULL', $transformer->getOption('default2', 'NULL'));
	}

	public function testScent() {
		$transformer = new Value(12);
		$expected = [
			(object) [
				'class'   => 'HelloFuture\\SemanticProxy\\Source\Value',
				'options' => ['value' => 12]
			]
		];
		$this->assertEquals($expected, $transformer->getScent());
	}

}

class OptionTest extends AbstractSource {

	public function getData() {
		return json_decode($this->getOptions());
	}

	public function getDefaultOptions() {
		return ['default' => 'tohuwabohu'];
	}

}
