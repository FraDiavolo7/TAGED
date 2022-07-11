<?php

class WebServices
{
	public static function handle ( $WSName, $Data = NULL )
	{
		$Class = 'WS' . $WSName;
		
		$WS = new $Class ( $Data );
        
        echo $WS->serve ( );
	}
} // WebServices
