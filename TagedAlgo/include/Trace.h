
#ifndef __TRACE_H__
#define __TRACE_H__

#ifdef TRACEON
    #define TRACE(x)		x
#else
    #define TRACE(x)
#endif

/*
#ifdef TRACEMEMON
    #define TRACEMEM(x)     x
#else
    #define TRACEMEM(x)
#endif
*/

#endif //__TRACE_H__
