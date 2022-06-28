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
    $query = 'SELECT * FROM DPlayer ORDER BY player_loc_id ASC';
    $result = pg_query($link, $query);

    $playerArr = ['33cbe1d718e13fc8caaebd9f7cedb8b7' => 'P$_1$', '1519f090f98190a14c74b7cd9ac25502' => 'P$_2$', 'a1a7627509acf758aa2130819a123138' => 'P$_3$'];

    if (!$result) {
        echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
    } else {
        if (pg_numrows($result) != 0) {
?>
            <pre>
      \textsc{player\_id} & \textsc{country} & \textsc{region} & \textsc{city} & \textsc{ipaddress} & \textsc{platform} & \textsc{browser} & \textsc{lang} & \textsc{localization} \\
      \midrule
<?php
            while ($row = pg_fetch_assoc($result)) {
?>
      <?php echo $row['player_loc_id']; ?> & <?php echo $row['country']; ?> & <?php echo $row['region']; ?> & <?php echo $row['city']; ?> & <?php echo $row['ipaddress']; ?> & <?php echo $row['platform']; ?> & <?php echo $row['browser']; ?>  & <?php echo $row['lang']; ?> & <?php echo isset($playerArr[$row['player_loc']]) ? $playerArr[$row['player_loc']] : $row['player_loc'] ; ?> \\
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

        All [texlbl="\textsc{All}$_\textsc{DPlayer}$"];
<?php
        $query = 'SELECT player_loc_id, player_loc FROM DPlayer;';
        $result = pg_query($link, $query);

        if (!$result) {
            echo 'Impossible d\'exécuter la requête ', $query, ' : ', pg_last_error($link);
        } else {
            while ($row = pg_fetch_assoc($result)) {
?>
        node<?php echo $row['player_loc_id']; ?> [texlbl="<?php echo $row['player_loc']; ?>"];
<?php
            }
        }
?>

<?php

        $query = 'SELECT node_id, parent_node_id, name FROM DPlayerHierarchy;';
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
                    <h1>DPlayer</h1>
                    <h2>Relation</h2>
<?php
                    renderRelation($link);
?>
                    <h2>Hierarchy</h2>
<?php
                    renderHierarchy($link);
// ***

require _MMN_INCLUDE_DIR_ . 'footer.inc.php';