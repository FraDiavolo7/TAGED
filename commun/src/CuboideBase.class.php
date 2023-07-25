<?php

/**
 * Classe de base abstraite pour les Cuboides.
 *
 * @package Commun
 */
abstract class CuboideBase
{
	/** Nom du Cuboide courant (pour le debuggage) */
    const CURRENT = '';
    
    /**
     * Constructeur de la classe CuboideBase.
     *
     * @param string $CuboideID ID du cuboide (combinaison de ColIDs).
     */
    public function __construct ( $CuboideID  )
    {
        $this->ID = $CuboideID;
        $this->DataSet = array ();
        $this->RowHeaders = array ();
        $this->ColIDs = array ();
        $this->IsValid = FALSE;
        $this->EquivalenceClasses = array ();
        
        $this->RowIDsInput = array ();
        $this->RowIDsFiltered = array ();
        $this->RowIDsComputed = array ();
    }

    /**
     * Méthode abstraite pour calculer le Cuboide.
     */
    abstract public function computeCuboide ( );

    /**
     * Enregistre une variable pour le débogage.
     *
     * @param mixed  $Var   Variable à enregistrer.
     * @param string $Label Libellé de la variable.
     * @param bool   $Show  Afficher ou enregistrer dans le log.
     * @param string $File  Nom du fichier (automatiquement renseigné).
     * @param int    $Line  Numéro de ligne (automatiquement renseigné).
     */
    protected function logVar ( $Var, $Label, $Show = FALSE, $File = __FILE__, $Line = __LINE__ )
    {
        $this->log ( $Label . ' : \'' . print_r ( $Var, TRUE ) . '\'', $Show, $File, $Line );
    }
    
    /**
     * Enregistre un message dans le log ou affiche si c'est le Cuboide courant.
     *
     * @param string $Text Message à enregistrer ou afficher.
     * @param bool   $Show Afficher le message ou enregistrer dans le log.
     * @param string $File Nom du fichier (automatiquement renseigné).
     * @param int    $Line Numéro de ligne (automatiquement renseigné).
     */
    protected function log ( $Text, $Show = FALSE, $File = __FILE__, $Line = __LINE__ )
    {
        if ( $this->ID == static::CURRENT ) 
        {
            if ( $Show )
            {
                echo "$File:$Line - $Text<br>";
            }
            else
            {
                Log::log ( $Text );
            }
        }
    }
    
    /**
     * Vérifie si une ligne donnée est équivalente à une ligne de référence.
     *
     * @param array $RawDataSet Tableau de données brutes.
     * @param int   $RowID      ID de la ligne à vérifier.
     * @return mixed Renvoie l'ID de la ligne équivalente si trouvée, sinon FALSE.
     */
    protected function isEquivalent ( $RawDataSet, $RowID )
    {
        foreach ( $this->EquivalenceClasses as $RowEq => $Parts )
        {
            if ( empty ( array_diff ( $RawDataSet [$RowEq], $RawDataSet [$RowID] ) ) )
            {
                return $RowEq;
            }
        }
        return FALSE;
    }
    
	/**
	 * ID du cuboide (combinaison de ColIDs).
	 *
	 * @return string
	 */
	public function getID()
	{
		return $this->ID;
	}

	/**
	 * Tableau de données indexé par RowID et ColID des mesures de la relation.
	 *
	 * @return array
	 */
	public function getDataSet()
	{
		return $this->DataSet;
	}

	/**
	 * Tableau des en-têtes de lignes indexé par RowID des identifiants de la relation.
	 *
	 * @return array
	 */
	public function getRowHeaders()
	{
		return $this->RowHeaders;
	}

	/**
	 * Tableau des identifiants de colonnes indexé par ColID des identifiants de mesure.
	 *
	 * @return array
	 */
	public function getColIDs()
	{
		return $this->ColIDs;
	}

	/**
	 * Indique si le cuboide est valide ou non.
	 *
	 * @return bool
	 */
	public function isValid()
	{
		return $this->IsValid;
	}

	/**
	 * Tableau des identifiants de lignes après le filtrage SkyLine.
	 *
	 * @return array
	 */
	public function getFilteredIDs()
	{
		return $this->RowIDsFiltered;
	}

	/**
	 * Tableau des identifiants de lignes après le traitement Taged.
	 *
	 * @return array
	 */
	public function getComputedIDs()
	{
		return $this->RowIDsComputed;
	}

	/**
	 * Retourne les en-têtes de lignes filtrées.
	 *
	 * @return array
	 */
	public function getRowHeadersFiltered()
	{
		$Result = array();
		foreach ($this->RowIDsFiltered as $RowID) {
			$Result[$RowID] = $this->RowHeaders[$RowID];
		}
		return $Result;
	}

	/**
	 * Retourne le jeu de données filtrées.
	 *
	 * @return array
	 */
	public function getDataSetFiltered()
	{
		$Result = array();
		foreach ($this->RowIDsFiltered as $RowID) {
			$Result[$RowID] = $this->DataSet[$RowID];
		}
		return $Result;
	}

	/**
	 * Retourne les en-têtes de lignes calculées.
	 *
	 * @return array
	 */
	public function getRowHeadersComputed()
	{
		$Result = array();
		foreach ($this->RowIDsComputed as $RowID) {
			$Result[$RowID] = $this->RowHeaders[$RowID];
		}
		return $Result;
	}

	/**
	 * Retourne le jeu de données calculées.
	 *
	 * @return array
	 */
	public function getDataSetComputed()
	{
		$Result = array();
		foreach ($this->RowIDsComputed as $RowID) {
			$Result[$RowID] = $this->DataSet[$RowID];
		}
		return $Result;
	}

	/**
	 * Retourne les classes d'équivalence pour les lignes.
	 *
	 * @param bool $AsArray     Si vrai, renvoie les classes d'équivalence sous forme de tableau.
	 * @param bool $OnlyFiltered Si vrai, renvoie uniquement les classes d'équivalence pour les lignes filtrées.
	 *
	 * @return mixed Retourne les classes d'équivalence sous forme de chaîne ou de tableau (selon $AsArray).
	 */
	public function getEquivalenceClasses($AsArray = FALSE, $OnlyFiltered = FALSE)
	{
		$Result = ($AsArray ? array() : '');

		if ($AsArray) {
			$Result = $this->EquivalenceClasses;
		} else {
			$Sep = '{';
			foreach ($this->EquivalenceClasses as $RowIndex => $Parts) {
				if (!$OnlyFiltered || in_array($RowIndex, $this->RowIDsFiltered)) {
					$Result .= $Sep . $RowIndex . implode('', $Parts);
					$Sep = ',';
				}
			}
			$Result .= '}';
		}
		return $Result;
	}
    
	/** @var string ID du cuboide (combinaison de ColIDs). */
    protected $ID;

    /** @var array Tableau de données indexé par RowID et ColID des mesures de la relation. */
    protected $DataSet;

    /** @var array Tableau des en-têtes de lignes indexé par RowID des identifiants de la relation. */
    protected $RowHeaders;

    /** @var array Tableau des identifiants de colonnes indexé par ColID des identifiants de mesure. */
    protected $ColIDs;

    /** @var bool Indique si le cuboide est valide ou non. */
    protected $IsValid;

    /** @var array Tableau des identifiants de lignes tels qu'ils ont été saisis. */
    protected $RowIDsInput;

    /** @var array Tableau des identifiants de lignes après le filtrage SkyLine. */
    protected $RowIDsFiltered;

    /** @var array Tableau des identifiants de lignes après le traitement Taged. */
    protected $RowIDsComputed;

    /** @var array Tableau des classes d'équivalence pour les lignes. */
    protected $EquivalenceClasses;

} // CuboideBase
