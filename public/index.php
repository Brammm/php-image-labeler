<?php

use Fig\Http\Message\StatusCodeInterface;
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

    $addText = function (Image $img, string $text, int $size) {
        static $y = 70;

        $img->text(
            $text,
            100,
            $y,
            static function (Font $font) use ($size) {
                $font
                    ->valign('top')
                    ->file(__DIR__ . '/../templates/courier.ttf')
                    ->size($size);
            });

        $y += $size + 10;
    };

    if (array_key_exists('size', $params) && $params['size']) {
        $addText($img, 'Maat: ' . $params['size'], 30);
    }
    if (array_key_exists('brand', $params) && $params['brand']) {
        $addText($img, 'Merk: ' . $params['brand'], 30);
    }
    if (array_key_exists('state', $params) && $params['state']) {
        $addText($img, $params['state'], 30);
    }
    if (array_key_exists('price', $params) && $params['price']) {
        $addText($img, 'Startprijs: ' . $params['price'] . ' euro', 30);
    }
    if (array_key_exists('halfbid', $params)) {
        $addText($img, 'Opbieden per halve euro toegestaan', 20);
    }
    if (array_key_exists('pickup', $params)) {
        $addText($img, 'Enkel op te halen bij verkoper of regioverantwoordelijke', 20);
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

$app->post('/save', function (Request $request, Response $response) use ($images) {
    $body  = $request->getParsedBody();
    $index = $body['index'];
    $path  = $images[$index];
    $img   = getImage($path, $body);

    $img->save(__DIR__ . '/../images/processed/' . basename($path));

    return $response
        ->withStatus(StatusCodeInterface::STATUS_FOUND)
        ->withHeader('Location', '/?index=' . min(count($images) - 1, $index + 1));
});

$app->run();
