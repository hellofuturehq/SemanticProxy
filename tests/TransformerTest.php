<?php

use HelloFuture\SemanticProxy\AbstractTransformer;
use HelloFuture\SemanticProxy\ValidationException;
use HelloFuture\SemanticProxy\Transformers\Value;

require_once __DIR__ . '/../vendor/autoload.php';

class CallTest extends PHPUnit_Framework_TestCase {

	public function testBasic() {
		$transformer = new Value('foo42');

		$this->assertSame('foo42', $transformer->getData());
		$this->assertSame('foo42', $transformer->getInputData());
		$this->assertSame('foo42', $transformer->getOutputData());
		$this->assertNull($transformer->getInner());
	}

	public function testScent() {
		$transformer = new Value('foo23');
		$expectedScent = [
			(object) [
				'data'    => 'foo23',
				'options' => (object) []
			],
			(object) [
				'class'   => 'HelloFuture\\SemanticProxy\\Transformers\\Value',
				'options' => (object) []
			],
		];

		$this->assertEquals($expectedScent, $transformer->getScent());
	}

	public function testFactory() {
		$transformer1 = new Value('foo42');
		$transformer2 = Value::create('foo42');

		$this->assertEquals($transformer1, $transformer2);
	}

	public function testWrapping() {
		$transformer1 = new Palindromify(
			new Value('snow')
		);

		$this->assertSame('snowons', $transformer1->getData());
		$this->assertSame('snow',    $transformer1->getInputData());
		$this->assertSame('snowons', $transformer1->getOutputData());

		$transformer2 = new Duplicate($transformer1);

		$this->assertSame('snowonssnowons', $transformer2->getData());

		// a(b()) != b(a())

		$transformer3 = new Palindromify(
			new Duplicate(
				new Value('snow')
			)
		);

		$this->assertSame('snowsnowonswons', $transformer3->getData());
	}

	public function testToMethod() {

		$transformer1 = new Palindromify(
			new Duplicate(
				new Value('frost')
			)
		);

		$transformer2 = Value::create('frost')->to('Duplicate')->to('Palindromify');

		$this->assertEquals($transformer1, $transformer2);

		$transformer1->getData();
		$transformer2->getData();

		$this->assertEquals($transformer1, $transformer2);

	}

	public function testTraverse() {

		$transformer1 = new Duplicate(
			new Value('ice')
		);

		$transformer2 = new Palindromify($transformer1);

		$this->assertSame($transformer2->getInner(), $transformer1);
		$this->assertInstanceOf('HelloFuture\\SemanticProxy\\AbstractTransformer', $transformer1->getInner());
		$this->assertInstanceOf('HelloFuture\\SemanticProxy\\AbstractTransformer', $transformer2->getInner()->getInner());
		$this->assertNull($transformer1->getInner()->getInner());
		$this->assertSame($transformer1->getData(), $transformer2->getInputData());
	}

	public function testOptions() {

		$transformer1 = Value::create('cold')->to('Repeat', ['number' => 3]);

		$this->assertSame(3, $transformer1->getOption('number'));
		$this->assertSame('coldcoldcold', $transformer1->getData());

		$expected = (object) ['number' => 3];

		$this->assertEquals($expected, $transformer1->getOptions());

		// unknown param

		$this->assertNull($transformer1->getOption('unknown'));
		$this->assertSame('?', $transformer1->getOption('unknown', '?'));
	}

	public function testValid() {

		$transformer = Value::create('VALID')->to('StrangeTransformer');

		$this->assertSame('VALID', $transformer->getData());

	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\ValidationException
	 */
	public function testInvalidInValidateMethod() {

		$transformer = Value::create('BREAK-V')->to('StrangeTransformer');
		$transformer->getData();

	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\ValidationException
	 */
	public function testInvalidInTransformMethod() {

		$transformer = Value::create('BREAK-T')->to('StrangeTransformer');
		$transformer->getData();

	}

}

class Palindromify extends AbstractTransformer {

	public function transform($inputData) {
		return substr($inputData, 0, -1) . strrev($inputData);
	}

}

class Duplicate extends AbstractTransformer {

	public function transform($inputData) {
		return str_repeat($inputData, 2);
	}

}

class Repeat extends AbstractTransformer {

	public function __construct($input, $options = array()) {
		$options = array_merge(['number' => 1], $options);
		parent::__construct($input, $options);
	}

	public function transform($inputData) {
		return str_repeat($inputData, $this->getOption('number'));
	}

}

class StrangeTransformer extends AbstractTransformer {

	public function validate($inputData) {
		return $inputData != 'BREAK-V';
	}

	public function transform($inputData) {
		if ($inputData == 'BREAK-T') {
			throw new ValidationException;
		}
		return $inputData;
	}

}
