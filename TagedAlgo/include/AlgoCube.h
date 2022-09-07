/*
 * AlgoCube.h
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */

#ifndef ALGOCUBE_H_
#define ALGOCUBE_H_
#include<boost/type_traits.hpp>

#include "Relation.h"
#include "Afficheur.h"
#include "Statistiques.h"

namespace Taged
{
enum Phase{P0 = 0, P1 = 1, P2 = 2, P3 = 3};

// Nb de valeurs distinctes maximales des attributs (utilise pour CountingSort)
// Par defaut, valeur à 1000
extern unsigned int CardMaxAttr;
extern double ZipfFactor;
extern unsigned int *TabCountingSort;

/**
 * @brief Classe AlgoCube.
 * 
 * @tparam TAfficheur 
 */
template <typename TAfficheur>
class AlgoCube
{
	static_assert(boost::is_base_of<Afficheur, TAfficheur>::value, "Le parametre de genericite n'est pas une sous classe de Afficheur");

protected:
	Relation* r;
	std::ostream& out;
	TAfficheur* aff;
	std::string nomAlgo;

public:
	/**
	 * @brief Construct a new Algo Cube object.
	 * 
	 * @param R 
	 * @param Out 
	 * @param NomAlgo 
	 */
	AlgoCube(Relation* R, std::ostream & Out, const std::string& NomAlgo):r(R),out(Out),nomAlgo(NomAlgo)
	{
		aff = new TAfficheur(R, Out);
	}

	/**
	 * @brief Destroy the Algo Cube object.
	 * 
	 */
	~AlgoCube()
	{
		delete aff;
	}

	/**
	 * @brief Fonction Calculer.
	 * 
	 * @param indexDataIn 
	 * @param taille 
	 */
	void Calculer(unsigned int indexDataIn[], unsigned int taille);

	/**
	 * @brief Get the Nom Algo object.
	 * 
	 * @return std::string 
	 */
	std::string getNomAlgo() const
    {
        return nomAlgo;
    }
};

/**
 * @brief Fonction Dedup.
 * 
 * @param r 
 * @param int 
 * @param taille 
 * @param dimCour 
 * @param int 
 * @param int 
 */
void Dedup(Relation* r,
		unsigned int* indexDataIn,
		unsigned int  taille,
		unsigned int  dimCour,
		unsigned int* indexDataOut,
		unsigned int& tailleIndexDataOut);

/**
 * @brief Fonction CountingSort.
 * 
 * @param r 
 * @param indexDataIn 
 * @param taille 
 * @param dim 
 * @param indexDataSorted 
 * @return unsigned int 
 */
unsigned int CountingSort(Relation* r,
		unsigned int *indexDataIn,
        unsigned int taille,
        unsigned int dim,
        unsigned int *indexDataSorted);

// Calcule la valeur agrégée correspondant à l'ensemble de tuples de pIndexData de taille uSize
// Seule la fonction SOMME est faite ici
inline void Aggregate(Relation* r, unsigned int indexData[], unsigned int taille)
{
    r->ValAgg1 = 0;
    r->ValAgg2 = 0;
    for (unsigned int i = 0; i < taille; ++i)
    {
        r->ValAgg1 += r->Mesure1[indexData[i]];
        r->ValAgg2 += r->Mesure2[indexData[i]];
    }
}

// Calcul de partionnement pour une dimension
inline unsigned int Partition(Relation* r, unsigned int *indexData,
        unsigned int taille, unsigned int dim, unsigned int *indexDataSorted)
{
    ++NbSortCall; // Tiens a jour les stat de traitement
    NbElemUsedSort += taille;
    // On trie avec CountingSort et on renvoie le nombre de valeurs distinctes de la partition obtenue
    return CountingSort(r, indexData, taille, dim, indexDataSorted);
}
}
#endif /* ALGOCUBE_H_ */
