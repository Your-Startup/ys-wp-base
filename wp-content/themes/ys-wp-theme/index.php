<?php

get_header();

$repoPost = \YS\Core\Site::getInstance()->getRepository();
$repoTerm = \YS\Core\Site::getInstance()->getRepository('term');

$post  = $repoPost->find(1);
$posts = $repoPost->findAll([
    'fields' => [
        'id',
        'title',
        'excerpt',
        'field1',
        'field_1'
    ]
]);

$terms = $repoTerm->findAll();

print_r($post);
print_r($posts);
print_r($terms);
get_footer();