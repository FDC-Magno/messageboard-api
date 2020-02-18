<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use RKS\Middleware\IpAddress;

require __DIR__ . '/../vendor/autoload.php';

ORM::configure('mysql:host=localhost;dbname=messageboard');
ORM::configure('username', 'root');
ORM::configure('password', '');

$app = AppFactory::create();

$app->addBodyParsingMiddleware();
$app->add(new RKA\Middleware\IpAddress());
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $users = ORM::for_table('users')->where('id', '23')->find_array();
    $response->getBody()->write(json_encode($users));
    return $response;
});

$app->post('/login', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $user = ORM::for_table('users')->where('email', $data['email'])->where('password', $data['password'])->find_array();
    $response->getBody()->write(json_encode($user));
    return $response;
});

$app->post('/register', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $ip = $request->getAttribute('ip_address');
    $user = ORM::for_table('users')->create();
    $user->set(array(
        'name' => $data['name'],
        'password' => $data['password'],
        'email' => $data['email'],
        'image' => $data['image'],
        'gender' => $data['gender'],
        'birthdate' => $data['birthday'],
        'hubby' => $data['hubby'],
        'created_ip' => $ip,
        'modified_ip' => $ip,
    ));
    $user->set_expr('created', 'NOW()');
    $user->set_expr('modified', 'NOW()');
    $user->save();
    $response->getBody()->write(json_encode($user));
    return $response;
});

$app->run();