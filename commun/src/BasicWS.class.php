<?php

/**
 * Classe abstraite repr�sentant un service Web de base.
 *
 * @package Commun
 */
abstract class BasicWS
{
    /**
     * Constructeur de la classe BasicWS.
     *
     * @param mixed $InputData (facultatif) Donn�es d'entr�e � passer lors de l'instanciation de la classe.
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
     * Cette m�thode peut �tre red�finie dans les classes enfants pour impl�menter des fonctionnalit�s sp�cifiques.
     *
     * @return string R�sultat du service Web. Par d�faut, renvoie 'Unknown WS'.
     */
    public function serve()
    {
        return 'Unknown WS';
    }

    /**
     * Fonction utilis�e par les classes enfants comme fonction par d�faut.
     * Cette m�thode peut �tre red�finie dans les classes enfants pour fournir un comportement sp�cifique.
     *
     * @return string R�sultat par d�faut. Par d�faut, renvoie une cha�ne vide.
     */
    protected function nothing()
    {
        return '';
    }
    
} // BasicWS
