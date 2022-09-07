/*
 * AfficheurStd.h
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */

#ifndef AFFICHEURSTD_H_
#define AFFICHEURSTD_H_

#include "Afficheur.h"


namespace Taged
{
/**
 * @brief Classe AfficheurStd.
 * 
 */
class AfficheurStd: public Afficheur
{
public:
	/**
	 * @brief Construct a new Afficheur Std object.
	 * 
	 * @param R 
	 * @param Out 
	 */
	AfficheurStd(Relation* R, std::ostream & Out):Afficheur(R, Out, "Standard")
	{
	}

	/**
	 * @brief Destroy the Afficheur Std object.
	 * 
	 */
	~AfficheurStd()
	{
	}

	/**
	 * @brief Fonction AfficherAncetres.
	 * 
	 * @param outputRec 
	 * @param indexAncetre 
	 * @param dimCour 
	 */
	void AfficherAncetres(nuplet & outputRec, unsigned int indexAncetre, unsigned int dimCour) const
	{
        out << "OPT => ";
	    Afficher(r->OutputRec);
		AfficherAncetres_rec(outputRec, indexAncetre, dimCour);
        out <<std::endl;
	}
private:
	/**
	 * @brief Fonction AfficherAncetres_rec.
	 * 
	 * @param outputRec 
	 * @param indexAncetre 
	 * @param dimCour 
	 */
	void AfficherAncetres_rec(nuplet & outputRec, unsigned int indexAncetre, unsigned int dimCour) const;
};
}
#endif /* AFFICHEURSTD_H_ */
