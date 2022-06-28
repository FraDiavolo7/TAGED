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
 * @author Mickaël Martin-Nevot
 */

define('rGj65F1w', 'mickael-martin-nevot.com');

require_once $_SERVER['DOCUMENT_ROOT'] . '/_assets/config/main.inc.php';
require _MMN_INCLUDE_DIR_ . 'header.inc.php';

require _MMN_RECHERCHE_THESE_DIR_ . 'config/main.inc.php';

// ***

require _MMN_RECHERCHE_THESE_INCLUDE_DIR_ . 'database-vgdm.inc.php';

function renderRelation($link) {
    $query = 'SELECT round_date_id, game_id, round_num, turn, TO_CHAR(date, \'dd/mm/yyyy HH24:MI:SS\') AS date FROM DRound ORDER BY round_date_id ASC';
    $result = pg_query($link, $query);

    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        if (pg_numrows($result) != 0) {
?>
            <pre>
      \textsc{round\_id} & \textsc{game\_id} & \textsc{round\_num} & \textsc{turn} & \textsc{date} \\
      \midrule
<?php
            while ($row = pg_fetch_assoc($result)) {
?>
      <?php echo $row['round_date_id']; ?> & <?php echo $row['game_id']; ?> & <?php echo $row['round_num']; ?> & <?php echo $row['turn']; ?> & <?php echo $row['date']; ?> \\
<?php
            }
?>
</pre>
<?php
        }
    }
}

function renderHierarchy($link) {
    ?>
    <pre>
  \begin{dot2tex}
    graph G {
        d2toptions = "-s -c -tmath --nominsize --autosize -ftikz";
        node[shape=rect, style="fill=green!20"];

        All [texlbl="\textsc{All}$_\textsc{DRound}$"];
<?php
        $query = 'SELECT round_date_id, turn FROM DRound;';
        $result = pg_query($link, $query);

        if (!$result) {
            echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
        } else {
            while ($row = pg_fetch_assoc($result)) {
?>
        node<?php echo $row['round_date_id']; ?> [texlbl="<?php echo $row['turn']; ?>"];
<?php
            }
        }
?>

<?php

        $query = 'SELECT node_id, parent_node_id, name FROM DRoundHierarchy;';
        $result = pg_query($link, $query);

        if (!$result) {
            echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
        } else {
            while ($row = pg_fetch_assoc($result)) {
?>
        <?php echo ($row['parent_node_id'] == 0 ? 'All' : 'node' . $row['parent_node_id']); ?> -- node<?php echo $row['node_id']; ?>;
<?php
            }
}
?>
    }
  \end{dot2tex}
    </pre>
<?php
}

?>
                    <h1>DRound</h1>
                    <h2>Relation</h2>
<?php
                    renderRelation($link);
?>
                    <h2>Hierarchy</h2>
<?php
                    renderHierarchy($link);

// ***

require _MMN_INCLUDE_DIR_ . 'footer.inc.php';