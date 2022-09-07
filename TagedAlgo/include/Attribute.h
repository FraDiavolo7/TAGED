#ifndef __ATTRIBUTE_H__
#define __ATTRIBUTE_H__

#include <iostream>
#include <stdlib.h>
#include <string.h>
#define MAX_ATTRIBUTE 32

namespace Taged
{

/**
 * @brief Classe CAttribute.
 * 
 */
class CAttribute
{
  public:
    /**
     * @brief Fonction InitMaxNbAttribute.
     * 
     * @param uMaxNbAttribute 
     */
    static void InitMaxNbAttribute( unsigned int uMaxNbAttribute );

    /**
     * @brief Get the Max Attribute object.
     * 
     * @return unsigned int 
     */
    static inline unsigned int GetMaxAttribute( )
    {
        return MAX_ATTRIBUTE;
    }

  public:
    unsigned int m_V;

    /**
     * @brief Construct a new CAttribute object.
     * 
     */
    CAttribute( )
    {
        clear( );
    }

    /**
     * @brief Construct a new CAttribute object.
     * 
     * @param V 
     */
    CAttribute( unsigned int V )
    {
        clear( );
        Insert(V);
    }

    /**
     * @brief Destroy the CAttribute object.
     * 
     */
    ~CAttribute( )
    {

    }

    /**
     * @brief Fonction InitMem.
     * 
     */
    void InitMem( )
    {
        clear( );
    }

    /**
     * @brief Fonction size.
     * 
     * @return unsigned int 
     */
    unsigned int size() const
    {
	    unsigned int s = 0;
        for ( unsigned int j = 0; j < MAX_ATTRIBUTE ; ++j)
            if (m_V & (1 << j)) ++s;
        return s;
    }

    /**
     * @brief Fonction Insert.
     * 
     * @param i 
     */
    void Insert( unsigned int i )
    {
        m_V |= (1 << i);
    }

    /**
     * @brief Fonction Next.
     * 
     * @return true 
     * @return false 
     */
    bool Next( )
    {
        ++m_V;
        // pour les tours de compteurs...
        return 0 != m_V;
    }

    /**
     * @brief Fonction Skip.
     * 
     * @return true 
     * @return false 
     */
    bool Skip( )
    {
        for ( unsigned int j = 0; j < MAX_ATTRIBUTE ; ++j )
        {
            if (m_V & (1 << j)) break;
            m_V |= (1 << j);
        }
        ++m_V;

            // pour les tours de compteurs...
        return 0 != m_V;
    }

    /**
     * @brief Fonction Min.
     * 
     * @return unsigned int 
     */
    unsigned int Min( )
    {
        unsigned int j;

        for ( j = 0; j < MAX_ATTRIBUTE; ++j )
            if (m_V & (1 << j)) break;

        return j;
    }

    /**
     * @brief Fonction Max.
     * 
     * @return unsigned int 
     */
    unsigned int Max( )
    {
        unsigned int j;

        for ( j = MAX_ATTRIBUTE - 1; j > 0 ; --j )
            if (m_V & (1 << j)) break;

        return j;
    }

    /**
     * @brief Opérateur =.
     * 
     * @param bs 
     * @return CAttribute& 
     */
    inline CAttribute &operator=(const CAttribute &bs)
    {
	    m_V = bs.m_V;
	    return *this;
    }

    /**
     * @brief Opérateur &=.
     * 
     * @param bs 
     * @return CAttribute& 
     */
    CAttribute &operator&=(const CAttribute &bs)
    {
	    m_V &= bs.m_V;
    	return *this;
    }

    /**
     * @brief Opérateur &.
     * 
     * @param bs 
     * @return CAttribute 
     */
    CAttribute operator&(const CAttribute &bs) const
    {
	    CAttribute tmp(*this);
	    tmp &= bs;
	    return tmp;
    }

    /**
     * @brief Opérateur |=.
     * 
     * @param bs 
     * @return CAttribute& 
     */
    CAttribute &operator|=(const CAttribute &bs)
    {
	    m_V |= bs.m_V;
    	return *this;
    }

    /**
     * @brief Opérateur |.
     * 
     * @param bs 
     * @return CAttribute 
     */
    CAttribute operator|(const CAttribute &bs) const
    {
	    CAttribute tmp(*this);
	    tmp |= bs;
	    return tmp;
    }

    /**
     * @brief Opérateur ==.
     * 
     * @param bs 
     * @return true 
     * @return false 
     */
    bool operator==(const CAttribute &bs) const
    {
	    return m_V == bs.m_V;
    }

    /**
     * @brief Opérateur !=.
     * 
     * @param bs 
     * @return true 
     * @return false 
     */
    bool operator!=(const CAttribute &bs) const
    {
        return !(*this == bs );
    }

    /**
     * @brief Opérateur <=.
     * 
     * @param bs 
     * @return true 
     * @return false 
     */
    bool operator<=(const CAttribute &bs) const
    {
	    return (m_V & bs.m_V) == m_V;
    }

    /**
     * @brief Opérateur <.
     * 
     * @param bs 
     * @return true 
     * @return false 
     */
    bool operator<(const CAttribute &bs) const
    {
        unsigned int s1 = size( ), s2 = bs.size( );

        if ( s1 < s2 )
            return true;
        if ( s2 < s1 )
            return false;
	    return m_V < bs.m_V;
    }

    /**
     * @brief Opérateur -=.
     * 
     * @param bs 
     * @return CAttribute& 
     */
    CAttribute &operator-=(const CAttribute &bs)
    {
	    m_V &= ~bs.m_V;
	    return *this;
    }

    /**
     * @brief Opérateur -.
     * 
     * @param bs 
     * @return CAttribute 
     */
    CAttribute operator-(const CAttribute &bs) const
    {
	    CAttribute tmp(*this);
	    tmp -= bs;
	    return tmp;
    }

    /**
     * @brief Fonction clear.
     * 
     */
    void clear( )
    {
        m_V = 0;
    }

    /**
     * @brief Fonction empty.
     * 
     * @return true 
     * @return false 
     */
    bool empty() const
    {
        return m_V == 0;
    }

    /**
     * @brief Fonction contain.
     * 
     * @param x 
     * @return true 
     * @return false 
     */
    bool contain(unsigned int x) const
    {
	    return (m_V & (1 << x)) != 0;
    }

    /**
     * @brief Fonction erase.
     * 
     * @param x 
     */
    void erase(unsigned int x)
    {
        m_V &= ~(1 << x);
    }

    /**
     * @brief Fonction is_same_begin.
     * 
     * @param attr 
     * @return true 
     * @return false 
     */
    bool is_same_begin( const CAttribute &attr ) const // Les 2 attr ont le meme debut
    {
        unsigned int a;
            // On cherche les premiers 2 bits differents (attributs differents)
        for ( a = 0; a < GetMaxAttribute( ); a++ )
            if ( contain( a ) != attr.contain( a ) )
                break;

            // On est sur 2 bits differents
        unsigned int j;
            // On verifie qu'il n'y a plus qu'un seul bit a 1
        for ( j = a; ( (j < GetMaxAttribute( )) && !contain( j ) ); j++ );
        for ( j++;   ( (j < GetMaxAttribute( )) && !contain( j ) ); j++ );

            // On a au moins 2 bits a 1 donc erreur
        if ( j < GetMaxAttribute( ) )
            return false;

            // On verifie qu'il n'y a plus qu'un seul bit a 1
        for ( j = a; ( (j < GetMaxAttribute( )) && !attr.contain( j ) ); j++ );
        for ( j++;   ( (j < GetMaxAttribute( )) && !attr.contain( j ) ); j++ );

            // On a au moins 2 bits a 1 donc erreur
        if ( j < GetMaxAttribute( ) )
            return false;

        return j == GetMaxAttribute( );
    }

};

/**
 * @brief Opérateur <<.
 * 
 * @param os 
 * @param bs 
 * @return std::ostream& 
 */
inline std::ostream &operator<<(std::ostream &os, const CAttribute &bs)
{
	for ( unsigned int i = 0; i < MAX_ATTRIBUTE; ++i)
            if ( bs.m_V & (1 << i) )
                os << i << " ";
	return os;
}
}
#endif /*__ATTRIBUTE_H__*/
