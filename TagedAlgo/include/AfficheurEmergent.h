/*
 * AfficheurEmergent.h
 *
 *  Created on: 17 août 2010
 *      Author: nedjar
 */

#ifndef AFFICHEUREMERGENT_H_
#define AFFICHEUREMERGENT_H_

#include "Afficheur.h"

namespace Taged
{
/**
 * @brief Classe AfficheurEmergent.
 * 
 */
class AfficheurEmergent: public Afficheur
{
protected:
	unsigned int minSeuil1;
	unsigned int minSeuil2;

	/**
	 * @brief Construct a new Afficheur Emergent object.
	 * 
	 * @param R 
	 * @param Out 
	 * @param NomAfficheur 
	 * @param MinSeuil1 
	 * @param MinSeuil2 
	 */
	AfficheurEmergent(Relation* R, std::ostream & Out, const std::string & NomAfficheur,
			unsigned int MinSeuil1, unsigned int MinSeuil2):Afficheur(R, Out, NomAfficheur),
			minSeuil1(MinSeuil1), minSeuil2(MinSeuil2)
	{
	}

	/**
	 * @brief Construct a new Afficheur Emergent object.
	 * 
	 * @param R 
	 * @param Out 
	 * @param NomAfficheur 
	 */
	AfficheurEmergent(Relation* R, std::ostream & Out, const std::string & NomAfficheur):Afficheur(R, Out, NomAfficheur),
			minSeuil1(std::numeric_limits<unsigned int>::max()), minSeuil2(0)
	{
	}

	/**
	 * @brief Destroy the Afficheur Emergent object.
	 * 
	 */
	 ~AfficheurEmergent()
	{
	}

public:

	/**
	 * @brief Fonction estEmergent.
	 * 
	 * @return true 
	 * @return false 
	 */
	inline bool estEmergent()
	{
		return r->ValAgg1 <= minSeuil1 && r->ValAgg2 <= minSeuil2;
	}

	/**
	 * @brief Set the Min Seuil1 object.
	 * 
	 * @param minSeuil1 
	 */
	inline void setMinSeuil1(unsigned int minSeuil1)
	{
		this->minSeuil1 = minSeuil1;
	}

	/**
	 * @brief Set the Min Seuil2 object.
	 * 
	 * @param minSeuil2 
	 */
	inline void setMinSeuil2(unsigned int minSeuil2)
	{
		this->minSeuil2 = minSeuil2;
	}
};
}
#endif /* AFFICHEUREMERGENT_H_ */
