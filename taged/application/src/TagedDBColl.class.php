<?php

/**
 * Cette classe représente la connexion à la base de données pour l'application TAGED.
 *
 * Elle étend la classe Database, qui contient les fonctionnalités de base pour la gestion des connexions à la base de données.
 * @package TAGED
 */
class TagedDBColl extends Database
{
    /**
     * @var string $DBserver Adresse et port du serveur de la base de données PostgreSQL.
     */
    protected static $DBserver = "pgsql:host=localhost;port=5432;dbname=taged_collection";

    /**
     * @var string $DBuser Nom d'utilisateur pour la connexion à la base de données PostgreSQL.
     */
    protected static $DBuser = "postgres";

    /**
     * @var string $DBpwd Mot de passe pour la connexion à la base de données PostgreSQL.
     */
    protected static $DBpwd = "plopplopP2";

    /**
     * @var PDO|null $PDO Objet PDO pour gérer la connexion à la base de données.
     */
    protected static $PDO = NULL;

    /**
     * @var PDOStatement|null $PDOStatement Objet PDOStatement pour exécuter les requêtes préparées.
     */
    protected static $PDOStatement = NULL;
}

//Log::setDebug ( __FILE__ );
