<?php

# Specify your AWS credentials and configs below

# Access key
$access_key = getenv('A_KEY');
# Secret access key
$secret_key = getenv('S_KEY');
# Region
$region = 'ap-southeast-1';
# Autoscaling group
$as_group = getenv('EC2_GROUP');
# Document root
$doc_root = '/var/www/html/Deploy';