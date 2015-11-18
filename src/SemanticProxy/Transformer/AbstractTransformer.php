<?php

namespace HelloFuture\SemanticProxy\Transformer;

use HelloFuture\SemanticProxy\Exceptions\InvalidOptionsException;
use HelloFuture\SemanticProxy\InputInterface;
use HelloFuture\SemanticProxy\OutputInterface;
use HelloFuture\SemanticProxy\Source\AbstractSource;

abstract class AbstractTransformer extends AbstractSource implements InputInterface {

	private $innerTransformer = null;
	private $options          = null;
	private $outputData       = null;
	private $meta             = [];

	public function __construct(OutputInterface $input, $options = array()) {
		$this->innerTransformer = $input;
		parent::__construct($options);
	}

	final public function getInner() {
		return $this->innerTransformer;
	}

	public function getInnerData() {
		return $this->getInner()->getData();
	}

	final public function getData() {
		return $this->transform($this->getInnerData());
	}

	final public function getScent() {
		return array_merge(parent::getScent(), $this->getInner()->getScent());
	}

	abstract protected function transform($inputData);

}
