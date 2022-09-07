/*
 * AlgoCube.cpp
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */
#include <cassert> //assert()
#include "AlgoCube.h"

namespace Taged
{
// Nb de valeurs distinctes maximales des attributs (utilise pour CountingSort)
// Par defaut, valeur à 1000
unsigned int CardMaxAttr = 1000 + 1;
double ZipfFactor = 0;
unsigned int *TabCountingSort = NULL;



// The CountingSort
unsigned int CountingSort(Relation* r, unsigned int *indexDataIn,
		unsigned int taille, unsigned int dim, unsigned int *indexDataSorted)
{
	// On récupére les tuples pour la dimension en cours de traitement
	unsigned int* dimData = r->RelationData[dim];
	// Mise à 0 de l'histogramme
	memset(TabCountingSort, 0, sizeof(unsigned int) * CardMaxAttr);
	// Mise à 0 du nombre d'elem de la dimension en cours de traitement
	memset(r->DataCount[dim], 0, r->Cardinalite[dim]);
	// On remplie l'histogramme
	for (unsigned int i = 0; i < taille; ++i)
		++TabCountingSort[dimData[indexDataIn[i]]];

	// On exploite l'histogramme
	unsigned int card = 0;
	unsigned int premier = 0;
	unsigned int dernier = 0;
	for (unsigned int i = 0; i < CardMaxAttr; ++i)
	{
		// Si l'histogramme[i] a été rempli, on stocke cette cardinalité
		if (TabCountingSort[i] != 0)
			r->DataCount[dim][card++] = TabCountingSort[i];

		// On met à jour les index afin de recomposer le résultat en une seule passe
		dernier = TabCountingSort[i];
		TabCountingSort[i] = premier;
		premier += dernier;
	}
	// Petite vérif de valeur (DEBUG only)
	assert( card <= r->Cardinalite[dim] );
	// On recopie le résultat
	for (unsigned int i = 0; i < taille; ++i)
	{
		unsigned int v = dimData[indexDataIn[i]];
		indexDataSorted[TabCountingSort[v]] = indexDataIn[i];
		++TabCountingSort[v];
	}
	// On rend le nombre de valeur distincte de la dimension
	return card;
}

void Dedup(Relation* r, unsigned int* indexDataIn,
		unsigned int taille, unsigned int dimCour, unsigned int* indexDataOut,
		unsigned int& tailleIndexDataOut)
{
	unsigned int *indexDataSorted = new unsigned int[taille];
	unsigned int cardDimCour = Partition(r, indexDataIn, taille, dimCour,
			indexDataSorted);

	unsigned int k = 0;
	for (unsigned int i = 0; i < cardDimCour; ++i)
	{
		unsigned int cardPartCour = r->DataCount[dimCour][i];

		if (cardPartCour > 1 && dimCour < r->NbAttr - 1)
			Dedup(r, indexDataSorted + k, cardPartCour, dimCour + 1,
					indexDataOut, tailleIndexDataOut);
		else if (cardPartCour > 1 && dimCour == r->NbAttr - 1)
		{
			NbDoublons += cardPartCour - 1;
			std::cerr << "Liste des doublons de " << indexDataSorted[k] << " :";
			for (unsigned int i = 1; i < cardPartCour; ++i)
			{
				r->Mesure1[indexDataSorted[k]] += r->Mesure1[indexDataSorted[k+ i]];
				r->Mesure2[indexDataSorted[k]] += r->Mesure2[indexDataSorted[k+ i]];
				std::cerr << indexDataSorted[k + i] << " ";
			}
			std::cerr << std::endl;
			indexDataOut[tailleIndexDataOut++] = indexDataSorted[k];
		}
		else
		{
			indexDataOut[tailleIndexDataOut++] = indexDataSorted[k];
		}
		k += cardPartCour;
	}

	delete[] indexDataSorted;
}
}
