<?php

/**
 * Classe Form pour gérer les opérations liées aux formulaires.
 *
 * @package Commun
 */
class Form
{
    /**
     * Récupère les données du formulaire en fonction du nom du champ spécifié.
     *
     * @param string $Name Le nom du champ dont les données doivent être récupérées.
     * @param mixed $Default (Optionnel) La valeur par défaut à retourner si le champ n'est pas trouvé.
     * @param array|null $Data (Optionnel) Le tableau de données à partir duquel récupérer les informations.
     * @return mixed La valeur du champ si trouvé, sinon la valeur par défaut.
     */
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
	
    /**
     * Récupère le nom du fichier uploadé pour le champ spécifié.
     *
     * @param string $Name Le nom du champ dont récupérer le nom du fichier.
     * @return string|null Le chemin temporaire du fichier uploadé, ou NULL si le champ n'est pas trouvé ou l'upload a échoué.
     */
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
} // Form
