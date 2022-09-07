/*
 * buc.h
 *
 *  Created on: 10 juin 2009
 *      Author: nedjar
 */
#include <limits>

#include "Relation.h"
#include "AlgoCube.h"
#include "AfficheurStd.h"

#ifndef BUC_H_
#define BUC_H_
namespace Taged
{
/**
 * @brief Classe Buc.
 * 
 * @tparam TAfficheur 
 */
template <typename TAfficheur>
class Buc : public AlgoCube<TAfficheur>
{
	unsigned int minSeuil;
	using AlgoCube<TAfficheur>::aff;
	using AlgoCube<TAfficheur>::r;
	using AlgoCube<TAfficheur>::out;
public:

	/**
	 * @brief Construct a new Buc object.
	 * 
	 * @param R 
	 * @param Out 
	 * @param minSeuil 
	 */
	Buc(Relation* R, std::ostream & Out, unsigned int minSeuil = std::numeric_limits<unsigned int>::min())
	:AlgoCube<TAfficheur>(R, Out,""), minSeuil(minSeuil)
	{
		AlgoCube<TAfficheur>::nomAlgo = "Buc " + aff->getNomAfficheur();
	}

	/**
	 * @brief Destroy the Buc object.
	 * 
	 */
	~Buc(){};

	/**
	 * @brief Algorithme Buc (première passe).
	 * 
	 * @param indexDataIn 
	 * @param taille 
	 */
	void Calculer(unsigned int indexDataIn[], unsigned int taille) {Calculer_rec(indexDataIn, taille, 0, 0);}
protected:

	/**
	 * @brief Algorithme Buc (récursif).
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

typedef Buc<AfficheurStd> BucStd;

// BUC cf article pour les explications de l'algo
// (line en ref dans le src correspond à celle de l'article)
template <typename TAfficheur>
void Buc<TAfficheur>::Calculer_rec(unsigned int indexDataIn[],
		unsigned int taille,
		unsigned int dim,
		unsigned int profondeur)
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

		unsigned int cardDimCour = Partition(r, indexDataIn, taille, dimCour,
				indexDataSorted); // Line 5, 6

		unsigned int k = 0; // Line 7
		for (unsigned int i = 0; i < cardDimCour; ++i) // Line 8
		{
			unsigned int cardPartCour = r->DataCount[dimCour][i]; // Line 9
			if (cardPartCour >= minSeuil) // Line 10
			{
				r->OutputRec[dimCour]
						= r->RelationData[dimCour][indexDataSorted[k]]; // Line 11
				Calculer_rec(indexDataSorted + k, cardPartCour, dimCour + 1, profondeur+1); // Line 12
			} // Line 13
			//else cout<<"stop!!"<<endl;
			k += cardPartCour; // Line 14
		} // Line 15
		r->OutputRec[dimCour] = ALL; // Line 16
	} // Line 17
}
}
#endif /* BUC_H_ */
