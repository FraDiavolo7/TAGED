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

define('rGj65F1w', 'mickael-martin-nevot.com');

require_once $_SERVER['DOCUMENT_ROOT'] . '/_assets/config/main.inc.php';
require _MMN_INCLUDE_DIR_ . 'header.inc.php';

require _MMN_RECHERCHE_THESE_DIR_ . 'config/main.inc.php';

// ***

require _MMN_RECHERCHE_THESE_INCLUDE_DIR_ . 'database-vgdm.inc.php';

function renderRelation($link) {
    $query = 'SELECT * FROM FactOM3 ORDER BY id ASC';
    $result = pg_query($link, $query);

    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        if (pg_numrows($result) != 0) {
            ?>
            <pre>
      \textsc{id} & \textsc{player\_id} & \textsc{round\_id} & \textsc{match\_id} & \textsc{time} & \textsc{duration} & \textsc{length} & \textsc{score} & \textsc{shape} \\
      \midrule
                <?php
                while ($row = pg_fetch_assoc($result)) {
                    ?>
                    <?php echo $row['id']; ?> & <?php echo $row['player_id']; ?> & <?php echo $row['round_id']; ?> & <?php echo $row['match_id']; ?> & <?php echo $row['time']; ?> & <?php echo $row['duration']; ?> & <?php echo $row['length']; ?> & <?php echo $row['score']; ?> & <?php echo $row['shape']; ?> \\
                    <?php
                }
                ?>
</pre>
            <?php
        }
    }
}

function renderRelationLbl($link) {
    $query = 'SELECT * FROM FactOM3Lbl ORDER BY id ASC';
    $result = pg_query($link, $query);

    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        if (pg_numrows($result) != 0) {
            ?>
            <pre>
      \textsc{id} & \textsc{player\_id} & \textsc{round\_id} & \textsc{match\_id} & \textsc{time} & \textsc{duration} & \textsc{length} & \textsc{score} & \textsc{shape} \\
      \midrule
                <?php
                while ($row = pg_fetch_assoc($result)) {
                    ?>
                    <?php echo $row['id']; ?> & <?php echo $row['player_id']; ?> & <?php echo $row['round_id']; ?> & <?php echo $row['match_id']; ?> & <?php echo $row['time']; ?> & <?php echo $row['duration']; ?> & <?php echo $row['length']; ?> & <?php echo $row['score']; ?> & <?php echo $row['shape']; ?> \\
                    <?php
                }
                ?>
</pre>
            <?php
        }
    }
}

?>
                    <h1>FactOM3</h1>
                    <h2>Relation</h2>
<?php
                    renderRelation($link);
                    renderRelationLbl($link);

// ***

require _MMN_INCLUDE_DIR_ . 'footer.inc.php';