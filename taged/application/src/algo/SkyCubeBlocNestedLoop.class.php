<?php

/**
 * 
 * 
 * Concepts de base :
 * DataSet : Ensemble de données indexées par les RowID d'un côté et les ColID de l'autre
 * RowID : ID chiffré d'une ligne de donnée ( Tuple ) il est directement issu de l'ordre des données d'origine, il remplace l'ensemble des données d'identification des données d'origine
 * ColID : ID alphabétique d'une colonne de donnée ( Attribut ), il remplace les noms de colonnes des données d'origine
 * CuboideID : Combinaison des ColID composant le Cuboide
 */
class SkyCubeBlocNestedLoop extends SkyCube
{
    const CUBOIDE = 'CuboideBlocNestedLoop';
}
