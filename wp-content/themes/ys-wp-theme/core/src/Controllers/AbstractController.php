<?php
namespace RB\Site\Controller\Api;

abstract class AbstractController
{
    /**
     * @var string
     */
    protected $namespace = 'rb';
    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var string
     */
    protected $version = 'v1.0';
    /**
     * @var \RB\Site\Repository\AbstractRepository
     */
    protected $repo;

    public function run()
    {
        $this->setupHooks();
    }

    protected function setupHooks()
    {
        add_action('rest_api_init', [$this, 'registerRoutes'], 10, 0);
    }

    protected function addRoute(string $route, array $args = [], bool $override = false): bool
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

        return register_rest_route($namespace, $endpoint . $route, $args, $override);
    }

    abstract public function registerRoutes();
}
