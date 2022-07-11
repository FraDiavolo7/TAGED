# taged

Taged :

Configuration :
fichier de vhost apache : /taged.conf
fichier de constantes PHP : /taged/application/define.php ( emplacement des données DATA_HOME )

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
/taged/application/src/parse -> les parseurs
/taged/application/src/parse -> les webservices

Pages affichées :
/ => Statistiques de stockage

Collection :

Configuration :
fichier de connexion BD : /taged/application/TagedDBColl.class.php

Récupération des données :
taged/application/script/TAGED_get_collection.sh appelé toutes les 5 minutes pour couvrir un maximum de données

Traitement des données :
taged/application/script/TAGED_process_collection.sh appelé toutes les heures pour étaler la charge

Calcul des statistiques de stockage :
taged/application/script/TAGED_set_stats.sh

Pages affichées :
/?sel=CollData => Ensemble des données stockées pour Collection

Classes :
PageCollData -> affiche les données chargées en base.
CollParser -> traduit un contenu de fichier en données utilisable par la base de données
CollGame -> Ensemble structuré des données d'un combat
CollPlayer -> Ensemble structuré des données d'un joueur
CollTeam -> Ensemble structuré des données d'une équipe de pokemons
CollTable -> Ensemble des données formattées dans un grand tableau

Match3 :

Configuration :
fichier de connexion BD : /taged/application/TagedDBMatch3.class.php

Récupération des données :
Automatique dès lors que quelqu'un joue au jeu

Traitement des données :
Automatique dès lors que quelqu'un joue au jeu

Calcul des statistiques de stockage :
Uniquement en base de données, fait par le système à la demande

Pages affichées :
/?sel=Match3 => Le jeu (les données ne sont pas encore affichées car peu descriptives)

Classes :
PageMatch3 -> redirige vers le jeu.
M3Game -> Ensemble structuré des données d'une partie
M3Stroke -> Ensemble structuré des données d'un coup
M3Match -> Ensemble structuré des données d'un e correspondance dans le jeu

Hack'n Slash :

Configuration :
fichier de connexion BD : /taged/application/TagedDBHnS.class.php

Récupération des données :
taged/application/script/getDiablo.sh appelé manuellement, sera automatisé après évaluation des besoins de l'algorithme

Traitement des données :
taged/application/script/processHnS.sh appelé manuellement, sera automatisé après évaluation des besoins de l'algorithme (temps approximatif d'exécution : 16h)

Calcul des statistiques de stockage :
taged/application/script/TAGED_set_stats.sh

Pages affichées :
/?sel=HnSData => Ensemble des données stockées pour Collection

Classes :
PageHnSData -> affiche les données chargées en base.
HnSParser -> traduit un contenu de fichier Ladder en une liste d'adresses de fichier Hero
HnSHeroParser -> traduit un contenu de fichier Hero en données utilisable par la base de données
Hero -> Ensemble structuré des données d'un hero
HnSPlayer -> Ensemble structuré des données d'un joueur
HnSComp -> Ensemble structuré des données d'une compétence utilisée par un Hero
HnSItem -> Ensemble structuré des données d'un équipement utilisé par un Hero
HnSTable -> Ensemble des données formattées dans un grand tableau

# taged