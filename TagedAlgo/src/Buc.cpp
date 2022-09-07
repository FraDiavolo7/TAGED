//  - Prog principal de BUC

#include <iostream>
#include <fstream>

#include "Buc.h"
#include "Attribute.h"		// description d'un attribut

using namespace std;
using namespace Taged;
// Classe servant uniquement à instancier la classe template BucStd
class Instanciate{
	BucStd buc;
    Instanciate(BucStd buc):buc(buc)
	{}
};
