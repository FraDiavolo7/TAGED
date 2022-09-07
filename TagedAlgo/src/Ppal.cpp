/*
 * ppal.cpp
 *
 *  Created on: 11 juin 2009
 *      Author: nedjar
 */
#include <cassert> // assert()
#include <iostream> //cout cerr
#include <fstream> //cout cerr
#include <cstdlib> //exit()
#include <limits>

#include "Relation.h"
#include "BucComplet.h"
#include "TagedE.h"
#include "TagedF.h"
#include "DicoMaxList.h"
#include "AfficheurMax.h"
#include "ThreadTime.h"     // Pour faire les calculs de temps
#include "Attribute.h"      // description d'un attribut
using namespace std;
using namespace Taged;

// Les fonctions de ce fichier
void ppal(int argc, char *argv[]);
void AfficherArg(int argc, char *argv[]);
void VerifierArg(int argc, char *argv[]);
Relation* TraiterArg(int argc, char *argv[]);

/**
 * @brief Fonction EvaluerAlgo.
 * 
 * @tparam TAlgo 
 * @param r 
 * @param algo 
 * @param ficName 
 */
template <typename TAlgo>
void EvaluerAlgo(Relation* r, TAlgo & algo, string const & ficName);

unsigned int MinSeuil2 = 0;
unsigned int MinSeuil1 = numeric_limits<unsigned int>::max();

// argv[0] = program name (BUC)
// argv[1] = FileName without extension (.rel for relation file and .mes for mesure file)
// argv[2] = NbAttr
// argv[3] = NbTuples
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
void ppal(int argc, char *argv[])
{
	std::ostream& defaultOut = std::cout;

	// On joue avec la ligne de commande
	AfficherArg(argc, argv);
	VerifierArg(argc, argv);
	Relation* r = TraiterArg(argc, argv);

	//BucComplet<Filtre<AfficheurStd> > fbuc1(r,defaultOut, MinSeuil1, MinSeuil2);
	//EvaluerAlgo(r, fbuc1, argv[1]);

	//TagedE<AfficheurStd> tagede1(r,defaultOut, MinSeuil1, MinSeuil2);
	//EvaluerAlgo(r, tagede1, argv[1]);

	//TagedF<AfficheurStd> tagedf1(r,defaultOut, MinSeuil1, MinSeuil2);
	//EvaluerAlgo(r, tagedf1, argv[1]);

	//BucComplet<Filtre<AfficheurMax<DicoMaxList> > > fbuc2(r,defaultOut, MinSeuil1, MinSeuil2);
	//EvaluerAlgo(r, fbuc2, argv[1]);

	//TagedE<AfficheurMax<DicoMaxList> > tagede2(r,defaultOut, MinSeuil1, MinSeuil2);
	//EvaluerAlgo(r, tagede2, argv[1]);

	//TagedF<AfficheurMax<DicoMaxList> > tagedf2(r,defaultOut, MinSeuil1, MinSeuil2);
	//EvaluerAlgo(r, tagedf2, argv[1]);

	// Utilisation de l'algorithme Taged-F
	TagedF<AfficheurMaxRapide<DicoMaxList> > tagedf3(r,defaultOut, MinSeuil1, MinSeuil2);
	EvaluerAlgo(r, tagedf3, argv[1]);

	// Libération ressources
	delete r;
}

/**
 * @brief Fonction EvaluerAlgo.
 * 
 * @tparam TAlgo 
 * @param r 
 * @param algo 
 * @param ficName 
 */
template <typename TAlgo>
void EvaluerAlgo(Relation* r, TAlgo & algo, const string & ficName)
{
	// Pour réaliser le premier traitement sur les données (trier sur la première dimension)
	//  il faut de la mémoire disponible et initialisée
	unsigned int *IndexData = new unsigned int[r->NbTuple];
	for (unsigned int i = 0; i < r->NbTuple; ++i)
		IndexData[i] = i;

	// Initialisation des variables de Relations en fonction des données en entrée
	CardMaxAttr = r->InitDataCount(IndexData) + 1;

	// Allocation mémoire de certaines données globales utilisées pour le CountingSort
	assert(CardMaxAttr != 0);
	TabCountingSort = new unsigned int[CardMaxAttr];

	// Remise à zéro des statistiques et de l'index de départ
	InitStatistiques();
	for (unsigned int i = 0; i < r->NbTuple; ++i)
		IndexData[i] = i;

	cerr << endl;
	cerr << "==== "<< algo.getNomAlgo() <<" ====" << endl;

	string FicNameCubeEmergent = ficName + ".cube.emergent";
	ofstream ficCubeEmergent(FicNameCubeEmergent.c_str(), ios::out | ios::trunc); //déclaration du flux et ouverture du fichier
	if (!ficCubeEmergent)
	{
		cerr << "Impossible d'ouvrir le fichier " << FicNameCubeEmergent << " !" << endl;
		exit(-1);
	}

	// Init du compteur de temps
	ThreadTimeInit();

	// Appel de l'agorithme.
	algo.Calculer( IndexData, r->NbTuple);

	// Combien de temps ?
	TempsCalculCubeEmergent = ThreadTimeElapsedTime();
	ficCubeEmergent.close();
	//remove(FicNameCubeEmergent.c_str());

	cerr << endl;
	cerr << "==== Statistiques "<< algo.getNomAlgo() <<" ====" << endl;

	cerr << "NomFic                  = " << ficName << endl;
	cerr << "NbTuple                 = " << r->NbTuple << endl;
	cerr << "NbAttr                  = " << r->NbAttr << endl;
	cerr << "CardMaxAttr             = " << CardMaxAttr << endl;
	cerr << "ZipfFactor              = " << ZipfFactor << endl;
	cerr << "MinSup                  = " << MinSeuil1 << endl;
	cerr << "MinSup2                 = " << MinSeuil2 << endl;
	AfficheStatistiques();

	delete[] TabCountingSort;
	delete[] IndexData;
}

/**
 * @brief Fonction AfficherArg.
 * 
 * @param argc 
 * @param argv 
 */
void AfficherArg(int argc, char *argv[])
{
	string ArgName[] =
	{ "BinFile", "FileName.rel", "NbAttr", "NbTuples", "[MinSup2]", "[MinSup1]", "[ZipfFactor]" ,"[CardMaxAttr]" };

	cerr << "argc : " << argc << endl;
	for (unsigned int i = 0; i < unsigned(argc); ++i)
	{
		if (i < sizeof ArgName / sizeof ArgName[0])
			cerr << ArgName[i];
		else
			cerr << "?????";

		cerr << " => argv[" << i << "] = " << argv[i] << endl;
	}
}

/**
 * @brief Fonction VerifierArg.
 * 
 * @param argc 
 * @param argv 
 */
void VerifierArg(int argc, char *argv[])
{
	if (argc < 4 ||  argc > 8)
	{
		cerr << "Usage: " << argv[0] << " FileName.rel NbAttr NbTuples [-mMinSup1] [-nMinSup2] [-sZipfFactor] [-dCardMaxAttr]" << endl;
		exit(-1);
	}

	for (int i = 4; i < argc; ++i)
		if (argv[i][0] != '-' || (argv[i][1] != 'n' && argv[i][1] != 'm' && argv[i][1] != 'd' && argv[i][1] != 's'))
		{
			cerr << "Usage: " << argv[0] << " FileName.rel NbAttr NbTuples [-mMinSup1] [-nMinSup2] [-sZipfFactor] [-dCardMaxAttr]"
					<< endl;
			exit(-1);
		}

	cerr << "**********" << endl << argv[0] << " is started..." << endl;
}

/**
 * @brief Fonction TraiterArg.
 * 
 * @param argc 
 * @param argv 
 * @return Relation* 
 */
Relation* TraiterArg(int argc, char *argv[])
{
	// Init de la relation
	Relation* r = new Relation(argv[1], atoi(argv[2]), atoi(argv[3]));

	for (int i = 4; i < argc; ++i)
		if (argv[i][1] == 'm')
			MinSeuil1 = atoi(argv[i] + 2);
		else if (argv[i][1] == 'n')
			MinSeuil2 = atoi(argv[i] + 2);
		else if (argv[i][1] == 'd')
			CardMaxAttr = atoi(argv[i] + 2);
		else if (argv[i][1] == 's')
			ZipfFactor = atof(argv[i] + 2);

	// Initialisation de la taille des attributs
	CAttribute::InitMaxNbAttribute(atoi(argv[2]));

	// Chargement de la relation
	r->LoadRelation();
	//r->AfficherRelation();

	// Chargement des valeurs Mesure.
	// Si les valeurs mesures n'existe pas, on les génére automatiquement
	r->LoadMesure();
	//r->AfficherMesure();
	return r;
}