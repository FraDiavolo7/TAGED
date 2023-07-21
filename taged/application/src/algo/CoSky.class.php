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
     * @param SkyCube $SkyCube Le SkyCube contenant les donn�es � modifier.
	 * @param int $k Le compte de donn�es � conserver
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
	 * Interpr�te les r�sultats de CoSky
	 * @param array $Results Les r�sultats � interpr�ter
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
	 * M�thode prot�g�e pour trier un tableau d'entr�es en fonction des scores associ�s.
	 *
	 * @param array $EntryA La premi�re entr�e � comparer.
	 * @param array $EntryB La deuxi�me entr�e � comparer.
	 * @return int Retourne 0 si les scores sont �gaux, 1 si le score de $EntryA est inf�rieur � celui de $EntryB, -1 sinon.
	 */
	protected function sortScores ( $EntryA, $EntryB )
	{
		// R�cup�rer les scores pour les entr�es, en prenant 0 par d�faut s'ils ne sont pas d�finis.
		$ValueA = $EntryA ['Score'] ?? 0;
		$ValueB = $EntryB ['Score'] ?? 0;

		// Comparer les scores et retourner le r�sultat appropri�.
		return ( $ValueA == $ValueB ? 0 : ( $ValueA < $ValueB ? 1 : -1 ) );
	}

    
	/**
	 * M�thode publique pour obtenir les k meilleurs scores des tuples avec les attributs associ�s.
	 *
	 * @return array Retourne un tableau contenant les k meilleurs scores des tuples avec leurs attributs associ�s.
	 */
	public function getScores ()
	{
		$Scores = array ();

		// Remplir le tableau Scores avec les tuples et leurs attributs associ�s
		foreach ( $this->Tuples as $Tuple ) 
		{
			$Scores [$Tuple]['RowId'] = $Tuple;

			// R�cup�rer les valeurs des attributs pour le tuple en cours
			foreach ( $this->Data as $Attr => $Data ) 
			{
				$Scores [$Tuple][$Attr] = $Data [$Tuple];
			}

			// Ajouter le score du tuple au tableau Scores
			$Scores [$Tuple]['Score'] = $this->Score [$Tuple];
		}

		// Trier le tableau Scores en utilisant la m�thode sortScores de cette classe
		usort ( $Scores, array ( $this, 'sortScores' ) );

		// R�cup�rer les k meilleurs scores en utilisant array_slice
		return array_slice ( $Scores, 0, $this->k );
	}

	/**
	 * M�thode d'acc�s pour obtenir les donn�es de l'algorithme.
	 *
	 * @return array Retourne un tableau contenant les donn�es de l'algorithme.
	 */
	public function getData()
	{
		return $this->Data;
	}

	/**
	 * M�thode d'acc�s pour obtenir la valeur de N (nombre total de tuples).
	 *
	 * @return int Retourne la valeur de N.
	 */
	public function getN() { return $this->N; }

	/**
	 * M�thode d'acc�s pour obtenir la valeur de Gini.
	 *
	 * @return float Retourne la valeur de Gini.
	 */
	public function getGini() { return $this->Gini; }

	/**
	 * M�thode d'acc�s pour obtenir la valeur de W.
	 *
	 * @return float Retourne la valeur de W.
	 */
	public function getW() { return $this->W; }

	/**
	 * M�thode d'acc�s pour obtenir la valeur de P.
	 *
	 * @return float Retourne la valeur de P.
	 */
	public function getP() { return $this->P; }

	/**
	 * M�thode d'acc�s pour obtenir la valeur d'Ideal.
	 *
	 * @return float Retourne la valeur d'Ideal.
	 */
	public function getIdeal() { return $this->Ideal; }

	/**
	 * M�thode d'acc�s pour obtenir la somme des attributs (SumAttr).
	 *
	 * @return array Retourne un tableau contenant la somme des attributs.
	 */
	public function getSumAttr() { return $this->SumAttr; }

	/**
	 * M�thode d'acc�s pour obtenir la somme des carr�s des N (SumNSquare).
	 *
	 * @return float Retourne la somme des carr�s des N.
	 */
	public function getSumNSquare() { return $this->SumNSquare; }

	/**
	 * M�thode d'acc�s pour obtenir la somme des carr�s des P (SumPSquare).
	 *
	 * @return float Retourne la somme des carr�s des P.
	 */
	public function getSumPSquare() { return $this->SumPSquare; }

	/**
	 * M�thode d'acc�s pour obtenir la somme des carr�s des valeurs id�ales (SumIdealSquare).
	 *
	 * @return float Retourne la somme des carr�s des valeurs id�ales.
	 */
	public function getSumIdealSquare() { return $this->SumIdealSquare; }

	/**
	 * M�thode d'acc�s pour obtenir la somme des valeurs Gini (SumGini).
	 *
	 * @return float Retourne la somme des valeurs Gini.
	 */
	public function getSumGini() { return $this->SumGini; }

	/**
	 * M�thode d'acc�s pour obtenir la racine carr�e de la somme des carr�s des P (SqrtSumPSquare).
	 *
	 * @return float Retourne la racine carr�e de la somme des carr�s des P.
	 */
	public function getSqrtSumPSquare() { return $this->SqrtSumPSquare; }

	/**
	 * M�thode d'acc�s pour obtenir la racine carr�e de la somme des carr�s des valeurs id�ales (SqrtSumIdealSquare).
	 *
	 * @return float Retourne la racine carr�e de la somme des carr�s des valeurs id�ales.
	 */
	public function getSqrtSumIdealSquare() { return $this->SqrtSumIdealSquare; }

	/**
	 * Nombre de tuples � consid�rer dans l'algorithme.
	 * @var int
	 */
	protected $k;

	/**
	 * Instance de la classe SkyCube utilis�e pour les calculs.
	 * @var SkyCube
	 */
	protected $SkyCube;

	/**
	 * Tableau contenant les donn�es d'entr�e pour l'algorithme.
	 * @var array
	 */
	protected $Data;

	/**
	 * Tableau contenant les tuples sur lesquels l'algorithme va op�rer.
	 * @var array
	 */
	protected $Tuples;

	/**
	 * MinMax pour l'algorithme de Skycube.
	 * @var int
	 */
	protected $MinMax;

	/**
	 * Nombre total de tuples dans les donn�es d'entr�e.
	 * @var int
	 */
	protected $N;

	/**
	 * Valeur de l'indice de Gini calcul�e pour les donn�es d'entr�e.
	 * @var float
	 */
	protected $Gini;

	/**
	 * Valeur de l'indice W calcul�e pour les donn�es d'entr�e.
	 * @var float
	 */
	protected $W;

	/**
	 * Valeur de l'indice P calcul�e pour les donn�es d'entr�e.
	 * @var float
	 */
	protected $P;

	/**
	 * Valeur de l'indice Ideal calcul�e pour les donn�es d'entr�e.
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
	 * Somme des carr�s des valeurs des N (tuples) par attribut.
	 * @var array
	 */
	protected $SumNSquare;

	/**
	 * Somme des carr�s des valeurs des P (attributs) par tuple.
	 * @var array
	 */
	protected $SumPSquare;

	/**
	 * Somme des carr�s des valeurs id�ales (Ideal) par tuple.
	 * @var float
	 */
	protected $SumIdealSquare;

	/**
	 * Somme des valeurs Gini par tuple.
	 * @var float
	 */
	protected $SumGini;

	/**
	 * Racine carr�e des sommes des carr�s des valeurs des P (attributs) par tuple.
	 * @var array
	 */
	protected $SqrtSumPSquare;

	/**
	 * Racine carr�e des sommes des carr�s des valeurs id�ales (Ideal) par tuple.
	 * @var float
	 */
	protected $SqrtSumIdealSquare;

    
}