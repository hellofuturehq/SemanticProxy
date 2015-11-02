<?php

namespace HelloFuture\SemanticProxy\Transformer;

use HelloFuture\SemanticProxy\Exceptions\InvalidOptionsException;

abstract class AbstractTransformer implements TransformerInterface {

	private $innerTransformer = null;
	private $options          = null;
	private $inputData        = null;
	private $outputData       = null;
	private $meta             = [];

	public function __construct(/* Transformer or mixed */ $input, $options = array()) {
		if (is_a($input, 'HelloFuture\\SemanticProxy\\Transformer\\TransformerInterface')) {
			$this->innerTransformer = $input;
		} else {
			$this->inputData = $input;
		}
		$this->options = array_merge($this->getDefaultOptions(), $options);
		if (!$this->validateOptions()) {
			throw new InvalidOptionsException('invalid call of ' . get_class($this));
		}
	}

	static public function create($input, $options = array()) {
		return new static($input, $options);
	}

	public function to($className, $options = array()) {
		return new $className($this, $options);
	}

	public function getDefaultOptions() {
		return [];
	}

	final public function getInner() {
		return $this->innerTransformer;
	}

	public function getInputData() {
		if ($this->getInner()) {
			return $this->getInner()->getData();
		} else {
			return $this->inputData;
		}
	}

	public function getOutputData() {
		return $this->transform($this->getInputData());
	}

	final public function getData() {
		return $this->getOutputData();
	}

	final public function getScent() {
		$inner = $this->getInner();
		if ($inner) {
			$path = $inner->getScent();
		} else {
			$path = [(object) ['data' => $this->getData(), 'options' => $this->getOptions()]];
		}
		array_push($path, (object) ['class' => get_class($this), 'options' => $this->getOptions()]);
		return $path;
	}

	final public function getOptions() {
		return $this->options;
	}

	final public function getOption($key, $default = null) {
		$options = $this->getOptions();
		return isset($options[$key]) ? $options[$key] : $default;
	}

	final public function getMetaValue($key, $default = null) {
		if (isset($this->meta[$key])) {
			return $this->meta[$key];
		} else {
			$inner = $this->getInner();
			if ($inner) {
				return $inner->getMetaValue($key, $default);
			} else {
				return $default;
			}
		}
	}

	final public function setMetaValue($key, $value) {
		$this->meta[$key] = $value;
	}

	abstract protected function transform($inputData);

	public function validateOptions() {
		foreach($this->rules() as $option => $pattern) {
			$value = $this->getOption($option);
			if (is_null($value) || !preg_match($pattern, $value)) {
				return false;
			}
		}
		return true;
	}

	public function rules() {
		return [];
	}

}
