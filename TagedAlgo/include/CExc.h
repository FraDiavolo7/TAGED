
#ifndef __CEXC_H__
#define __CEXC_H__

#include <iostream>
#include <string>
#include <cerrno>
#include <exception>
namespace Taged
{
/**
 * @brief Classe CExc.
 * 
 */
class CExc : public std::exception
{
  protected :
    std::string m_Libelle;

  public:
    /**
     * @brief Construct a new CExc object.
     * 
     * @param Libelle 
     */
    CExc (const std::string Libelle);

    /**
     * @brief Destroy the CExc object.
     * 
     */
    virtual ~CExc () {}

    /**
     * @brief Fonction Afficher.
     * 
     * @param os 
     * @return std::ostream& 
     */
    virtual std::ostream & Afficher (std::ostream & os = std::cerr) const;

    virtual const char * what ( void ) const throw ();
};  //  CExc

////////////////// class CExc ////////////////

inline CExc::CExc (const std::string Libelle) : m_Libelle (Libelle) {}

inline std::ostream & CExc::Afficher (std::ostream & os /*= cerr*/ ) const
{
    os << std::endl;
    return os << m_Libelle ;

}  //  Afficher()

inline const char * CExc::what ( void ) const throw ()
{
   return m_Libelle.c_str ();
} // what

}
#endif  // __CEXC_H__

