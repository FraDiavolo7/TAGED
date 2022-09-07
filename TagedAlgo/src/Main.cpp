/*
 * main.cpp
 *
 *  Created on: 10 juin 2009
 *      Author: nedjar
 */
#include <iostream>
#include "Ppal.h"

using namespace std;

/**
 * @brief Point d'entrée du programme qui sert simplement de traitement d'exception et de mandataire à la fonction ppal() du fichier Ppal.cpp.
 * 
 * @param argc 
 * @param argv 
 * @return int 
 * 
 * @see Ppal.cpp
 * @see ppal()
 */
int main(int argc, char *argv[])
{
    try
    {
        ::ppal(argc, argv);
    }
    catch (exception &e)
    {
        cerr << "Exception: " << e.what();
    }
    cerr << endl <<argv[0]<< " is stopped" << endl << "**********" << endl << endl;
}

