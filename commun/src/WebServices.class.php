<?php

class WebServices
{
	public static function handle ( $WSName, $Data = NULL )
	{
		$Class = 'WS' . $PageName;
		
		$WS = new $Class ( $Data );
	}
} // WebServices
