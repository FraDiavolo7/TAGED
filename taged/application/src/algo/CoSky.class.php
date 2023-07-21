<?php


/**
 * Computes the CoSky algorithm
 * @package TAGED\Algo
 */
class CoSky
{
	/**
	 * Contructeur pour CoSky
	 *
     * @param SkyCube $SkyCube Le SkyCube contenant les données à modifier.
	 * @param int $k Le compte de données à conserver
	 */
    public function __construct ( $SkyCube, $k = 2 )
    {
        $this->SkyCube = $SkyCube;
        $this->k = $k;
    }
    
	/**
	 * Execute le calcul de CoSky
	 * @return nothing?
	 */
    public function run ()
    {
        $this->prepare ();
        
        $Results = '';
        
        return $this->interpret ( $Results );
    }
    
	/**
	 * Prepare le calcul de CoSky
	 */
    protected function prepare ()
    {
        $CuboidesIDs  = $this->SkyCube->getCuboideIDs ( FALSE );
        $CuboideLevel = reset ( $CuboidesIDs  );
        $CuboideID    = reset ( $CuboideLevel );
        
        $Cuboide = $this->SkyCube->getCuboide ( $CuboideID );
        $DataSet = $Cuboide->getDataSetFiltered ();
        $ColIDs = $Cuboide->getColIDs ();
        $HeadersCol = array_values ( $this->SkyCube->getColIDs () );
        
        foreach ( $DataSet as $RowID => $Row )
        {
            foreach ( $Row as $ColID => $Value )
            {
                $this->Data [$ColIDs [$ColID]] [$RowID + 1] = $Value;
            }
            $this->Tuples [] = $RowID + 1;
        }
        
        foreach ( $this->Data as $Attr => $Values )
        {
            $this->Ideal   [$Attr] = 100;
            $this->MinMax  [$Attr] = 'min';
            $this->SumAttr [$Attr] = array_sum ( $Values );
        }
    }
    
    /**
     * Do not read, not yet published
	 * Interprète les résultats de CoSky
	 * @param array $Results Les résultats à interpréter
     */
    protected function interpret ( $Results )
    {
        foreach ( $this->Tuples as $Tuple )
        {
            $this->SumPSquare [$Tuple] = 0;
        }
        
        $this->SumGini = 0;
        $this->SumIdealSquare = 0;
        
        foreach ( $this->Data as $Attr => $Data )
        {
            $this->SumNSquare [$Attr] = 0;
            foreach ( $Data as $Tuple => $Value )
            {
                $Ni = $Value / $this->SumAttr [$Attr];
                $this->N [$Attr] [$Tuple]  = $Ni;
                $this->SumNSquare [$Attr] += $Ni ** 2;
            }
            
            $this->Gini [$Attr] = 1 - $this->SumNSquare [$Attr];
            $this->SumGini += $this->Gini [$Attr];
        }
        
        foreach ( $this->Data as $Attr => $Data )
        {
            $Compare = $this->MinMax [$Attr];
            foreach ( $Data as $Tuple => $Value )
            {
                $this->W [$Attr] [$Tuple]  = $this->Gini [$Attr] / $this->SumGini;
                $this->P [$Attr] [$Tuple]  = $this->W [$Attr] [$Tuple] * $this->N [$Attr] [$Tuple];
                $this->SumPSquare [$Tuple] += $this->P [$Attr] [$Tuple] ** 2;
                $this->Ideal [$Attr] = $Compare ( $this->P [$Attr] [$Tuple], $this->Ideal [$Attr] );
            }
        }
        
        foreach ( $this->Ideal as $Attr => $Ideal )
        {
            $this->SumIdealSquare += $Ideal ** 2;
        }
        
        $this->SqrtSumIdealSquare = sqrt ( $this->SumIdealSquare );

        foreach ( $this->Tuples as $Tuple )
        {
            $this->SqrtSumPSquare [$Tuple] = sqrt ( $this->SumPSquare [$Tuple] );
        }
            
        foreach ( $this->Tuples as $Tuple )
        {
            $SumIP = 0;
            foreach ( $this->Data as $Attr => $Data )
            {
                $SumIP += $this->Ideal [$Attr] * $this->P [$Attr] [$Tuple];
            }
            $this->Score [$Tuple] = $SumIP / ( $this->SqrtSumPSquare [$Tuple] * $this->SqrtSumIdealSquare );
        }
    }
    
	/**
	 * Méthode protégée pour trier un tableau d'entrées en fonction des scores associés.
	 *
	 * @param array $EntryA La première entrée à comparer.
	 * @param array $EntryB La deuxième entrée à comparer.
	 * @return int Retourne 0 si les scores sont égaux, 1 si le score de $EntryA est inférieur à celui de $EntryB, -1 sinon.
	 */
	protected function sortScores ( $EntryA, $EntryB )
	{
		// Récupérer les scores pour les entrées, en prenant 0 par défaut s'ils ne sont pas définis.
		$ValueA = $EntryA ['Score'] ?? 0;
		$ValueB = $EntryB ['Score'] ?? 0;

		// Comparer les scores et retourner le résultat approprié.
		return ( $ValueA == $ValueB ? 0 : ( $ValueA < $ValueB ? 1 : -1 ) );
	}

    
	/**
	 * Méthode publique pour obtenir les k meilleurs scores des tuples avec les attributs associés.
	 *
	 * @return array Retourne un tableau contenant les k meilleurs scores des tuples avec leurs attributs associés.
	 */
	public function getScores ()
	{
		$Scores = array ();

		// Remplir le tableau Scores avec les tuples et leurs attributs associés
		foreach ( $this->Tuples as $Tuple ) 
		{
			$Scores [$Tuple]['RowId'] = $Tuple;

			// Récupérer les valeurs des attributs pour le tuple en cours
			foreach ( $this->Data as $Attr => $Data ) 
			{
				$Scores [$Tuple][$Attr] = $Data [$Tuple];
			}

			// Ajouter le score du tuple au tableau Scores
			$Scores [$Tuple]['Score'] = $this->Score [$Tuple];
		}

		// Trier le tableau Scores en utilisant la méthode sortScores de cette classe
		usort ( $Scores, array ( $this, 'sortScores' ) );

		// Récupérer les k meilleurs scores en utilisant array_slice
		return array_slice ( $Scores, 0, $this->k );
	}

	/**
	 * Méthode d'accès pour obtenir les données de l'algorithme.
	 *
	 * @return array Retourne un tableau contenant les données de l'algorithme.
	 */
	public function getData()
	{
		return $this->Data;
	}

	/**
	 * Méthode d'accès pour obtenir la valeur de N (nombre total de tuples).
	 *
	 * @return int Retourne la valeur de N.
	 */
	public function getN() { return $this->N; }

	/**
	 * Méthode d'accès pour obtenir la valeur de Gini.
	 *
	 * @return float Retourne la valeur de Gini.
	 */
	public function getGini() { return $this->Gini; }

	/**
	 * Méthode d'accès pour obtenir la valeur de W.
	 *
	 * @return float Retourne la valeur de W.
	 */
	public function getW() { return $this->W; }

	/**
	 * Méthode d'accès pour obtenir la valeur de P.
	 *
	 * @return float Retourne la valeur de P.
	 */
	public function getP() { return $this->P; }

	/**
	 * Méthode d'accès pour obtenir la valeur d'Ideal.
	 *
	 * @return float Retourne la valeur d'Ideal.
	 */
	public function getIdeal() { return $this->Ideal; }

	/**
	 * Méthode d'accès pour obtenir la somme des attributs (SumAttr).
	 *
	 * @return array Retourne un tableau contenant la somme des attributs.
	 */
	public function getSumAttr() { return $this->SumAttr; }

	/**
	 * Méthode d'accès pour obtenir la somme des carrés des N (SumNSquare).
	 *
	 * @return float Retourne la somme des carrés des N.
	 */
	public function getSumNSquare() { return $this->SumNSquare; }

	/**
	 * Méthode d'accès pour obtenir la somme des carrés des P (SumPSquare).
	 *
	 * @return float Retourne la somme des carrés des P.
	 */
	public function getSumPSquare() { return $this->SumPSquare; }

	/**
	 * Méthode d'accès pour obtenir la somme des carrés des valeurs idéales (SumIdealSquare).
	 *
	 * @return float Retourne la somme des carrés des valeurs idéales.
	 */
	public function getSumIdealSquare() { return $this->SumIdealSquare; }

	/**
	 * Méthode d'accès pour obtenir la somme des valeurs Gini (SumGini).
	 *
	 * @return float Retourne la somme des valeurs Gini.
	 */
	public function getSumGini() { return $this->SumGini; }

	/**
	 * Méthode d'accès pour obtenir la racine carrée de la somme des carrés des P (SqrtSumPSquare).
	 *
	 * @return float Retourne la racine carrée de la somme des carrés des P.
	 */
	public function getSqrtSumPSquare() { return $this->SqrtSumPSquare; }

	/**
	 * Méthode d'accès pour obtenir la racine carrée de la somme des carrés des valeurs idéales (SqrtSumIdealSquare).
	 *
	 * @return float Retourne la racine carrée de la somme des carrés des valeurs idéales.
	 */
	public function getSqrtSumIdealSquare() { return $this->SqrtSumIdealSquare; }

	/**
	 * Nombre de tuples à considérer dans l'algorithme.
	 * @var int
	 */
	protected $k;

	/**
	 * Instance de la classe SkyCube utilisée pour les calculs.
	 * @var SkyCube
	 */
	protected $SkyCube;

	/**
	 * Tableau contenant les données d'entrée pour l'algorithme.
	 * @var array
	 */
	protected $Data;

	/**
	 * Tableau contenant les tuples sur lesquels l'algorithme va opérer.
	 * @var array
	 */
	protected $Tuples;

	/**
	 * MinMax pour l'algorithme de Skycube.
	 * @var int
	 */
	protected $MinMax;

	/**
	 * Nombre total de tuples dans les données d'entrée.
	 * @var int
	 */
	protected $N;

	/**
	 * Valeur de l'indice de Gini calculée pour les données d'entrée.
	 * @var float
	 */
	protected $Gini;

	/**
	 * Valeur de l'indice W calculée pour les données d'entrée.
	 * @var float
	 */
	protected $W;

	/**
	 * Valeur de l'indice P calculée pour les données d'entrée.
	 * @var float
	 */
	protected $P;

	/**
	 * Valeur de l'indice Ideal calculée pour les données d'entrée.
	 * @var float
	 */
	protected $Ideal;

	/**
	 * Tableau associatif contenant les scores pour chaque tuple.
	 * @var array
	 */
	protected $Score;

	/**
	 * Somme des attributs par attribut.
	 * @var array
	 */
	protected $SumAttr;

	/**
	 * Somme des carrés des valeurs des N (tuples) par attribut.
	 * @var array
	 */
	protected $SumNSquare;

	/**
	 * Somme des carrés des valeurs des P (attributs) par tuple.
	 * @var array
	 */
	protected $SumPSquare;

	/**
	 * Somme des carrés des valeurs idéales (Ideal) par tuple.
	 * @var float
	 */
	protected $SumIdealSquare;

	/**
	 * Somme des valeurs Gini par tuple.
	 * @var float
	 */
	protected $SumGini;

	/**
	 * Racine carrée des sommes des carrés des valeurs des P (attributs) par tuple.
	 * @var array
	 */
	protected $SqrtSumPSquare;

	/**
	 * Racine carrée des sommes des carrés des valeurs idéales (Ideal) par tuple.
	 * @var float
	 */
	protected $SqrtSumIdealSquare;

    
}