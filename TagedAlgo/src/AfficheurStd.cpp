/*
 * AfficheurStd.cpp
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */

#include "AfficheurStd.h"
namespace Taged {

void AfficheurStd::AfficherAncetres_rec(nuplet & outputRec, unsigned int indexAncetre, unsigned int dimCour) const
{
    for(unsigned int i = dimCour; i < r->NbAttr;++i )
    {
        outputRec[i] = r->RelationData[i][indexAncetre];
        Afficher(r->OutputRec);
    }

    for(unsigned int i = dimCour; i < r->NbAttr;++i )
        outputRec[i] = ALL;

    if(dimCour+1 < r->NbAttr)
    	AfficherAncetres_rec(outputRec, indexAncetre, dimCour+1);
}

}
