/*
 * Afficheur.cpp
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */

#include "Afficheur.h"
namespace Taged {
void AfficherNuplet(const nuplet & outputRec, Relation* r, std::ostream & out)
{
    for (unsigned int a = 0; a < r->NbAttr; ++a)
    {
        if (outputRec[a] != ALL)
            out << outputRec[a] << ' ';
        else
            out << "ALL ";
    }
    out << std::endl;
}
}
