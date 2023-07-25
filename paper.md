---
title: 
  "TAGED : tool assisted game equilibrium design"

tags:
  - Video game data mining
  - Video game balancing
  - Game design
  - Tool assisted

authors:
  - name: Mickaël Martin Nevot
    orcid: 0009-0004-7893-3449
    affiliation: "1"
  - name: Christophe Delagarde
    orcid: 0009-0006-4498-9887
    affiliation: "2"

affiliations:
 - name: Aix-Marseille Université, France
   index: 1
 - name: SATT Sud-Est & ACTIVUS Group, France
   index: 2

date: ... ... 2023
bibliography: paper.bib
europar-doi: ...
---

# Summary

Creating video game, and bet on its success and popularity, is becomming a major challenge in the competitive environment of the world's number one cultural industry. Video games often are more and more complex to conceive, develop and balance.

Video games, especially online ones, can record every action of every player, thus generating billions of pieces of data on, sometimes, millions of players. To handle such a volume of information, assistance tools seem to be essential.

The TAGED method (for Tool Assisted Game Equilibrium Design) we propose offers a solution adaptable for single-player or multiplayer games with large volume of numerical or qualitative analysable data, being either intrinsic to the game itself (relative to characters, objects, levels...) or extrinsic, in relation to the players' behaviours (game durations, game loops, intermediate scoring, action sequences...).

La recherche d'un bon équilibre dans un jeu vidéo est un concept central car il va fortement impacter la satisfaction des joueurs. Dans le cadre d'un jeu solo, s'il est déséquilibré en terme de difficulté, les joueurs risquent très rapidement de s'en désintéresser par absence de défi, ou inversement, par frustration lié à l'impossibilité d'atteindre son objectif. Dans les jeux multijoueurs, la problématique gagne à la fois en importance et en complexité de part la multiplicité des situations qui explose. Il devient donc crucial de détecter au plus vite ces déséquilibres qui peuvent radicalement déstabiliser un jeu vidéo lorsqu'ils sont découverts par des joueurs qui en tirent un profit perçu comme immérité. L'objectif de notre travail de recherche est donc à la fois de détecter ces déséquilibres par une analyse des données du jeu vidéo et de son moteur de règles mais aussi de les découvrir grâce à une analyse en temps réel de la dynamique des comportements des joueurs.

# Statement of need

TAGED offre un large panel d'usages dans l'équilibrage en conception de jeu vidéo. Son utilisation par les concepteurs de jeux peut se faire durant toute la durée de vie d'un dispositif ludique : qu'il soit en conception, en développement ou en production.

TAGED est actuellement d'un TRL 5. Il porte pour le moment sur trois jeux vidéo de types très populaires et différents : jeu vidéo de collection pour Pokémon Showdown!, Hack 'n' slash pour Diablo III: Reaper of Souls et jeu vidéo *puzzle-game* de type Match 3 pour Open Match 3. Cette diversité sert de véritable démonstrateur à notre proposition, et ce même pour des jeux vidéo qui semblent avoir peu en commun.

Voici la schématisation du principe de fonctionnement de TAGED :
<!--- 
<p align="center">
	<img src="how-taged-works.png" alt="Principe de fonctionnement de TAGED" width="800">
</p>
 --->
 
![Principe de fonctionnement de TAGED](how-taged-works.png)

Et voici la présentation détaillée de TAGED :
<!--- 
<p align="center">
	<img src="taged-method.png" alt="Principe de fonctionnement de TAGED" width="800">
</p>
 --->
 
![Présentation détaillée de TAGED](taged-method.png)

TAGED est un outil d'extraction de données qui peut s'utiliser avec des données brutes d'entrée, réelles ou modélisées, intrinsèques au jeu vidéo, comme celles issues des mécaniques, dynamiques ou esthétiques de jeu [@books/lib/SalenZ04], ou bien extrinsèques comme celles extraites des *verbatims* des comportements de joueurs, déviants ou atypiques, et des informations de parties, solo ou multijoueur.

Les connaissances acquises grâce à TAGED permettent d'équilibrer le *gameplay* de jeux vidéo, en influençant notamment l'ajout ou la suppression de briques de *gameplay* [@Alvarez2018] dans le but de garantir au mieux l'état de *flow* [@books/Csikszentmihalyi09].

TAGED s'appuie sur les théories fondamentales suivantes :

- Skyline [@icde/BorzsonyiKS01] et Skycube [@vldb/YuanLLWYZ05]

- Cube de données [@datamine/GrayCBLRVPP97] et cube de données émergent [@dawak/NedjarCCL07]

- Cosky et Top-*k* [@Yiu:2007:Ongoing]

L'approche Skycube alliée à celle de cube de données émergent donne celle de Skycube émergent. Après classement, nous obtenons un Skycube émergent ordonné.

TAGED utilise les algorithmes :

- BNL : block-nested loop is used to compute SKyline

- IDEA : la plateforme algorithmique IDEA, et notamment les algorithmes E-IDEA et F-IDEA, eux-même basés sur l'algorithme BUC [@sigmod/BeyerR99], et comptible avec C-IDEA [@martinnevot:hal-02446921]

- DeepSky : uses multilevel Skylines [@Preisinger:2015:Approach] with Cosky ranking method

# Acknowledgements

We acknowledge the Société d'Accélération du Transfert des Technologies Sud-Est (SATT SE) for its financial support via Franck Orsatti. We also acknowledge contributions from Lotfi Lakhal and Sébastien Nedjar during the genesis of this project.

# References
