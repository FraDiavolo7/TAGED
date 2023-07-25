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
	 * Initialise l'objet en utilisant les données d'entrée fournies. Si aucune donnée n'est fournie, les données de la requête ($_REQUEST) seront utilisées par défaut.
	 * 
	 * @param array|null $InputData Les données d'entrée à utiliser pour initialiser l'objet (facultatif). Si aucune donnée n'est fournie, les données de la requête ($_REQUEST) seront utilisées.
	 */
	public function __construct ( $InputData = NULL )
	{
		parent::__construct ( $InputData );
		$Data = ( NULL == $InputData ? $_REQUEST : $InputData );
	}

} // WSDefault
