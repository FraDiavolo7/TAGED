<?php

/**
 * @package TAGED
 */
class TagedDBMatch3 extends Database
{
    protected static $DBserver = "pgsql:host=localhost;port=5432;dbname=taged_match3";
    protected static $DBuser = "postgres";
    protected static $DBpwd = "plopplopP2";
    protected static $PDO = NULL;
    protected static $PDOStatement = NULL; 
}

