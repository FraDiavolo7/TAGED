/*
 * Filtre.h
 *
 *  Created on: 13 août 2010
 *      Author: nedjar
 */

#ifndef FILTRE_H_
#define FILTRE_H_

#include<boost/type_traits.hpp>
#include "AfficheurEmergent.h"
#include "Statistiques.h"

namespace Taged
{
/**
 * @brief Classe Filtre.
 * 
 * @tparam TAfficheur 
 */
template<typename TAfficheur>
class Filtre: public AfficheurEmergent
{
	static_assert(boost::is_base_of<Afficheur, TAfficheur>::value, "Le parametre de genericite n'est pas une sous classe de Afficheur");
	TAfficheur* aff;
public:
	/**
	 * @brief Construct a new Filtre object.
	 * 
	 * @param R 
	 * @param Out 
	 * @param MinSeuil1 
	 * @param MinSeuil2 
	 */
	Filtre(Relation* R, std::ostream & Out, unsigned int MinSeuil1,
			unsigned int MinSeuil2): AfficheurEmergent(R, Out, "", MinSeuil1, MinSeuil2), aff(NULL)
	{
		aff = new TAfficheur(R, Out);
		nomAfficheur = aff->getNomAfficheur() + " + Filtrage";
	}

	/**
	 * @brief Construct a new Filtre object.
	 * 
	 * @param R 
	 * @param Out 
	 */
	Filtre(Relation* R, std::ostream & Out): AfficheurEmergent(R, Out, ""), aff(NULL)
	{
		aff = new TAfficheur(R, Out);
		nomAfficheur = aff->getNomAfficheur() + " + Filtrage";
	}

	/**
	 * @brief Destroy the Filtre object.
	 * 
	 */
	~Filtre()
	{
		delete aff;
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
		if (r->ValAgg1 > minSeuil1 || r->ValAgg2 < minSeuil2)//Filtrage des tuples non émergents
		{
			NbFiltrage++;
			return;
		}

		aff->AfficherAncetres(outputRec, indexAncetre, dimCour);
	}

	/**
	 * @brief Fonction Afficher.
	 * 
	 * @param outputRec 
	 */
	void Afficher(nuplet & outputRec) const
	{
		if (r->ValAgg1 > minSeuil1 || r->ValAgg2 < minSeuil2)//Filtrage des tuples non émergents
		{
			NbFiltrage++;
			return;
		}
		aff->Afficher(outputRec);
	}
};
}
#endif /* FILTRE_H_ */
