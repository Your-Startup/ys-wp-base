<?php

namespace YS\Core\Controller;

use YS\Core\Repository\AbstractRepository;

abstract class AbstractController
{
    public array $queries = [];
    protected string $namespace = 'ys';
    protected string $version   = 'v1.0';
    protected string $endpoint;
    protected AbstractRepository $repo;

    public function run(): self
    {
        $this->initCustomPages();
        $this->setupHooks();

        return $this;
    }

    final protected function setupHooks()
    {
        add_action('rest_api_init', [$this, 'registerRoutes'], 10, 0);
        add_action('parse_query', [$this, 'actionWpQuery']);
    }

    final public function actionWpQuery($wpQuery)
    {
        foreach ($this->queries as $query) {
            if (!get_query_var($query)) {
                continue;
            }

            $wpQuery->is_home       = false;
            $wpQuery->is_front_page = false;
        }
    }

    final protected function addRoute(string $route, array $args = [], bool $override = false): self
    {
        $namespace = $this->namespace . '/' . $this->version;
        $endpoint  = '/' . $this->endpoint;

        if (isset($args['args'])) {
            $commonArgs = $args['args'];
            unset($args['args']);
        } else {
            $commonArgs = [];
        }

        if (isset($args['callback'])) {
            $args = [$args];
        }

        $defaults = [
            'methods'             => 'GET',
            'callback'            => null,
            'args'                => [],
            'permission_callback' => '__return_true'
        ];

        foreach ($args as $key => &$argGroup) {
            if (!is_numeric($key)) {
                continue;
            }
            $argGroup         = array_merge($defaults, $argGroup);
            $argGroup['args'] = array_merge($commonArgs, $argGroup['args']);
        }

        register_rest_route($namespace, $endpoint . $route, $args, $override);

        return $this;
    }

    abstract public function registerRoutes();

    abstract protected function initCustomPages();
}
