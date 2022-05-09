# taged

Collection :

Configuration :
fichier de vhost apache : /taged.conf
fichier de constantes PHP : /taged/application/define.php ( emplacement des donn�es DATA_HOME )
fichier de connexion BD : /taged/application/TagedDB.class.php

R�cup�ration des donn�es :
taged/application/script/TAGED_get_collection.sh appel� toutes les 5 minutes pour couvrir un maximum de donn�es

Traitement des donn�es :
taged/application/script/TAGED_process_collection.sh appel� toutes les heures pour �taler la charge

Calcul des statistiques de stockage :
taged/application/script/TAGED_set_stats.sh

Pages affich�es :
/ => Statistiques de stockage
/?sel=CollData => Ensemble des donn�es stock�es pour Collection

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
/taged/application/src/parse -> les parseurs (pour le moment que Collection)

Classes :
PageCollData -> affiche les donn�es charg�es en base.
CollParser -> traduit un contenu de fichier en donn�es utilisable par la base de donn�es
CollGame -> Ensemble structur� des donn�es d'un combat
CollPlayer -> Ensemble structur� des donn�es d'un joueur
CollTeam -> Ensemble structur� des donn�es d'une �quipe de pokemons
CollTable -> Ensemble des donn�es formatt�es dans un grand tableau
# taged