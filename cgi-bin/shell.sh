#!/bin/bash

./lda/lda est $1 $2 lda/settings.txt wordcount.txt random ./lda_result

python generate_db.py ../htdocs/yelp_data_tmp.db wordcount.txt lda_result/final.beta lda_result/final.gamma vocab.txt ../htdocs/yelp_academic_dataset_review.json

rm ../htdocs/yelp_data.db

mv ../htdocs/yelp_data_tmp.db ../htdocs/yelp_data.db
