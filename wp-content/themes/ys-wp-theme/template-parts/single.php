<?php

$repo = new \YS\PostRepository();
$post = $repo->findNew(1, [
    'id',
    'title',
    'field1'
]);

?>

