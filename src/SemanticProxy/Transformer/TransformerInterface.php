<?php

namespace HelloFuture\SemanticProxy\Transformer;

interface TransformerInterface {

	public function getData();

	public function getInner();

	public function getInputData();

	public function getOutputData();

}
