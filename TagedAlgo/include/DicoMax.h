/*
 * DicoMax.h
 *
 *  Created on: 19 août 2010
 *      Author: nedjar
 */

#ifndef DICOMAX_H_
#define DICOMAX_H_

#include "Relation.h"

namespace Taged
{
/**
 * @brief Classe DicoMax.
 * 
 */
class DicoMax
{
	std::string nomDico;
public:
	/**
	 * @brief Fonction Afficher.
	 * 
	 * @param R 
	 * @param Out 
	 */
	void Afficher(Relation* R, std::ostream & Out);

	/**
	 * @brief Fonction contient.
	 * 
	 * @param t 
	 * @return true 
	 * @return false 
	 */
	bool contient(const nuplet & t) const;

	/**
	 * @brief Fonction ajouterDirectement.
	 * 
	 * @param t 
	 */
	void ajouterDirectement(nuplet & t);

	/**
	 * @brief Fonction ajouterApresVerification.
	 * 
	 * @param t 
	 */
	void ajouterApresVerification(nuplet & t);

	/**
	 * @brief Get the Nom Dico object.
	 * 
	 * @return std::string 
	 */
    std::string getNomDico()
    {
    	return nomDico;
    }
protected:
	/**
	 * @brief Destroy the Dico Max object.
	 * 
	 */
	~DicoMax()
	{
	}

	/**
	 * @brief Construct a new Dico Max object.
	 * 
	 * @param nomDico 
	 */
	DicoMax(std::string nomDico):nomDico(nomDico)
	{
	}
};

/**
 * @brief Fonction estPlusGrandQue.
 * 
 * @param t1 
 * @param t2 
 * @return true 
 * @return false 
 */
inline bool estPlusGrandQue(const nuplet & t1, const nuplet & t2)
{
	for (unsigned int i = 0; i < t1.size(); i++)
        if(t2[i] != ALL && t1[i] != t2[i])
        	return false;
	return true;
}

}
#endif /* DICOMAX_H_ */
