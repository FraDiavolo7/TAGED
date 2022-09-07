/*
 * ppal.h
 *
 *  Created on: 11 juin 2009
 *      Author: nedjar
 */

#ifndef PPAL_H_
#define PPAL_H_

/**
 * @brief Fonction principale du programme.
 * 
 * argv[0] = program name (BUC)
 * argv[1] = FileName without extension (.rel for relation file and .mes for mesure file)
 * argv[2] = NbAttr
 * argv[3] = NbTuples
 * 
 * @param argc 
 * @param argv 
 */
void ppal(int argc, char *argv[]);
#endif /* PPAL_H_ */
