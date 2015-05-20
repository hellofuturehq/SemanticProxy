<?php

namespace HelloFuture\SemanticProxy;

interface TransformerInterface {

	public function getData();

	public function getInner();

	public function getInputData();

	public function getOutputData();

}
