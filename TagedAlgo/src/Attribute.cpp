#include "Attribute.h"

using namespace std;

namespace Taged {

void CAttribute::InitMaxNbAttribute( unsigned int uMaxNbAttribute )
    {
        if ( uMaxNbAttribute > MAX_ATTRIBUTE )
        {
            cerr << "CAttribute::InitNbAlloc uMaxNbAttribute > MAX_ATTRIBUTE ["
                 << uMaxNbAttribute << ']' << endl;
            exit( -2 );
        }
        cerr << "This version is limited to " << MAX_ATTRIBUTE << " attributes" << endl;
    }
}
