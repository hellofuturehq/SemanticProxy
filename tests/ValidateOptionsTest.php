<?php

use HelloFuture\SemanticProxy\Exceptions\Exception as ValidationException;
use HelloFuture\SemanticProxy\Transformer\AbstractTransformer;
use HelloFuture\SemanticProxy\Transformer\Value;

require_once __DIR__ . '/../vendor/autoload.php';

class ValidateOptionsTest extends PHPUnit_Framework_TestCase {

	public function testValidOptions() {
		$options     = [
			'mol' => 42,
			'url' => 'https://thats/a/valid/url/for/picky',
		];
		$transformer = new PickyTransformer(null, $options);
		$this->assertSame(42, $transformer->getOption('mol'));
	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\InvalidOptionsException
	 */
	public function testInvalidOptionsA() {
		$transformer = new PickyTransformer(null);
	}

	/**
	 * @expectedException HelloFuture\SemanticProxy\Exceptions\InvalidOptionsException
	 */
	public function testInvalidOptionsB() {
		$options     = [
			'mol' => 42,
			'url' => 'ftp://thats/no/valid/url/for/picky',
		];
		$transformer = new PickyTransformer(null, $options);
	}

}

class PickyTransformer extends AbstractTransformer {

	public function transform($inputData) {
		return $inputData;
	}

	public function optionRules() {
		return [
			'mol' => '/^42$/',
			'url' => '/^https?:\/\//',
		];
	}

}
