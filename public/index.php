<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

function render(string $template, array $data = []): string {
    extract($data, EXTR_SKIP);

    ob_start();
    include __DIR__ . '/../templates/index.php';

    return ob_get_clean();
}

$app->get('/', function (Request $request, Response $response) {
    $images = glob(__DIR__ . '/../images/*.{jpg,jpeg,png}', GLOB_BRACE);

    $index = $request->getQueryParams()['index'] ?? 0;

    $response->getBody()->write(render('home', [
        'images' => $images,
        'index' => $index,
    ]));

    return $response;
});

$app->run();
