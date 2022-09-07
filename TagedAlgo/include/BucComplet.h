/*
 * BucComplet.h
 *
 *  Created on: 8 juin 2010
 *      Author: nedjar
 */

#include "Relation.h"
#include "AfficheurFiltrant.h"
#include "AlgoCube.h"

#ifndef BUCCOMPLET_H_
#define BUCCOMPLET_H_
namespace Taged
{
/**
 * @brief Classe BucComplet.
 * 
 * @tparam TAfficheur 
 */
template <typename TAfficheur>
class BucComplet : public AlgoCube<TAfficheur>
{
	using AlgoCube<TAfficheur>::aff;
	using AlgoCube<TAfficheur>::r;
	using AlgoCube<TAfficheur>::out;
public:
	/**
	 * @brief Construct a new Buc Complet object.
	 * 
	 * @param R 
	 * @param Out 
	 * @param MinSeuil1 
	 * @param MinSeuil2 
	 */
	BucComplet(Relation* R,std::ostream & Out,
			unsigned int MinSeuil1,unsigned int MinSeuil2):AlgoCube<TAfficheur>(R, Out, "")
	{
		AlgoCube<TAfficheur>::nomAlgo = "Buc + " + aff->getNomAfficheur();
		if(boost::is_base_of<AfficheurEmergent, TAfficheur>::value)
		{
			((AfficheurEmergent*)aff)->setMinSeuil1(MinSeuil1);
			((AfficheurEmergent*)aff)->setMinSeuil2(MinSeuil2);
		}
	}

	/**
	 * @brief Construct a new Buc Complet object.
	 * 
	 * @param R 
	 * @param Out 
	 */
	BucComplet(Relation* R,std::ostream & Out):AlgoCube<TAfficheur>(R, Out, "")
	{
		AlgoCube<TAfficheur>::nomAlgo = "Buc Complet + " + aff->getNomAfficheur();
	}

	/**
	 * @brief Destroy the Buc Complet object.
	 * 
	 */
	virtual ~BucComplet()
	{
	}

	/**
	 * @brief Algorithme Buc (complet, première passe).
	 * 
	 * @param indexDataIn 
	 * @param taille 
	 */
	virtual void Calculer(unsigned int indexDataIn[], unsigned int taille)
	{
		Calculer_rec(indexDataIn, taille, 0, 0);
	}
protected:
	/**
	 * @brief Algorithme Buc (complet, récursif).
	 * 
	 * @param indexDataIn 
	 * @param taille 
	 * @param dim 
	 * @param profondeur 
	 */
    void Calculer_rec(unsigned int indexDataIn[],
    		unsigned int taille,
    		unsigned int dim,
    		unsigned int profondeur);
};

typedef BucComplet<AfficheurFiltrant> BucFiltrant;

template <typename TAfficheur>
void BucComplet<TAfficheur>::Calculer_rec(unsigned int indexDataIn[],unsigned int taille,
		unsigned int dim,unsigned int profondeur)
{
	++NbBottomUpCubeCall;

	Aggregate(r, indexDataIn, taille); // Line 1
	if (1 == taille) // Line 2
	{
		++NbOptimizationCall;
		aff->AfficherAncetres(r->OutputRec, indexDataIn[0], dim);
		return;
	}
	aff->Afficher(r->OutputRec); // Line 3

	unsigned int dimCour; // Line 4
	for (dimCour = dim; dimCour < r->NbAttr; ++dimCour)
	{
		unsigned int *indexDataSorted = r->TabIndexData[profondeur];

		unsigned int cardDimCour = Partition(r, indexDataIn, taille, dimCour, indexDataSorted); // Line 5, 6

		unsigned int k = 0; // Line 7
		for (unsigned int i = 0; i < cardDimCour; ++i) // Line 8
		{
			unsigned int cardPartCour = r->DataCount[dimCour][i]; // Line 9
			r->OutputRec[dimCour] = r->RelationData[dimCour][indexDataSorted[k]]; // Line 11
			Calculer_rec(indexDataSorted + k, cardPartCour, dimCour + 1, profondeur + 1); // Line 12
			k += cardPartCour; // Line 14
		} // Line 15
		r->OutputRec[dimCour] = ALL; // Line 16
	} // Line 17
}
}
#endif /* BUCCOMPLET_H_ */
