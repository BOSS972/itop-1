#!/bin/bash

for _csv_file in $4*.csv; do
	mysql -u $1 --password=$2 --database=$3 --local-infile=1 -e "
      LOAD DATA LOCAL INFILE \"$_csv_file\"
      INTO TABLE $5
      FIELDS TERMINATED BY ',' 
      OPTIONALLY ENCLOSED BY '\"' 
      LINES TERMINATED BY '\n' 
      IGNORE 1 LINES
      ($6);"
done