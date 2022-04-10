<?php

class TagedDB extends Database
{
    protected static $DBserver = "pgsql:host=localhost;port=5432;dbname=taged_collection";
    protected static $DBuser = "postgres";
    protected static $DBpwd = "plopplopP2";
    protected static $PDO = NULL;
    protected static $PDOStatement = NULL;
}

