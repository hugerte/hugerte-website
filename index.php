<?php

require __DIR__ . '/vendor/autoload.php';

class Redirect {
    public function __construct(public string $location, public int $statusCode = 302, public string $content = "") {}
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->get('/', fn () => file_get_contents('views/index.html'));
    $r->get('/powered-by-hugerte', fn () => new Redirect('/', 307)); // TODO: Maybe dedicated page later.
    $r->get('/docs/hugerte/1/changelog', fn () => new Redirect('https://github.com/hugerte/hugerte/blob/main/modules/hugerte/CHANGELOG.md'));
    $r->get('/docs/hugerte/1/vite-es6-npm', fn () => new Redirect('https://github.com/hugerte/hugerte-docs/?tab=readme-ov-file#bundling'));
    $r->get('/docs/hugerte/1/{page}', fn ($params) => <<<EOF
        <!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Docs page redirection | HugeRTE</title>
            <link href="/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
          </head>
          <body>
            <main class="container mt-3">
              <h1>We do not have docs for this topic here yet.</h1>
              <p style="text-align: justify;">Please visit the <a href="https://tiny.cloud/docs/tinymce/6/$params[page]" id="link">TinyMCE docs page</a>, but note that you have to replace every instance of <code>tinymce</code> by <code>hugerte</code> in your code. We've decided to write docs for HugeRTE ourselves instead of copying the ones from TinyMCE over. The TinyMCE docs are licensed under an <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/">Attribution-NonCommercial-ShareAlike 3.0 Unported</a> license, which could create legal problems when copying code snippets (at least larger ones) into a codebase that is used for commercial purposes or that is distributed under licenses different than CC-BY-NC-SA. This does not match the neither the MIT nor the GPL license used by TinyMCE today. TinyMCE isn't the only open source project which licenses its documentation under terms that could create legal issues for commercial projects although the license of the editor itself was permissive. There is even a project that licenses its docs under a license that prohibits publishing modifications of it (not explicitly excluding code samples!) although the license of the source code of the project itself is MIT.<br>Anyway, at HugeRTE, we're going to write MIT-licensed docs for our project ourselves. <a href="https://github.com/hugerte/hugerte-docs">This repo is the start.</a></p>
            </main>
            <script>
                document.querySelector('#link').href = document.querySelector('#link').href + (location.hash ?? '');
            </script>
          </body>
        </html>
    EOF);
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
