<?php
/*
 * Copyright © 2013 Diveen
 * All Rights Reserved.
 *
 * This software is proprietary and confidential to Diveen ReplayParser
 * and is protected by copyright law as an unpublished work.
 *
 * Unauthorized access and disclosure strictly forbidden.
 */

/**
 * @author Mickaël Martin Nevot
 */

// Forbidden access.
if (!defined('rGj65F1w')) {
    trigger_error('Hacking attempt', E_USER_ERROR);
}

// PostgreSQL.
$link = pg_connect("host=postgresql-sypher.alwaysdata.net port=5432 dbname=sypher_vgdm user=sypher password=marmic") or die ('Pb de connexion au serveur');