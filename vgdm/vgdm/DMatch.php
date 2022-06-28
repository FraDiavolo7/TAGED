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
    $query = 'SELECT match_id, stroke_id, match_num, elem, color, special_four, beam, TO_CHAR(date, \'dd/mm/yyyy HH24:MI:SS\') AS date FROM DMatch ORDER BY match_id ASC';
    $result = pg_query($link, $query);

    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        if (pg_numrows($result) != 0) {
?>
            <pre>
      \textsc{match\_id} & \textsc{stroke\_id} & \textsc{match\_num} & \textsc{elem} & \textsc{color} & \textsc{special\_four} & \textsc{beam} & \textsc{date} \\
      \midrule
<?php
            while ($row = pg_fetch_assoc($result)) {
?>
      <?php echo $row['match_id']; ?> & <?php echo $row['stroke_id']; ?> & <?php echo $row['match_num']; ?> & <?php echo $row['elem']; ?> & <?php echo $row['color']; ?> & <?php echo $row['special_four'] == 't' ? '\checkmark' : '' ; ?> & <?php echo $row['beam'] == 't' ? '\checkmark' : '' ; ?> & <?php echo $row['date']; ?>\\
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

        All [texlbl="\textsc{All}$_\textsc{DMatch}$"];
<?php
        $query = 'SELECT match_id, elem FROM DMatch;';
        $result = pg_query($link, $query);

        if (!$result) {
            echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
        } else {
            while ($row = pg_fetch_assoc($result)) {
?>
        node<?php echo $row['match_id']; ?> [texlbl="<?php echo $row['elem']; ?>"];
<?php
            }
        }
?>

<?php

        $query = 'SELECT node_id, parent_node_id, name FROM DMatchHierarchy;';
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
                    <h1>DMatch</h1>
                    <h2>Relation</h2>
<?php
                    renderRelation($link);
?>
                    <h2>Hierarchy</h2>
<?php
                    renderHierarchy($link);

// ***

require _MMN_INCLUDE_DIR_ . 'footer.inc.php';