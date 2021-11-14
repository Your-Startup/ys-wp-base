<?php

namespace YS\Core;

use YS\Core\Wp\PostType;
use YS\Core\Repository\AbstractRepository;

class Site
{
    static self $instance;

    public /*readonly*/ PostType $postType;

    public function __construct()
    {
        $this->postType = new PostType();
    }

    public static function getInstance(): static
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getRepository($name = 'post'): AbstractRepository
    {
        $name = ucfirst($name);

        $class = '\YS\Core\Repository\\' . $name . '\\' . $name . 'Repository';
        if (class_exists($class)) {
            $repo = new $class();
        }

        return $repo;
    }
}