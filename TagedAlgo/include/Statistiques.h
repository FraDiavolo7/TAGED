/*
 * Statistiques.h
 *
 *  Created on: 29 juin 2010
 *      Author: nedjar
 */

#ifndef STATISTIQUES_H_
#define STATISTIQUES_H_

// Variables globales pour simplifier les stat de BUC
extern unsigned long long int NbBottomUpCubeCall;
extern unsigned long long int NbSortCall;
extern unsigned long long int NbElemUsedSort;
extern unsigned long long int NbOptimizationCall;
extern unsigned long long int NbLineInCube;
extern unsigned long long int NbDoublons;
extern unsigned long long int TempsCalculCube;
extern unsigned long long int TempsCalculCubeEmergent;

// Variables globales pour simplifier les stat de BUC + Filtrage
extern unsigned long long int NbBottomUpCubeFilteredCall;
extern unsigned long long int NbLineInCubeEmergent;
extern unsigned long long int NbLineInCubeComplet;
extern unsigned long long int NbFiltrage;
extern unsigned long long int TempsFiltrage;

/**
 * @brief Fonction InitStatistiques.
 * 
 */
void InitStatistiques();

/**
 * @brief Fonction AfficheStatistiques.
 * 
 */
void AfficheStatistiques();

#endif /* STATISTIQUES_H_ */
