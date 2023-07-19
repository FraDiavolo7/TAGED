<?php

/**
 *
 * @package Commun
 */
class Page
{
	public static function handle ( $PageName, $Data = NULL )
	{
		$Class = 'Page' . $PageName;
		
		$Page = new $Class ( $Data );
	}
} // Page
