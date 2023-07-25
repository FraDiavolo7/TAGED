<?php

/**
 * Classe abstraite représentant un service Web de base.
 *
 * @package Commun
 */
abstract class BasicWS
{
    /**
     * Constructeur de la classe BasicWS.
     *
     * @param mixed $InputData (facultatif) Données d'entrée à passer lors de l'instanciation de la classe.
     */
    public function __construct($InputData = NULL)
    {
    }

    /**
     * Destructeur de la classe BasicWS.
     */
    public function __destruct()
    {
    }

    /**
     * Fonction principale du service Web.
     * Cette méthode peut être redéfinie dans les classes enfants pour implémenter des fonctionnalités spécifiques.
     *
     * @return string Résultat du service Web. Par défaut, renvoie 'Unknown WS'.
     */
    public function serve()
    {
        return 'Unknown WS';
    }

    /**
     * Fonction utilisée par les classes enfants comme fonction par défaut.
     * Cette méthode peut être redéfinie dans les classes enfants pour fournir un comportement spécifique.
     *
     * @return string Résultat par défaut. Par défaut, renvoie une chaîne vide.
     */
    protected function nothing()
    {
        return '';
    }
    
} // BasicWS
