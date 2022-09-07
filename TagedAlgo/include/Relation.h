// Relations.h: namespace Relations.
//
//////////////////////////////////////////////////////////////////////

#ifndef __NSRELATION_H__
#define __NSRELATION_H__

#include <string>
#include <vector>
#include <array>
#include <iostream>
#include <limits>
#include "Attribute.h"

namespace Taged
{

typedef std::vector<std::string> CListeString;
typedef std::array<unsigned int,8> nuplet;
const unsigned int ALL = std::numeric_limits<unsigned int>::max();

/**
 * @brief Classe Relation.
 * 
 */
class Relation
{
public:
    // Nombre de tuples de la relation
    const unsigned int NbTuple;

    // Nombre d'attributs de la relation
    const unsigned int NbAttr;

    // Liste des valeurs MESURE dans la premiere relation
    unsigned int *Mesure1;
    unsigned int *Mesure2;

    // Les donnees de la relation...
    unsigned int **RelationData;

    // Tableau des indexes utilises pendant buc
    unsigned int **TabIndexData;

    // nombre d'elem de chaque classe de chaque attribut
    unsigned int **DataCount;

    // Ligne du datacube en cours de calcul
    nuplet OutputRec;

    // Val agregee calculee pour la ligne du datacube en cours
    unsigned int ValAgg1;
    unsigned int ValAgg2;

    unsigned int *Cardinalite;
public:
    /**
     * @brief Construct a new Relation object. Initialisation des membres du namespace.
     * 
     * @param FileName 
     * @param NbAttr 
     * @param NbTuple 
     */
    Relation(const char* FileName, unsigned int NbAttr, unsigned int NbTuple);

    /**
     * @brief Destroy the Relation object. Liberer la memoire occupee par le namespace.
     * 
     */
    ~Relation();

    /**
     * @brief Charge depuis le fichier les valeurs mesures.
     * 
     */
    void LoadMesure();

    /**
     * @brief Affiche les valeurs mesures.
     * 
     */
    void AfficherMesure();

    /**
     * @brief Chargement de la relation.
     * 
     */
    void LoadRelation();

    /**
     * @brief Affiche les donnees de la relation.
     * 
     */
    void AfficherRelation();

    /**
     * @brief Affiche les donnees de la relation.
     * 
     * @param indexData 
     * @param taille 
     */
    void AfficherRelation(unsigned int indexData[], unsigned int taille);

    /**
     * @brief Inititlise la nb max de fragment pour chaque attr.
     * 
     * @param pIndexData 
     * @return unsigned int 
     */
    unsigned int InitDataCount(unsigned int *pIndexData);

    /**
     * @brief Calcule la cardinalite d'un attribut par rapport aux donnees en entree.
     * 
     * @param pData 
     * @param uSize 
     * @param d 
     * @return unsigned int 
     */
    unsigned int Cardinality(unsigned int *pData, unsigned int uSize, unsigned int d);

    /**
     * @brief Fonction Max.
     * 
     * @param indexData 
     * @param taille 
     * @param dim 
     * @return unsigned int 
     */
    unsigned int Max(unsigned int *indexData, unsigned int taille,
            unsigned int dim);
private:
    // Noms des attributs si precises dans le fichier d'entree
    CListeString NameOfAttribute;
    // Nom du fichier contenant les données
    const char* FileName;

    // tous les attributs de la relation
    CAttribute* AttrRelation;

    // Utiliser pour calculer les cardinalites des dimension en fonction des entrees
    unsigned int* TmpHisto;

    /**
     * @brief Fonction ReadAttributeName.
     * 
     * @param fd 
     */
    void ReadAttributeName(FILE *fd);

    /**
     * @brief Fonction ReadTuples.
     * 
     * @param fd 
     */
    void ReadTuples(FILE *fd);
};

/*
std::ostream &operator<<(std::ostream &os, const Relation &r);

inline std::ostream &operator<<(std::ostream &os, const Relation &r)
{
    for (unsigned int i=0; i < r.NbTuple; ++i)
    {
        for(unsigned int j=0; j <r.NbAttr - 1; j++)
        {
            os << r.RelationData[j][i] << ", ";
        }
        os <<  r.RelationData[r.NbAttr - 1][i]<<endl;
    }
    return os;
}*/
}

#endif // __NSRELATION_H__
