<?php

// Cette classe sert à basculer du mode maintenance au mode production.

class Switcher
{
	public function __construct ( $InputData = NULL )
	{
		$Securite = Form::getData ( 'sec', '' );
		$Action   = Form::getData ( 'act', 'nothing' );
		
		if ( 'electr0lux' == $Securite )
		{
			if ( ( 'up' != $Action ) && ( 'down' != $Action ) )
			{
				$Action = 'nothing';
			}
			$this->$Action ();
		}
	}
	
	protected function nothing ()
	{
	}
	
	protected function up ()
	{
		$this->change ( 'application' );
	}
	
	protected function down ()
	{
	    $this->change ( 'maintenance' );
	}
	
	protected function change ( $NewTarget )
	{
		$Central = '../../http';
		system ( 'rm -f ' . $Central );
		system ('ln -s ' . $NewTarget . ' ' . $Central );
	}
}
