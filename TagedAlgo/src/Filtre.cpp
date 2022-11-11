/*
 * Filtre.cpp
 *
 *  Created on: 13 août 2010
 *      Author: nedjar
 */

#include "Filtre.h"
#include "AfficheurStd.h"

#include <iostream>
#include <sstream>
#include <fstream>

using namespace std;

namespace Taged {
template<>
class Filtre<AfficheurStd>: public AfficheurEmergent
{
public:
	Filtre(Relation* R, std::ostream & Out, unsigned int MinSeuil1,
			unsigned int MinSeuil2): AfficheurEmergent(R, Out, "Standard + Filtrage", MinSeuil1, MinSeuil2)
	{
	}

	virtual ~Filtre()
	{
	}

	virtual void Afficher(nuplet & outputRec) const
	{
		++NbLineInCubeComplet;
		stringstream s;
		for (unsigned int a = 0; a < r->NbAttr; ++a)
		{
			if (outputRec[a] != ALL)
				s << outputRec[a] << ' ';
			else
				s << "ALL ";
		}
		s << ": " << r->ValAgg1 << " " << r->ValAgg2 << endl;
		if (r->ValAgg1 > minSeuil1 || r->ValAgg2 < minSeuil2)//Filtrage des tuples non émergents
				return;
		++NbLineInCubeEmergent;
                cerr << "CDE CDE CDE!!!!! 2 " << NbLineInCubeEmergent << endl;
        cout << s.str();
		out << s.str();
	}

	virtual void AfficherAncetres(nuplet & outputRec, unsigned int indexAncetre, unsigned int dimCour) const
	{
		Afficher(r->OutputRec);
		AfficherAncetres_rec(outputRec, indexAncetre, dimCour);
	}

protected:
	virtual void AfficherAncetres_rec(nuplet & outputRec, unsigned int indexAncetre, unsigned int dimCour) const
	{
		for (unsigned int i = dimCour; i < r->NbAttr; ++i)
		{
			outputRec[i] = r->RelationData[i][indexAncetre];
			Afficher(r->OutputRec);
		}
		for (unsigned int i = dimCour; i < r->NbAttr; ++i)
			outputRec[i] = ALL;

		if (dimCour + 1 < r->NbAttr)
			AfficherAncetres_rec(outputRec, indexAncetre, dimCour + 1);
	}

};
}
