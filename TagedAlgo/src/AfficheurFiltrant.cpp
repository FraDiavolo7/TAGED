/*
 * AfficheurFiltrant.cpp
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */
#include <iostream>
#include <sstream>
#include <fstream>

#include "AfficheurFiltrant.h"
#include "Statistiques.h"

using namespace std;
using namespace Taged;
// Classe servant uniquement à instancier la classe template AfficheurFiltrant
class Instanciate{
	AfficheurFiltrant f;
    Instanciate(AfficheurFiltrant f):f(f)
	{}
};

namespace Taged
{
bool estEmergent(string contenue, unsigned int minSeuil1, unsigned int minSeuil2)
{
	unsigned int indiceDebut = contenue.find_first_of(':');
	unsigned int indiceFinM1 = contenue.find_last_of(' ');

	if (contenue.empty() || indiceDebut == string::npos || indiceFinM1 == string::npos)
		return false;

	indiceDebut++;//pour sauter les deux points

	unsigned int ValM1 = atoi(contenue.substr(indiceDebut, indiceFinM1 - indiceDebut).c_str());
	unsigned int ValM2 = atoi(contenue.substr(indiceFinM1).c_str());

	return (ValM1 <= minSeuil1) && (ValM2 >= minSeuil2);
}

void Filtrage(const char* fileNameIn, const char* fileNameOut, int minSeuil1, int minSeuil2)
{
	ofstream ficOut(fileNameOut, ios::out | ios::trunc); //déclaration du flux et ouverture du fichier
	if (ficOut)
	{
		Filtrage(fileNameIn, ficOut, minSeuil1, minSeuil2);
		ficOut.close(); // on ferme le fichier FicIn
	}
	else
		cerr << "Impossible d'ouvrir le fichier " << fileNameOut << " !" << endl;
	return;
}

void Filtrage(const char* fileNameIn, ostream & ficOut, unsigned int minSeuil1, unsigned int minSeuil2)
{
	ifstream ficIn(fileNameIn, ios::in); // on ouvre le fichier en lecture

	if (ficIn)
	{
		string contenu; // déclaration d'une chaîne qui contiendra la ligne lue
		while (!ficIn.eof())
		{
			getline(ficIn, contenu); // on met dans "contenu" la ligne
			if (estEmergent(contenu, minSeuil1, minSeuil2))
			{
				NbLineInCubeEmergent++;
				ficOut << contenu << endl; // on affiche la ligne
			}

		}
		ficIn.close(); // on ferme le fichier FicIn
	}
	else
		cerr << "Impossible d'ouvrir le fichier " << fileNameIn << " !" << endl;
	return;

}
}
