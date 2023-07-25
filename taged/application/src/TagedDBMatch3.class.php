<?php

/**
 * Cette classe représente la connexion à la base de données pour l'application TAGED en mode "Match-3".
 *
 * Elle étend la classe Database, qui contient les fonctionnalités de base pour la gestion des connexions à la base de données.
 * @package TAGED
 */
class TagedDBMatch3 extends Database
{
    /**
     * @var string $DBserver Adresse et port du serveur de la base de données PostgreSQL pour le mode Match-3.
     */
    protected static $DBserver = "pgsql:host=localhost;port=5432;dbname=taged_match3";

    /**
     * @var string $DBuser Nom d'utilisateur pour la connexion à la base de données PostgreSQL en mode Match-3.
     */
    protected static $DBuser = "postgres";

    /**
     * @var string $DBpwd Mot de passe pour la connexion à la base de données PostgreSQL en mode Match-3.
     */
    protected static $DBpwd = "plopplopP2";

    /**
     * @var PDO|null $PDO Objet PDO pour gérer la connexion à la base de données en mode Match-3.
     */
    protected static $PDO = NULL;

    /**
     * @var PDOStatement|null $PDOStatement Objet PDOStatement pour exécuter les requêtes préparées en mode Match-3.
     */
    protected static $PDOStatement = NULL;
}

