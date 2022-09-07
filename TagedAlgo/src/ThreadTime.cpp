
#include <sys/resource.h>
#include <stdio.h>

#include "ThreadTime.h"

struct rusage m_usage;

int ThreadTimeInit()
{
	return getrusage(RUSAGE_SELF, &m_usage) == 0;
}

long int ThreadTimeElapsedTime()
{
	struct rusage usage;
	long int duree;
	long heure;
	long minute;
	long seconde;
	long milliseconde;

	getrusage(RUSAGE_SELF, &usage);
	milliseconde = (usage.ru_utime.tv_usec - m_usage.ru_utime.tv_usec) / 1000;
	duree = usage.ru_utime.tv_sec - m_usage.ru_utime.tv_sec;
	if (milliseconde < 0)
	{
		milliseconde += 1000;
		duree--;
	}
	heure = duree / 3600;
	minute = (duree - (heure * 3600)) / 60;
	seconde = duree % 60;
	fprintf(stderr, "(%ld s %ld ms) %ld h %ld m %ld s %ld ms\n", duree, milliseconde, heure, minute, seconde, milliseconde);

	return duree;
}

