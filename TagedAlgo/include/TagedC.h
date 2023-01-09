/*
 * TagedC.h
 *
 *  Created on: 10 juin 2009
 *      Author: nedjar
 */
#ifndef TAGEDC_H_
#define TAGEDC_H_
#include<boost/type_traits.hpp>

#include "Relation.h"
#include "AlgoCube.h"
#include "Afficheur.h"
#include "AfficheurEmergent.h"

namespace Taged
{

/**
 * @brief Template de l'algorithme Taged-C.
 * 
 * @tparam TAfficheur 
 */
template <typename TAfficheur>
/**
 * @brief Algorithme Taged-C.
 * 
 */
class TagedC : public AlgoCube<TAfficheur>
{
	unsigned int minSeuil1;
	unsigned int minSeuil2;
	using AlgoCube<TAfficheur>::aff;
	using AlgoCube<TAfficheur>::r;
	using AlgoCube<TAfficheur>::out;
public:
	/**
	 * @brief Construct a new TagedC object
	 * 
	 * @param R 
	 * @param Out 
	 * @param MinSeuil1 
	 * @param MinSeuil2 
	 */
	TagedC(Relation* R,std::ostream & Out,
			unsigned int MinSeuil1,unsigned int MinSeuil2):AlgoCube<TAfficheur>(R, Out, ""), minSeuil1(MinSeuil1), minSeuil2(MinSeuil2)
	{
		AlgoCube<TAfficheur>::nomAlgo = "TagedC + " + aff->getNomAfficheur();
		if(boost::is_base_of<AfficheurEmergent, TAfficheur>::value)
		{
			((AfficheurEmergent*)aff)->setMinSeuil1(MinSeuil1);
			((AfficheurEmergent*)aff)->setMinSeuil2(MinSeuil2);
		}
	}

	/**
	 * @brief Destroy the TagedC object
	 * 
	 */
	~TagedC()
	{
	}

	/**
	 * @brief Algorithme Taged-C (première passe).
	 * 
	 * @param indexDataIn 
	 * @param taille 
	 */
	void Calculer(unsigned int indexDataIn[], unsigned int taille);
protected:

	/**
	 * @brief Algorithme Taged-C (récursif).
	 * 
	 * @param indexDataIn 
	 * @param taille 
	 * @param dim 
	 * @param profondeur 
	 * @param pCour 
	 */
    void Calculer_rec(unsigned int indexDataIn[],
    		unsigned int taille,
    		unsigned int dim,
    		unsigned int profondeur,
    		Phase pCour);
};

template <typename TAfficheur>
void TagedC<TAfficheur>::Calculer(unsigned int indexDataIn[], unsigned int taille)
{
	++NbBottomUpCubeCall;
	unsigned int profondeur = 0;// Cette fonction est dédiée au premier appel de FBUC
	unsigned int dim = 0;
	Phase pSuiv = P1;

	// Calcule les valeurs de la fonction mesure et les écrit dans la cellule courante.
	Aggregate(r, indexDataIn, taille);

	// Déterminons la phase suivante.
	if (r->ValAgg2 < minSeuil2) 
		return;//C2 n'est pas satisfaite lors du premier appel => phase 3 (stop)

	if (r->ValAgg1 <= minSeuil1)// les tuples sont émergents => phase 2
		pSuiv = P2;

	unsigned int dimCour;
	for (dimCour = dim; dimCour < r->NbAttr; ++dimCour)
	{
		unsigned int *indexDataSorted = r->TabIndexData[profondeur];
		unsigned int cardDimCour = Partition(r, indexDataIn, taille, dimCour, indexDataSorted);
		bool estCandidatMax = true;
		if (cardDimCour == 1)//la partition courante ne contient q'une valeur donc pas de changement de phase PX => PX
		{
			estCandidatMax = false;
			unsigned int cardPartCour = r->DataCount[dimCour][0];
			r->OutputRec[dimCour] = r->RelationData[dimCour][indexDataSorted[0]];
			Calculer_rec(indexDataSorted, cardPartCour, dimCour + 1, profondeur + 1, pSuiv);
		}
		else
		{
			unsigned int k = 0;
			for (unsigned int i = 0; i < cardDimCour; ++i)
			{
				unsigned int cardPartCour = r->DataCount[dimCour][i];

				Aggregate(r, indexDataSorted + k, cardPartCour);
				if (r->ValAgg2 >= minSeuil2)
				{
					estCandidatMax = false;
					r->OutputRec[dimCour] = r->RelationData[dimCour][indexDataSorted[k]];
					Calculer_rec(indexDataSorted + k, cardPartCour, dimCour + 1, profondeur + 1, pSuiv);
				}// else  => phase 3 (stop)
				k += cardPartCour;
			}
		}
		if (pSuiv == P2 && estCandidatMax)
			aff->Afficher(r->OutputRec);
		r->OutputRec[dimCour] = ALL;
	}
	//On met à jour les variables statistiques pour le cube emergent
	NbLineInCubeEmergent = NbLineInCube;
	NbLineInCube = 0;
}

// Algo FBUC pour calculer la bordure U du cube emergent
template <typename TAfficheur>
void TagedC<TAfficheur>::Calculer_rec(unsigned int indexDataIn[],
		unsigned int taille,
		unsigned int dim,
		unsigned int profondeur,
		Phase pCour)
{
	++NbBottomUpCubeCall;

	// Déterminons la phase suivante.
	Phase pSuiv = pCour;
	if (r->ValAgg1 <= minSeuil1)//P1 => P2 ou P2 =>P2
	{
		pSuiv = P2;
		if (1 == taille)
		{
			++NbOptimizationCall;
			// On ne cherche que les tuples de U#, pas besion de parcourir les tuples émergents.
			aff->AfficherAncetres(r->OutputRec, indexDataIn[0], dim);//P2 => P3
			return;
		}
	}
	else if (1 == taille)//P1 =>P3(stop)
		// La relation courante ne contient qu'un seul tuple, on peut l'ajouter directement dans U# sans traiter tous ses prédécesseurs.
		return;

	unsigned int dimCour; // Line 4
	for (dimCour = dim; dimCour < r->NbAttr; ++dimCour)
	{
		unsigned int *indexDataSorted = r->TabIndexData[profondeur];
		// r est partitionnée suivant ses valeurs.
		unsigned int cardDimCour = Partition(r, indexDataIn, taille, dimCour, indexDataSorted); // Line 5, 6
		bool estCandidatMax = true;

		if (cardDimCour == 1)//la partition courante ne contient qu'une valeur donc pas de changement de phase PX => PX
		{
			estCandidatMax = false;
			unsigned int cardPartCour = taille;
			r->OutputRec[dimCour] = r->RelationData[dimCour][indexDataSorted[0]];

			if(dimCour >= r->NbAttr -1 )// La dimension courante est la dernière
			{
				if(pSuiv == P2)
					aff->Afficher(r->OutputRec);
				return;
			}

			Calculer_rec(indexDataSorted, cardPartCour, dimCour + 1, profondeur + 1, pSuiv);
		}
		else
		{
			unsigned int k = 0; // Line 7
			for (unsigned int i = 0; i < cardDimCour; ++i) // Line 8
			{
				unsigned int cardPartCour = r->DataCount[dimCour][i]; // Line 9
				Aggregate(r, indexDataSorted + k, cardPartCour);
				if (r->ValAgg2 >= minSeuil2) // Line 10
				{
					estCandidatMax = false;
					r->OutputRec[dimCour] = r->RelationData[dimCour][indexDataSorted[k]]; // Line 11
					Calculer_rec(indexDataSorted + k, cardPartCour, dimCour + 1, profondeur + 1, pSuiv); // Line 12
				} // Line 13
				//else PX => P3
				k += cardPartCour;
			}
		}
		if (pSuiv == P2 && estCandidatMax)
			aff->Afficher(r->OutputRec);
		r->OutputRec[dimCour] = ALL; // Line 16
	} // Line 17
}
}
#endif /* TagedC_H_ */
