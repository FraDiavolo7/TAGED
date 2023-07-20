<?php

/**
 *
 * @package Commun
 */
class Form
{
	public static function getData ( $Name, $Default = '', $Data = NULL )
	{
		$UsedArray = ( is_array ( $Data ) ? $Data : $_REQUEST );
		$Result = $Default;
		if ( isset ( $UsedArray [ $Name ] ) )
		{
			$Result = $UsedArray [ $Name ];
		}
		return $Result;
	}
	
	public static function getFileName ( $Name )
	{
	    $UsedArray = $_FILES;
	    $Result = NULL;
	    if ( ( isset ( $UsedArray [ $Name ] ) ) && ( $UsedArray [ $Name ] [ 'error' ] == UPLOAD_ERR_OK ) )
	    {
	        $Result = $UsedArray [ $Name ] [ 'tmp_name' ];
	    }
	    return $Result;
	}
}