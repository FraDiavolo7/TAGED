<?php

class PageMatch3 extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$this->redirect ( '/match3' );

	}
} // PageMatch3
