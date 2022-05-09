# taged

Collection :

Configuration :
fichier de vhost apache : /taged.conf
fichier de constantes PHP : /taged/application/define.php ( emplacement des données DATA_HOME )
fichier de connexion BD : /taged/application/TagedDB.class.php

Récupération des données :
taged/application/script/TAGED_get_collection.sh appelé toutes les 5 minutes pour couvrir un maximum de données

Traitement des données :
taged/application/script/TAGED_process_collection.sh appelé toutes les heures pour étaler la charge

Calcul des statistiques de stockage :
taged/application/script/TAGED_set_stats.sh

Pages affichées :
/ => Statistiques de stockage
/?sel=CollData => Ensemble des données stockées pour Collection

Structure :
/commun -> contient des classes communes pour ajouter des fonctionnalités centrales
/taged -> contient les éléments du site et des scripts de chargement des données
/taged/maintenance -> site affiché en cas de bascule en maintenance 
/taged/application -> site effectif
/taged/application/script -> scripts de récupération des données et de gestion
/taged/application/src -> les sources de l'application
/taged/application/www -> la zone publique
/taged/application/src/models -> les modèles de données
/taged/application/src/modules -> les modules d'affichage (inutile)
/taged/application/src/pages -> les pages affichables
/taged/application/src/parse -> les parseurs (pour le moment que Collection)

Classes :
PageCollData -> affiche les données chargées en base.
CollParser -> traduit un contenu de fichier en données utilisable par la base de données
CollGame -> Ensemble structuré des données d'un combat
CollPlayer -> Ensemble structuré des données d'un joueur
CollTeam -> Ensemble structuré des données d'une équipe de pokemons
CollTable -> Ensemble des données formattées dans un grand tableau
# taged