<?php

use Intervention\Image\Gd\Font;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
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

$images = glob(__DIR__ . '/../images/*.{jpg,jpeg,png}', GLOB_BRACE);

function getImage(string $path, array $params): Image {
    $imageManager = new ImageManager();

    $img = $imageManager->make($path);
    $img->resize(1200, 1200, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    });

    $yOffset = 100;
    if (array_key_exists('size', $params)) {
        $img->text(
            $params['size'],
            100,
            $yOffset,
            static function (Font $font) {
                $font
                    ->file(__DIR__ . '/../templates/courier.ttf')
                    ->size(100);
            });

        $yOffset += 50;
    }
    if (array_key_exists('price', $params)) {
        $img->text(
            'Startprijs: ' . $params['price'] . ' euro',
            100,
            $yOffset,
            static function (Font $font) {
                $font
                    ->file(__DIR__ . '/../templates/courier.ttf')
                    ->size(30);
            });

        $yOffset += 40;
    }

    if (array_key_exists('halfbid', $params)) {
        $img->text(
            'Opbieden per halve euro toegestaan',
            100,
            $yOffset - 10,
            static function (Font $font) {
                $font
                    ->file(__DIR__ . '/../templates/courier.ttf')
                    ->size(20);
            });

        $yOffset += 40;
    }

    if (array_key_exists('state', $params)) {
        $img->text(
            $params['state'],
            100,
            $yOffset,
            static function (Font $font) {
                $font
                    ->file(__DIR__ . '/../templates/courier.ttf')
                    ->size(30);
            });
    }

    return $img;
}

$app->get('/', function (Request $request, Response $response) use ($images) {
    $index = $request->getQueryParams()['index'] ?? 0;

    $response->getBody()->write(render('home', [
        'images' => $images,
        'index' => $index,
    ]));

    return $response;
});

$app->get('/image/{index}', function (Request $request, Response $response, array $args) use ($images) {
    $image = $images[$args['index']];

    return getImage($image, $request->getQueryParams())->psrResponse();
});

$app->run();
