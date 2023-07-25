#!/bin/bash

OLDDIR=`pwd`
DIR=`dirname $0`
cd $DIR &> /dev/null

doxygen TAGED.doxyfile

cd doc/latex

make pdf

cd $OLDDIR &> /dev/null
