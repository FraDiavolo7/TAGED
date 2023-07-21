<?php

/**
 * Default WebService (doing nothing)
 * @package TAGED\WebServices
 */
class WSDefault extends TagedWS
{
	/**
	 * Constructeur de la classe WSDefault.
	 * 
	 * Initialise l'objet en utilisant les donn�es d'entr�e fournies. Si aucune donn�e n'est fournie, les donn�es de la requ�te ($_REQUEST) seront utilis�es par d�faut.
	 * 
	 * @param array|null $InputData Les donn�es d'entr�e � utiliser pour initialiser l'objet (facultatif). Si aucune donn�e n'est fournie, les donn�es de la requ�te ($_REQUEST) seront utilis�es.
	 */
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
	}

} // WSDefault
