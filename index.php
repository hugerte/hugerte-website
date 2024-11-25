<?php

require __DIR__ . '/vendor/autoload.php';

class Redirect {
    public function __construct(public string $location, public int $statusCode = 302, public string $content = "") {}
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->get('/', fn () => file_get_contents('views/index.html'));
    $r->get('/powered-by-hugerte', fn () => new Redirect('/', 307)); // TODO: Maybe dedicated page later.
    $r->get('/docs/hugerte/1/changelog', fn () => new Redirect('https://github.com/hugerte/hugerte/blob/main/modules/hugerte/CHANGELOG.md'));
});

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = rtrim($url, '/');
if (empty($url)) {$url = '/';}
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $url);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo 'Not found.';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo 'Method not allowed. Allowed methods are: ' . implode(', ', $routeInfo[1]);
        break;
    case FastRoute\Dispatcher::FOUND:
        $ret = call_user_func($routeInfo[1], $routeInfo[2]);
        if ($ret instanceof Redirect) {
            http_response_code($ret->statusCode);
            header("Location: $ret->location");
            echo $ret->content;
        } elseif (is_string($ret)) {
            echo $ret;
        }
        break;
}
