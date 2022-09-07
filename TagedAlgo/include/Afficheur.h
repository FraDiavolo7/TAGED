/*
 * Afficheur.h
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */

#ifndef AFFICHEUR_H_
#define AFFICHEUR_H_
#include "Relation.h"
#include "Statistiques.h"

namespace Taged
{

/**
 * @brief Fonction AfficherNuplet.
 * 
 * @param outputRec 
 * @param r 
 * @param out 
 */
void AfficherNuplet(const nuplet & outputRec, Relation* r, std::ostream & out);

/**
 * @brief Classe Afficheur.
 * 
 */
class Afficheur
{
protected:
	Relation* r;
	std::ostream & out;
	std::string nomAfficheur;

	/**
	 * @brief Construct a new Afficheur object.
	 * 
	 * @param R 
	 * @param Out 
	 * @param NomAfficheur 
	 */
	Afficheur(Relation* R, std::ostream & Out, const std::string & NomAfficheur):r(R),out(Out),nomAfficheur(NomAfficheur)
	{
	}

	/**
	 * @brief Destroy the Afficheur object.
	 * 
	 */
	~Afficheur()
	{
	}

public:

	/**
	 * @brief Fonction AfficherAncetres.
	 * 
	 * @param outputRec 
	 * @param indexAncetre 
	 * @param dimCour 
	 */
	void AfficherAncetres(nuplet & outputRec, unsigned int indexAncetre, unsigned int dimCour) const;

	/**
	 * @brief Fonction Afficher.
	 * 
	 * @param outputRec 
	 */
	void Afficher(nuplet & outputRec) const
	{
	    ++NbLineInCube;

	    for (unsigned int a = 0; a < r->NbAttr; ++a)
	    {
	        if (outputRec[a] != ALL)
	            out << outputRec[a] << ' ';
	        else
	            out << "ALL ";
	    }
	    out << ": " << r->ValAgg1 << " " << r->ValAgg2 << std::endl;
	}

	std::string getNomAfficheur() const
    {
        return nomAfficheur;
    }
};
}
#endif /* AFFICHEUR_H_ */
