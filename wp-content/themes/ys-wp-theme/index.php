<?php

get_header();

$poem = \YS\Core\Site::getInstance()
    ->getUserRepository()
    ->find(1);

print_r($poem);
//print_r($posts);
//print_r($terms);
get_footer();