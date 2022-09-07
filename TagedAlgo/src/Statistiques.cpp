/*
 * Statistiques.cpp
 *
 *  Created on: 29 juin 2010
 *      Author: nedjar
 */
#include<iostream>

#include "Statistiques.h"

unsigned long long int NbBottomUpCubeCall = 0;
unsigned long long int NbSortCall = 0;
unsigned long long int NbElemUsedSort = 0;
unsigned long long int NbOptimizationCall = 0;
unsigned long long int NbLineInCube = 0;

unsigned long long int NbDoublons = 0;
unsigned long long int TempsCalculCube = 0;
unsigned long long int TempsCalculCubeEmergent = 0;

unsigned long long int NbBottomUpCubeFilteredCall = 0;
unsigned long long int NbLineInCubeEmergent = 0;
unsigned long long int NbLineInCubeComplet = 0;
unsigned long long int NbFiltrage = 0;
unsigned long long int TempsFiltrage = 0;

using namespace std;

void InitStatistiques()
{
	NbBottomUpCubeCall = 0;
	NbSortCall = 0;
	NbElemUsedSort = 0;
	NbOptimizationCall = 0;
	NbLineInCube = 0;

	NbDoublons = 0;
	TempsCalculCube = 0;
	TempsCalculCubeEmergent = 0;

	NbBottomUpCubeFilteredCall = 0;
	NbLineInCubeEmergent = 0;
	NbLineInCubeComplet = 0;
	NbFiltrage = 0;
	TempsFiltrage = 0;
}

void AfficheStatistiques()
{
	cerr << endl;
	cerr << "NbBottomUpCubeCall      = " << NbBottomUpCubeCall << endl;
	cerr << "NbSortCall              = " << NbSortCall << endl;
	cerr << "NbElemUsedSort          = " << NbElemUsedSort << endl;
	cerr << "NbOptimizationCall      = " << NbOptimizationCall << endl;
	cerr << "NbDoublons              = " << NbDoublons << endl;
	cerr << endl;

	cerr << "NbLineInCube            = " << NbLineInCube << endl;
	cerr << "NbLineInCubeComplet     = " << NbLineInCubeComplet << endl;
	cerr << "TempsCalculCube         = " << TempsCalculCube << endl;

	cerr << "NbLineInCubeEmergent    = " << NbLineInCubeEmergent << endl;
	cerr << "NbFiltrage              = " << NbFiltrage << endl;
	cerr << "TempsFiltrage           = " << TempsFiltrage << endl;
	cerr << "TempsCalculCubeEmergent = " << TempsCalculCubeEmergent;
	cerr << endl;
}
