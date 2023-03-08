#!/bin/bash


db_list=("taged_collection" "taged_hns" "taged_match3")
dump_folder=/opt/taged/taged/taged/dump


function dump ()
{
    db=$1
    dump_file=$dump_folder/$db.sql

    if [ -s $dump_file ]
    then
        mv $dump_file $dump_file.old
    fi

    pg_dump $db > $dump_file
}

for db in ${db_list[@]}
do
    dump $db
done
