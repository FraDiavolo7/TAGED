/*
 * AfficheurFiltrant.h
 *
 *  Created on: 9 août 2010
 *      Author: nedjar
 */

#ifndef AFFICHEURFILTRANT_H_
#define AFFICHEURFILTRANT_H_

#include "Filtre.h"
#include "AfficheurStd.h"

namespace Taged
{
typedef Filtre<AfficheurStd> AfficheurFiltrant;

/**
 * @brief Fonction Filtrage.
 * 
 * @param fileNameIn 
 * @param fileNameOut 
 * @param minSeuil1 
 * @param minSeuil2 
 */
void Filtrage(const char* fileNameIn, const char* fileNameOut, int minSeuil1, int minSeuil2);

/**
 * @brief Fonction Filtrage.
 * 
 * @param fileNameIn 
 * @param ficOut 
 * @param minSeuil1 
 * @param minSeuil2 
 */
void Filtrage(const char* fileNameIn, std::ostream & ficOut, unsigned int minSeuil1, unsigned int minSeuil2);

/**
 * @brief Fonction estEmergent.
 * 
 * @param contenue 
 * @param minSeuil1 
 * @param minSeuil2 
 * @return true 
 * @return false 
 */
bool estEmergent(std::string contenue, unsigned int minSeuil1, unsigned int minSeuil2);
}
#endif /* AFFICHEURFILTRANT_H_ */
