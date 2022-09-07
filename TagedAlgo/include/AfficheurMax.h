/*
 * AfficheurMax.h
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */

#ifndef AFFICHEURMAX_H_
#define AFFICHEURMAX_H_

#include<boost/type_traits.hpp>

#include "Afficheur.h"
#include "DicoMax.h"

namespace Taged
{

/**
 * @brief Classe AfficheurMax.
 * 
 * @tparam TDicoMax 
 */
template <typename TDicoMax>
class AfficheurMax: public Afficheur
{
	static_assert(boost::is_base_of<DicoMax, TDicoMax>::value, "Le parametre de genericite n'est pas une sous classe de DicoMax");
	TDicoMax maximaux;

public:
	/**
	 * @brief Construct a new Afficheur Max object.
	 * 
	 * @param R 
	 * @param Out 
	 */
	AfficheurMax(Relation* R, std::ostream & Out):Afficheur(R,Out,""),maximaux()
	{
		nomAfficheur = "Maximaux (" + maximaux.getNomDico() + ")";
	}

	/**
	 * @brief Destroy the Afficheur Max object.
	 * 
	 */
	~AfficheurMax()
	{
		maximaux.Afficher(r,out);
	}

	/**
	 * @brief Fonction AfficherAncetres.
	 * 
	 * @param outputRec 
	 * @param indexAncetre 
	 * @param dimCour 
	 */
	void AfficherAncetres(nuplet & outputRec, unsigned int indexAncetre, unsigned int dimCour)
	{
	    for(unsigned int i = dimCour; i < r->NbAttr;++i )
	        outputRec[i] = r->RelationData[i][indexAncetre];

	    Afficher(r->OutputRec);

	    for(unsigned int i = dimCour; i < r->NbAttr;++i )
	        outputRec[i] = ALL;
	}

	/**
	 * @brief Fonction Afficher.
	 * 
	 * @param outputRec 
	 */
	void Afficher(nuplet & outputRec)
	{
		if(!maximaux.contient(outputRec))
		{
			maximaux.ajouterApresVerification(outputRec);
			Afficheur::Afficher(outputRec);
		}
	}

	/**
	 * @brief Fonction AfficherMaximaux (à venir).
	 * 
	 */
	void AfficherMaximaux()
	{

	}
};
template <typename TDicoMax>
class AfficheurMaxRapide: public Afficheur
{
	static_assert(boost::is_base_of<DicoMax, TDicoMax>::value, "Le parametre de genericite n'est pas une sous classe de DicoMax");
	TDicoMax maximaux;
public:

	/**
	 * @brief Construct a new Afficheur Max Rapide object.
	 * 
	 * @param R 
	 * @param Out 
	 */
	AfficheurMaxRapide(Relation* R, std::ostream & Out):Afficheur(R,Out,""),maximaux()
	{
		nomAfficheur = "Maximaux Rapide (" + maximaux.getNomDico() + ")";
	}

	/**
	 * @brief Destroy the Afficheur Max Rapide object.
	 * 
	 */
	~AfficheurMaxRapide()
	{
	}

	/**
	 * @brief Fonction AfficherAncetres.
	 * 
	 * @param outputRec 
	 * @param indexAncetre 
	 * @param dimCour 
	 */
	void AfficherAncetres(nuplet & outputRec, unsigned int indexAncetre, unsigned int dimCour)
	{
	    for(unsigned int i = dimCour; i < r->NbAttr;++i )
	        outputRec[i] = r->RelationData[i][indexAncetre];

	    Afficher(r->OutputRec);

	    for(unsigned int i = dimCour; i < r->NbAttr;++i )
	        outputRec[i] = ALL;
	}

	/**
	 * @brief Fonction Afficher.
	 * 
	 * @param outputRec 
	 */
	void Afficher(nuplet & outputRec)
	{
		if(!maximaux.contient(outputRec))
		{
			maximaux.ajouterDirectement(outputRec);
			Afficheur::Afficher(outputRec);
		}
	}
};

}
#endif /* AFFICHEURMAX_H_ */
