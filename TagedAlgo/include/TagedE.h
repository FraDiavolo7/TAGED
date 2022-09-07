/*
 * TagedE.h
 *
 *  Created on: 11 août 2010
 *      Author: nedjar
 */

#ifndef TAGEDE_H_
#define TAGEDE_H_

#include "AlgoCube.h"
#include "AfficheurStd.h"
namespace Taged
{
/**
 * @brief Template de l'algorithme Taged-E.
 * 
 * @tparam TAfficheur 
 */
template <typename TAfficheur>
/**
 * @brief Algorithme Taged-E.
 * 
 */
class TagedE: public AlgoCube<TAfficheur>
{
	unsigned int minSeuil1;
	unsigned int minSeuil2;

	using AlgoCube<TAfficheur>::aff;
	using AlgoCube<TAfficheur>::r;
	using AlgoCube<TAfficheur>::out;
	using AlgoCube<TAfficheur>::nomAlgo;
public:
	/**
	 * @brief Construct a new TagedE object.
	 * 
	 * @param R 
	 * @param Out 
	 * @param MinSeuil1 
	 * @param MinSeuil2 
	 */
	TagedE(Relation* R,std::ostream & Out, unsigned int MinSeuil1,unsigned int MinSeuil2)
	:AlgoCube<TAfficheur>(R, Out, ""),minSeuil1(MinSeuil1), minSeuil2(MinSeuil2)
	{
		nomAlgo = "TagedE + " + aff->getNomAfficheur();
		//aff->setMinSeuil1(MinSeuil1);
		//aff->setMinSeuil2(MinSeuil2);
	}

	/**
	 * @brief Destroy the TagedE object.
	 * 
	 */
	virtual ~TagedE()
	{
	}

	/**
	 * @brief Algorithme Taged-E (première passe).
	 * 
	 * @param indexDataIn 
	 * @param taille 
	 */
	void Calculer(unsigned int indexDataIn[], unsigned int taille);
protected:
	/**
	 * @brief Algorithme Taged-E (récursif).
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

typedef TagedE<AfficheurStd> TagedEStd;

template <typename TAfficheur>
void TagedE<TAfficheur>::Calculer(unsigned int indexDataIn[], unsigned int taille)
{
	unsigned int dim = 0;
	unsigned int profondeur = 0;
	++NbBottomUpCubeCall;

	Aggregate(r, indexDataIn, taille);

	if(r->ValAgg2 < minSeuil2)
		return;//C2 n'est pas satisfaite lors du premier appel, pas la peine de continuer

	if (r->ValAgg1 <= minSeuil1)
		aff->Afficher(r->OutputRec);

	unsigned int dimCour; // Line 4
	for (dimCour = dim; dimCour < r->NbAttr; ++dimCour)
	{
		unsigned int *indexDataSorted = r->TabIndexData[profondeur];

		unsigned int cardDimCour = Partition(r, indexDataIn, taille, dimCour, indexDataSorted); // Line 5, 6

		unsigned int k = 0; // Line 7
		for (unsigned int i = 0; i < cardDimCour; ++i) // Line 8
		{
			unsigned int cardPartCour = r->DataCount[dimCour][i]; // Line 9
			Aggregate(r, indexDataSorted + k, cardPartCour);
			if (r->ValAgg2 >= minSeuil2) // Line 10
			{
				r->OutputRec[dimCour] = r->RelationData[dimCour][indexDataSorted[k]]; // Line 11
				Calculer_rec(indexDataSorted + k, cardPartCour, dimCour + 1,profondeur+1); // Line 12
			} // Line 13
			//else cout<<"stop!!"<<endl;
			k += cardPartCour; // Line 14
		} // Line 15
		r->OutputRec[dimCour] = ALL; // Line 16
	} // Line 17
	//On met à jour les variables statistiques pour le cube emergent
	NbLineInCubeEmergent = NbLineInCube;
	NbLineInCube = 0;
}

template <typename TAfficheur>
void TagedE<TAfficheur>::Calculer_rec(unsigned int indexDataIn[], unsigned int taille, unsigned int dim, unsigned int profondeur)
{
	++NbBottomUpCubeCall;
	if (r->ValAgg1 <= minSeuil1)
	{
		if (1 == taille) // Line 2
		{
			++NbOptimizationCall;
			aff->AfficherAncetres( r->OutputRec, indexDataIn[0], dim);
			return;
		}
		aff->Afficher(r->OutputRec); // Line 3
	}
	else if(1 == taille)
		return;

	unsigned int dimCour; // Line 4
	for (dimCour = dim; dimCour < r->NbAttr; ++dimCour)
	{
		unsigned int *indexDataSorted = r->TabIndexData[profondeur];
		unsigned int cardDimCour = Partition(r, indexDataIn, taille, dimCour, indexDataSorted); // Line 5, 6

		unsigned int k = 0; // Line 7
		for (unsigned int i = 0; i < cardDimCour; ++i) // Line 8
		{
			unsigned int cardPartCour = r->DataCount[dimCour][i]; // Line 9
			Aggregate(r, indexDataSorted + k, cardPartCour);
			if (r->ValAgg2 >= minSeuil2) // Line 10
			{
				r->OutputRec[dimCour] = r->RelationData[dimCour][indexDataSorted[k]]; // Line 11
				Calculer_rec(indexDataSorted + k, cardPartCour, dimCour + 1, profondeur+1); // Line 12
			} // Line 13
			//else cout<<"stop!!"<<endl;
			k += cardPartCour; // Line 14
		} // Line 15
		r->OutputRec[dimCour] = ALL; // Line 16
	} // Line 17
}
}
#endif /* TAGEDE_H_ */
