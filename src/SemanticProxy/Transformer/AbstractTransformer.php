<?php

namespace HelloFuture\SemanticProxy\Transformer;

use HelloFuture\SemanticProxy\Exceptions\InvalidOptionsException;
use HelloFuture\SemanticProxy\InputInterface;
use HelloFuture\SemanticProxy\OutputInterface;
use HelloFuture\SemanticProxy\Source\AbstractSource;

abstract class AbstractTransformer extends AbstractSource implements InputInterface {

	protected $innerTransformer = null;
	protected $outputData       = null;

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

	public function getMetaValue($key, $default = null) {
		if (isset($this->meta[$key])) {
			return $this->meta[$key];
		} else {
			return $this->getInner()->getMetaValue($key, $default);
		}
	}

}
