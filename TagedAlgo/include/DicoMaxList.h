/*
 * DicoMaxList.h
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */

#ifndef DICOMAXLIST_H_
#define DICOMAXLIST_H_

#include <list>
#include <algorithm>
#include <iostream>

#include "Relation.h"
#include "Afficheur.h"//utile à des fin de test pour la fonction afficher...
#include "DicoMax.h"

namespace Taged
{
/**
 * @brief Classe DicoMaxList.
 * 
 */
class DicoMaxList:public DicoMax
{
	std::list<nuplet> elementsMax;
public:
	/**
	 * @brief Construct a new Dico Max List object.
	 * 
	 */
	DicoMaxList():DicoMax("Liste Chainée"), elementsMax()
	{
	}

	/**
	 * @brief Fonction Afficher.
	 * 
	 * @param R 
	 * @param Out 
	 */
	void Afficher(Relation* R, std::ostream & Out)
	{
		for(auto it = elementsMax.begin(); it != elementsMax.end();++it)
		{
			AfficherNuplet(*it, R, Out);
		}
	}

	/**
	 * @brief Fonction contient.
	 * 
	 * @param t 
	 * @return true 
	 * @return false 
	 */
	bool contient(const nuplet & t) const
	{
		auto fin = elementsMax.end();
		for(auto it = elementsMax.begin(); it != fin; ++it)
			if(estPlusGrandQue(*it, t))
				return true;
		return false;
	}

	/**
	 * @brief Ajoute le nuplet t sans vérifier si il y a des éléments à supprimer dans le dictionnaire. Attention cette methode ne vérifie pas l'invariant de la classe.
	 * 
	 * @param t 
	 */
	void ajouterDirectement(nuplet & t)
	{
		elementsMax.push_front(t);
	}

	/**
	 * @brief Ajoute le nuplet t après avoir vérifier les éléments à supprimer dans le dictionnaire.
	 * 
	 * @param t 
	 */
	void ajouterApresVerification(nuplet & t)
	{
		auto debut = elementsMax.begin();
	    auto fin = elementsMax.end();
	    while (debut != fin)
	    {
	        auto suivant = debut;
	        ++suivant;
	        if (estPlusGrandQue(t, *debut))
	        	elementsMax.erase(debut);
	        debut = suivant;
	    }
	    elementsMax.push_front(t);
	}
};
}
#endif /* DICOMAXLIST_H_ */
