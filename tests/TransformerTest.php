<?php

use HelloFuture\SemanticProxy\Exceptions\Exception as ValidationException;
use HelloFuture\SemanticProxy\Source\Value;
use HelloFuture\SemanticProxy\Transformer\AbstractTransformer;

require_once __DIR__ . '/../vendor/autoload.php';

class TransformerTest extends PHPUnit_Framework_TestCase {

	const VALUE_CLASS = 'HelloFuture\\SemanticProxy\\Source\\Value';
	const INPUT_IF    = 'HelloFuture\\SemanticProxy\\InputInterface';
	const OUTPUT_IF   = 'HelloFuture\\SemanticProxy\\OutputInterface';

	public function testWrapping() {
		$transformer1 = new Palindromify(
			new Value('snow')
		);

		$this->assertSame('snowons', $transformer1->getData());
		$this->assertSame('snow',    $transformer1->getInnerData());

		$transformer2 = new Duplicate($transformer1);

		$this->assertSame('snowonssnowons', $transformer2->getData());

		// a(b()) != b(a())

		$transformer3 = new Palindromify(
			new Duplicate(
				new Value('snow')
			)
		);

		$this->assertSame('snowsnowonswons', $transformer3->getData());
		$this->assertSame('snowsnow',        $transformer3->getInnerData());
		$this->assertSame('snowsnow',        $transformer3->getInner()->getData());
		$this->assertSame('snow',            $transformer3->getInner()->getInnerData());

		$this->assertInstanceOf('Palindromify',    $transformer3);
		$this->assertInstanceOf('Duplicate',       $transformer3->getInner());
		$this->assertInstanceOf(self::VALUE_CLASS, $transformer3->getInner()->getInner());

		$this->assertInstanceOf(self::INPUT_IF, $transformer3);
		$this->assertInstanceOf(self::INPUT_IF, $transformer3->getInner());
		$this->assertNotInstanceOf(self::INPUT_IF, $transformer3->getInner()->getInner());
		$this->assertInstanceOf(self::OUTPUT_IF, $transformer3);
		$this->assertInstanceOf(self::OUTPUT_IF, $transformer3->getInner());
		$this->assertInstanceOf(self::OUTPUT_IF, $transformer3->getInner()->getInner());

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

	public function testOptions() {

		$transformer1 = Value::create('cold')->to('Repeat', ['number' => 3]);

		$this->assertSame(3, $transformer1->getOption('number'));
		$this->assertSame('coldcoldcold', $transformer1->getData());

		$expected = ['number' => 3];
		$this->assertEquals($expected, $transformer1->getOptions());

		$expected = ['value' => 'cold'];
		$this->assertEquals($expected, $transformer1->getInner()->getOptions());

		// unknown param

		$this->assertNull($transformer1->getOption('unknown'));
		$this->assertSame('DEFAULT', $transformer1->getOption('unknown', 'DEFAULT'));

		// default param

		$transformer2 = Value::create('chilly')->to('Repeat');
		$this->assertSame(1, $transformer2->getOption('number'));

	}


	public function testScent() {
		$transformer1 = new Value('foo23');
		$expected = [
			(object) [
				'class'   => self::VALUE_CLASS,
				'options' => ['value' => 'foo23']
			],
		];
		$this->assertEquals($expected, $transformer1->getScent());

		$transformer2 = new Palindromify($transformer1);
		$expected = [
			(object) [
				'class'   => 'Palindromify',
				'options' => []
			],
			(object) [
				'class'   => self::VALUE_CLASS,
				'options' => ['value' => 'foo23']
			],
		];
		$this->assertEquals($expected, $transformer2->getScent());

	}

	public function testValid() {

		$transformer = Value::create('VALID')->to('StrangeTransformer');

		$this->assertSame('VALID', $transformer->getData());

	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\Exception
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

	public function transform($inputData) {
		return str_repeat($inputData, $this->getOption('number'));
	}

	public function getDefaultOptions() {
		return ['number' => 1];
	}

}

class StrangeTransformer extends AbstractTransformer {

	public function transform($inputData) {
		if ($inputData == 'BREAK-T') {
			throw new ValidationException;
		}
		return $inputData;
	}

}
