<?php

/**
 * @deprecated
 * @package Deprecated
 */
class AggCollRequete extends AggregateFile
{
    protected static $Table = "vw_equipe";
    protected static $DBClass = "TagedDBColl";
    protected static $File = "/home/taged/data/aggregates/" . __CLASS__;
    
    /* 
    protected function retrieveData ()
    {
        Log::fct_enter ( __METHOD__ );
        $DbClass = static::$DBClass;
        
        $DbClass::execute ( "
WITH TNbVictoire AS (
    SELECT CASE WHEN (TC.elo1 <= 1100) THEN 'NOOB' ELSE 'PGM' END AS Elo,
           TC.Liste1 AS Liste1,
           TC.Liste2 AS Liste2,
           COUNT(*) AS NbVictoire
    FROM vw_combat_12 TC
    WHERE TC.gagnant IN (0, 1)
    GROUP BY Elo, Liste1, Liste2
        ), 
TNbCombat AS (
    SELECT CASE WHEN (TC.elo1 <= 1100) THEN 'NOOB' ELSE 'PGM' END AS Elo,
           TC.Liste1 AS Liste1,
           TC.Liste2 AS Liste2,
           COUNT(*) AS NbCombat
    FROM vw_combat_12 TC
    GROUP BY Elo, Liste1, Liste2
        ), 
TMain AS (
    SELECT CASE WHEN (TC.elo1 <= 1100) THEN 'NOOB' ELSE 'PGM' END AS ELoRank,
           TC.Liste1 AS Seq1,
           TC.Liste2 AS Seq2,
           NbVictoire AS NbV,
           NbCombat AS NbC,
           CAST(ROUND(CAST(NbVictoire AS DECIMAL) / CAST(NbCombat AS DECIMAL), 2) * 100 AS INTEGER) AS TxVictoire
    FROM vw_combat_12 TC, TNbVictoire TNV, TNbCombat TNC
    WHERE TC.Liste1 = TNV.Liste1 AND TNV.Liste1 = TNC.Liste1
    AND TC.Liste2 = TNV.Liste2 AND TNV.Liste2 = TNC.Liste2
    GROUP BY ELoRank, Seq1, Seq2, NbV, NbC, TxVictoire
        )
SELECT *
FROM TMain
WHERE NbC > 3
ORDER BY EloRank, Seq1, Seq2;" );
        //WHERE NbC > 2 AND TxVictoire > 0.70
        
        $Results = $DbClass::getResults ( );
        
        Log::fct_exit ( __METHOD__ );
        
        return $Results;
    }
    */
}

