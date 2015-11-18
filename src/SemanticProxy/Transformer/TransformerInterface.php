<?php

namespace HelloFuture\SemanticProxy\Transformer;

use HelloFuture\SemanticProxy\Source\SourceInterface;

interface TransformerInterface extends SourceInterface {

	public function getInner();

	public function getInputData();

	public function getOutputData();

}
