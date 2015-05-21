<?php

namespace HelloFuture\SemanticProxy;

abstract class AbstractTransformer implements TransformerInterface {

	use ChainableTrait;

	private $innerTransformer = null;
	private $options          = null;
	private $hasInputData     = false;
	private $hasOutputData    = false;
	private $inputData        = null;
	private $outputData       = null;

	public function __construct(/* Transformer or mixed */ $input, $options = array()) {
		if (is_a($input, 'HelloFuture\\SemanticProxy\\TransformerInterface')) {
			$this->innerTransformer = $input;
		} else {
			$this->inputData = $input;
		}
		$this->options = (object) $options;
	}

	static public function create($input, $options = array()) {
		return new static($input, $options);
	}

	public function getInner() {
		return $this->innerTransformer;
	}

	public function getInputData() {
		if (!$this->hasInputData) {
			if ($this->getInner()) {
				$this->inputData    = $this->getInner()->getData();
				$this->hasInputData = true;
			}
		}
		return $this->inputData;
	}

	public function getOutputData() {
		if (!$this->hasOutputData) {
			$inputData = $this->getInputData();
			if (!$this->validate($inputData)) {
				throw new ValidationException('invalid input data in ' . get_class($this));
			}
			$this->outputData    = $this->transform($inputData);
			$this->hasOutputData = true;
		}
		return $this->outputData;
	}

	public function getData() {
		return $this->getOutputData();
	}

	public function getScent() {
		$inner = $this->getInner();
		if ($inner) {
			$path = $inner->getScent();
		} else {
			$path = [(object) ['data' => $this->getData(), 'options' => $this->getOptions()]];
		}
		array_push($path, (object) ['class' => get_class($this), 'options' => $this->getOptions()]);
		return $path;
	}

	public function getOptions() {
		return $this->options;
	}

	public function getOption($key, $default = null) {
		$options = $this->getOptions();
		return isset($options->$key) ? $options->$key : $default;
	}

	public function validate($inputData) {
		return true;
	}

	abstract protected function transform($inputData);

}
