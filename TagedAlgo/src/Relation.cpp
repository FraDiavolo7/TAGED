// Relations.cpp: namespace Relations.
//
//////////////////////////////////////////////////////////////////////

#include <stdio.h>
#include <cassert>
#include <limits> //numeric_limit

#include "Relation.h"
#include "CExc.h"

using namespace std;

// Valeur minimale du support (1 par defaut)
//unsigned int MinSeuil1 = numeric_limits<unsigned int>::min();
//unsigned int MinSeuil2 = numeric_limits<unsigned int>::min();

namespace Taged
{

template<class T>
ostream &operator<<(ostream &os, const vector<T> &v)
{
    typename vector<T>::const_iterator it = v.begin();
    for (; it != v.end() - 1; ++it)
        os << *it << ", ";
    os << *it;

    return os;
}

// Initialisation des membres du namespace
Relation::Relation(const char* fileName, unsigned int nbAttr, unsigned int nbTuple) :
    NbTuple(nbTuple),
    NbAttr(nbAttr),
    Mesure1(new unsigned int[NbTuple]),
    Mesure2(new unsigned int[NbTuple]),
    RelationData(new unsigned int*[NbAttr]),
    TabIndexData(new unsigned int*[NbAttr]),
    DataCount(new unsigned int *[NbAttr]),
    //OutputRec(nbAttr),
    ValAgg1(0),
    ValAgg2(0),
    Cardinalite(new unsigned int[NbAttr]),
    NameOfAttribute(),
    FileName(fileName),
    AttrRelation(new CAttribute()),
    TmpHisto(new unsigned int[NbTuple])
{
	fill(OutputRec.begin(),OutputRec.end(),ALL);
    // Generation des noms d'attributs de la relation
    for (unsigned int r = 0; r < NbAttr; r++)
        AttrRelation->Insert(r);


    for (unsigned int i = 0; i < NbAttr; ++i)
    {
        RelationData[i] = new unsigned int[NbTuple];
        memset(RelationData[i], 0, sizeof(unsigned int) * NbTuple);

        TabIndexData[i] = new unsigned int[NbTuple];
        memset(TabIndexData[i], 0, sizeof(unsigned int) * NbTuple);
    }

    // Nombre d'elem de chaque classe de chaque attribut
    memset(DataCount, 0, sizeof(unsigned int *) * NbAttr);
}

Relation::~Relation()
{
    delete AttrRelation;
    AttrRelation = NULL;

    delete[] Mesure1;
    Mesure1 = NULL;

    delete[] Mesure2;
    Mesure2 = NULL;

    unsigned int i;
    for (i = 0; i < NbAttr; ++i)
    {
        delete[] RelationData[i];
        RelationData[i] = NULL;

        delete[] TabIndexData[i];
        TabIndexData[i] = NULL;

        delete[] DataCount[i];
        DataCount[i] = NULL;
    }

    delete[] RelationData;
    RelationData = NULL;

    delete[] TabIndexData;
    TabIndexData = NULL;

    delete[] DataCount;
    DataCount = NULL;

    delete[] Cardinalite;
    Cardinalite = NULL;

    delete[] TmpHisto;
    TmpHisto = NULL;
}

void Relation::LoadMesure()
{
    string sFileName = string(FileName) + ".mes";
    cerr << "Ouverture du fichier MESURE <" << sFileName << '>';

    FILE *fd = fopen(sFileName.c_str(), "rt");
    if (!fd)
    {
        cerr << endl << "Erreur d'ouverture du fichier MESURE <" << sFileName
                << ">\nUtilisation de valeurs générées";

        for (unsigned int nLigne = 0; nLigne < NbTuple; ++nLigne)
        {
            Mesure1[nLigne] = 1 /** 10*/;
            Mesure2[nLigne] = 1 /** 10*/;
        }
    }
    else
    {
        cerr << " [OK]" << endl;
        cerr << "Récupération des mesures en cours";

        char buf[512];
        for (unsigned int nLigne = 0; nLigne < NbTuple; ++nLigne)
        {
            assert(fgets(buf, sizeof buf, fd) != NULL);
            char *p = strtok(buf, ", ");
            Mesure1[nLigne] = atoi(p);

            p = strtok(NULL, ", ");
            Mesure2[nLigne] = atoi(p);
        }

        fclose(fd);

    }

    cerr << " [OK]" << endl;
}

void Relation::AfficherMesure()
{
    cerr << "Mesure" << endl;
    for (unsigned int nLigne = 0; nLigne < NbTuple; ++nLigne)
        cerr << "Mesure[" << nLigne << "] = " << Mesure1[nLigne] << ", "
                << Mesure2[nLigne] << endl;
}

void Relation::ReadAttributeName(FILE *fd)
{
    char szLigne[2048];

    if (fgets(szLigne, sizeof szLigne, fd) != szLigne)
        throw CExc("Erreur de lecture nsRelalation::ReadAttributeName( )[LINE]");

    char *p = strtok(szLigne, ", \n");
    unsigned int i;
    for (i = 0; (i < NbAttr) && p; ++i)
    {
        NameOfAttribute.push_back(p);
        p = strtok(NULL, ", \n");
    }

    if ((i < NbAttr) || p)
        throw CExc("Erreur de lecture nsRelation:ReadAttributeName()[ATTR]");
}

void Relation::ReadTuples(FILE *fd)
{
    char szLigne[512];
    unsigned int i;

    for (i = 0; i < NbTuple; ++i)
    {
        if (fgets(szLigne, sizeof szLigne, fd) != szLigne)
            throw CExc("Erreur de lecture nsRelalation::ReadTuples( )[LINE]");

        char *p = strtok(szLigne, ", \n");
        unsigned int a;
        for (a = 0; (a < NbAttr) && p; ++a)
        {
            RelationData[a][i] = (unsigned int) atoi(p);
            p = strtok(NULL, ", \n");
        }
        if ((a < NbAttr) || p)
            throw CExc("Erreur de lecture nsRelation:ReadTuples()[ATTR]");
    }

    if (i < NbTuple)
        throw CExc("Erreur de lecture nsRelation:ReadTuples()[TUPLES]");
}

void Relation::LoadRelation()
{
    string sFileName = string(FileName) + ".rel";
    cerr << "Ouverture du fichier <" << sFileName << '>';

    FILE *fd = fopen(sFileName.c_str(), "rt");
    if (!fd)
        throw CExc(string("Erreur d'ouverture du fichier <")
                + sFileName.c_str() + ">");

    cerr << " [OK]" << endl;
    cerr << "Récupération des données en cours";

    //ReadAttributeName(fd);
    ReadTuples(fd);

    cerr << " [OK]" << endl;

    fclose(fd);
}

// Allocation de memoire pour la taille max des fragments des attr
// retourne LE PLUS GRAND DES ATTRIBUTS
unsigned int Relation::InitDataCount(unsigned int *indexData)
{
    unsigned int maxAttr = 0;
    for (unsigned int a = 0; a < NbAttr; ++a)
    {
        Cardinalite[a] = Cardinality(indexData, NbTuple, a);
        unsigned int maxCour = Max(indexData, NbTuple, a);
        if (maxCour >= maxAttr)
            maxAttr = maxCour;
        DataCount[a] = new unsigned int[Cardinalite[a]];
    }
    return maxAttr;
}

unsigned int Relation::Max(unsigned int *indexData, unsigned int taille,
        unsigned int dim)
{
    unsigned int maxAttr = 0;
    for (unsigned int t = 0; t < taille; ++t)
    {
        if (RelationData[dim][indexData[t]] >= maxAttr)
            maxAttr = RelationData[dim][indexData[t]];
    }
    return maxAttr;
}

// Calcule la cardinalite d'un attribut par rapport aux donnees en entree
unsigned int Relation::Cardinality(unsigned int *pData, unsigned int uSize, unsigned int d)
{
    unsigned int uCard = 0;

    memset(TmpHisto, 0, sizeof(unsigned int) * NbTuple);

    for (unsigned int t = 0; t < uSize; ++t)
        if (0 == TmpHisto[RelationData[d][pData[t]]])
        {
            TmpHisto[RelationData[d][pData[t]]] = 1;
            ++uCard;
        }

    return uCard;
}


void Relation::AfficherRelation(unsigned int indexData[], unsigned int taille)
{
    if (!RelationData)
    {
        cerr << "La relation est vide..." << endl;
        return;
    }

    unsigned int i;

    cerr << "Relation" << endl;
    cerr << NameOfAttribute << endl;

    for (i = 0; i < taille; ++i)
    {
        cerr << indexData[i]<< "), ";
        unsigned int a;
        for (a = 0; a < NbAttr - 1; ++a)
            cerr << RelationData[a][indexData[i]] << ", ";
        cerr << RelationData[a][indexData[i]]<<" :"<<Mesure1[indexData[i]] <<" "<< Mesure2[indexData[i]] << endl;
    }
}


void Relation::AfficherRelation()
{
    if (!RelationData)
    {
        cerr << "La relation est vide..." << endl;
        return;
    }

    unsigned int i;

    cerr << "Relation" << endl;
    cerr << NameOfAttribute << endl;

    for (i = 0; i < NbTuple; ++i)
    {
        cerr << i<< "), ";
        unsigned int a;
        for (a = 0; a < NbAttr - 1; ++a)
            cerr << RelationData[a][i] << ", ";
        cerr << RelationData[a][i] <<" :"<<Mesure1[i] <<" "<< Mesure2[i] << endl;
    }
}
}

