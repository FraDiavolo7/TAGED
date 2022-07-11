# taged

Taged :

Configuration :
fichier de vhost apache : /taged.conf
fichier de constantes PHP : /taged/application/define.php ( emplacement des donn�es DATA_HOME )

Structure :
/commun -> contient des classes communes pour ajouter des fonctionnalit�s centrales
/taged -> contient les �l�ments du site et des scripts de chargement des donn�es
/taged/maintenance -> site affich� en cas de bascule en maintenance 
/taged/application -> site effectif
/taged/application/script -> scripts de r�cup�ration des donn�es et de gestion
/taged/application/src -> les sources de l'application
/taged/application/www -> la zone publique
/taged/application/src/models -> les mod�les de donn�es
/taged/application/src/modules -> les modules d'affichage (inutile)
/taged/application/src/pages -> les pages affichables
/taged/application/src/parse -> les parseurs
/taged/application/src/parse -> les webservices

Pages affich�es :
/ => Statistiques de stockage

Collection :

Configuration :
fichier de connexion BD : /taged/application/TagedDBColl.class.php

R�cup�ration des donn�es :
taged/application/script/TAGED_get_collection.sh appel� toutes les 5 minutes pour couvrir un maximum de donn�es

Traitement des donn�es :
taged/application/script/TAGED_process_collection.sh appel� toutes les heures pour �taler la charge

Calcul des statistiques de stockage :
taged/application/script/TAGED_set_stats.sh

Pages affich�es :
/?sel=CollData => Ensemble des donn�es stock�es pour Collection

Classes :
PageCollData -> affiche les donn�es charg�es en base.
CollParser -> traduit un contenu de fichier en donn�es utilisable par la base de donn�es
CollGame -> Ensemble structur� des donn�es d'un combat
CollPlayer -> Ensemble structur� des donn�es d'un joueur
CollTeam -> Ensemble structur� des donn�es d'une �quipe de pokemons
CollTable -> Ensemble des donn�es formatt�es dans un grand tableau

Match3 :

Configuration :
fichier de connexion BD : /taged/application/TagedDBMatch3.class.php

R�cup�ration des donn�es :
Automatique d�s lors que quelqu'un joue au jeu

Traitement des donn�es :
Automatique d�s lors que quelqu'un joue au jeu

Calcul des statistiques de stockage :
Uniquement en base de donn�es, fait par le syst�me � la demande

Pages affich�es :
/?sel=Match3 => Le jeu (les donn�es ne sont pas encore affich�es car peu descriptives)

Classes :
PageMatch3 -> redirige vers le jeu.
M3Game -> Ensemble structur� des donn�es d'une partie
M3Stroke -> Ensemble structur� des donn�es d'un coup
M3Match -> Ensemble structur� des donn�es d'un e correspondance dans le jeu

Hack'n Slash :

Configuration :
fichier de connexion BD : /taged/application/TagedDBHnS.class.php

R�cup�ration des donn�es :
taged/application/script/getDiablo.sh appel� manuellement, sera automatis� apr�s �valuation des besoins de l'algorithme

Traitement des donn�es :
taged/application/script/processHnS.sh appel� manuellement, sera automatis� apr�s �valuation des besoins de l'algorithme (temps approximatif d'ex�cution : 16h)

Calcul des statistiques de stockage :
taged/application/script/TAGED_set_stats.sh

Pages affich�es :
/?sel=HnSData => Ensemble des donn�es stock�es pour Collection

Classes :
PageHnSData -> affiche les donn�es charg�es en base.
HnSParser -> traduit un contenu de fichier Ladder en une liste d'adresses de fichier Hero
HnSHeroParser -> traduit un contenu de fichier Hero en donn�es utilisable par la base de donn�es
Hero -> Ensemble structur� des donn�es d'un hero
HnSPlayer -> Ensemble structur� des donn�es d'un joueur
HnSComp -> Ensemble structur� des donn�es d'une comp�tence utilis�e par un Hero
HnSItem -> Ensemble structur� des donn�es d'un �quipement utilis� par un Hero
HnSTable -> Ensemble des donn�es formatt�es dans un grand tableau

# taged