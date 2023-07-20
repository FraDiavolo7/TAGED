<?php

/**
 * Redirection to Match 3 game
 * @package TAGED\Pages
 */
class PageMatch3 extends TagedPage
{
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$this->redirect ( '/match3' );

	}
} // PageMatch3
