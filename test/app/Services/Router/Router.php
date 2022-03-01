<?php

namespace App\Services\Router;

use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\RouteNotFoundException;
use App\Services\Request\RequestInterface;
use App\Services\Response\ResponseInterface;

class Router
{
    protected $request;

    protected $response;

    public static $routes = [];

    protected $matchRoutes = [];

    protected $allowedMethods = [];

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Load routes from router file
     *
     * @return $this
     */
    public function loadRoutes(): Router
    {
        $router = new static($this->request, $this->response);

        require __DIR__ . '/../../../routes.php';

        return $router;
    }

    /**
     * Check matched route, allowed request method then dispatch to controller
     *
     * @return mixed
     * @throws \App\Exceptions\MethodNotAllowedException
     * @throws \App\Exceptions\RouteNotFoundException
     */
    public function handle()
    {
        if (0 === count($this->matchRoutes)) {
            throw new RouteNotFoundException();
        }

        if (! $this->isMethodAllowed()) {
            throw new MethodNotAllowedException();
        }

        return $this->dispatchController();
    }

    /**
     * Find all matched routes without take care about request method.
     * All matched routes will be pushed to $matchedRoutes
     *
     * @return $this
     */
    public function findMatchedRoutes(): Router
    {
        $requestUri = $this->request->getPath();

        foreach (self::$routes as $route) {
            $parameters = $this->getParameters($route['uri']);

            /**
             * In case the route path doesn't have parameters and route path matches with request uri
             * we can directly push this route to $matchRoutes property
             *
             * @see \App\Services\Router\Router::getParameters()
             */
            if (0 === count($parameters) && $route['uri'] === $requestUri) {
                array_push($this->matchRoutes, $route);
            }

            /**
             * Otherwise we have to parse the segments of request path and route path
             *
             * Each segment which is not a parameter of route path which matches request path
             * will be pushed as an segment of $tempSegments variable
             *
             * For segment of route path which has parameter (start with (:) character), its index
             * will be replaced by segment of request URI which has same index and directly pushed
             * to $tempSegments variable
             */
            if (count($parameters) > 0 && strlen($requestUri) > 0) {
                $routeUriSegments = explode('/', $route['uri']);
                $requestUriSegments  = explode('/', $requestUri);
                $tempSegments = [];

                foreach ($routeUriSegments as $index => $segment) {
                    if (str_start_with($segment, ':')) {
                        $tempSegments[$index] = array_get($requestUriSegments, $index);
                    }

                    if ($segment === array_get($requestUriSegments, $index)) {
                        $tempSegments[$index] = $segment;
                    }
                }

                $tempUri = join('/', $tempSegments);

                if ($requestUri === $tempUri) {
                    array_push($this->matchRoutes, $route);
                }
            }
        }

        return $this;
    }

    /**
     * Find the allowed method from matched routes.
     * From here we can get the correct method with allowed request method
     *
     * @see \App\Services\Router\Router::findMatchedRoutes()
     * @return $this
     */
    public function findAllowedMethods(): Router
    {
        $this->allowedMethods = array_map(
            function ($route) {
                return array_get($route, 'method');
            },
            $this->matchRoutes
        );

        return $this;
    }

    /**
     * Check whether or not the method of current request is in the found allowed methods
     *
     * @see \App\Services\Router\Router::findAllowedMethods()
     * @return bool
     */
    public function isMethodAllowed(): bool
    {
        return in_array($this->request->getMethod(), $this->allowedMethods);
    }

    /**
     * Extract the Controller class name and its method from matched route.
     * After that dispatch request to this controller for processing then return response.
     *
     * @see \App\Services\Router\Router::middlewares()
     * @return mixed
     */
    public function dispatchController()
    {
        $route = $this->findMatchedRoute();

        $controllerClass = $route['controller'][0];
        $method = $route['controller'][1];

        return (new $controllerClass($this->request, $this->response))->{$method}();
    }

    /**
     * From found routes which matched the request path,
     * we can find the first route which allows the request method (GET, POST...)
     *
     * @see \App\Services\Router\Router::findMatchedRoutes()
     * @return mixed
     */
    protected function findMatchedRoute()
    {
        return collect($this->matchRoutes)
            ->filter(function ($route) {
                return $route['method'] === $this->request->getMethod();
            })
            ->first();
    }

    /**
     * Get parameters of route path (not request path)
     *
     * @param string $routePath
     * @return array
     */
    public function getParameters(string $routePath): array
    {
        $pattern = '/(:[a-zA-Z0-9_]+)/';
        $count = preg_match_all($pattern, $routePath, $matches);

        return is_int($count) && $count > 0 ? $matches[1] : [];
    }

    public static function get(string $uri, $controller, array $middlewares = null)
    {
        self::$routes[] = [
            'method' => 'GET',
            'uri' => $uri,
            'controller' => $controller,
            'middlewares' => $middlewares ?? []
        ];
    }

    public static function post(string $uri, $controller, array $middlewares = null)
    {
        self::$routes[] = [
            'method' => 'POST',
            'uri' => $uri,
            'controller' => $controller,
            'middlewares' => $middlewares ?? []
        ];
    }

    public static function put($uri, $controller, array $middlewares = null)
    {
        self::$routes[] = [
            'method' => 'PUT',
            'uri' => $uri,
            'controller' => $controller,
            'middlewares' => $middlewares ?? []
        ];
    }

    public static function delete($uri, $controller, array $middlewares = null)
    {
        self::$routes[] = [
            'method' => 'DELETE',
            'uri' => $uri,
            'controller' => $controller,
            'middlewares' => $middlewares ?? []
        ];
    }

    /**
     * Registering middlewares to route
     *
     * @param array $middlewares
     */
    public function middlewares(array $middlewares = [])
    {
        $lastIndex = count(self::$routes) - 1;

        self::$routes[$lastIndex]['middlewares'] = $middlewares;
    }
}
