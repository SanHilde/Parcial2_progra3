<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
error_reporting(-1);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/CuentaBancoController.php';
require_once './controllers/DepositoController.php';
require_once './controllers/RetiroController.php';
require_once './controllers/PuntosController.php';
require_once './controllers/AjusteController.php';
// require_once './controllers/LogInController.php';
require_once './db/AccesoDatos.php';
// require_once './middlewares/SectorMiddleware.php';
// require_once './middlewares/AuthMiddleware.php';
// require_once './utils/AutentificadorJWT.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$app->setBasePath('/public');
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();


$app->group('/', function (RouteCollectorProxy $group) {
  $group->get('[/]', function (Request $request, Response $response, $args) {
      $payload = json_encode(array("mensaje" => "Bienvenido a Banco Provincia"));
      sleep(2);
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  });//->add(new AuthMiddleware());
  $group->post('[/]', \LogInController::class . ':Loggearse');
});

$app->group('/punto', function (RouteCollectorProxy $group) {
  $group->post('1', \PuntosController::class . ':CuentaAlta');
  $group->post('2', \PuntosController::class . ':ConsultarCuenta');
  $group->post('3', \PuntosController::class . ':DepositoCuenta');
  $group->get('4', \PuntosController::class . ':ConsultarMovimientos');
  $group->put('5', \PuntosController::class . ':ModificarCuenta');
  $group->post('6', \PuntosController::class . ':RetiroCuenta');
  $group->post('7', \PuntosController::class . ':AjusteCuenta');
  $group->delete('9', \PuntosController::class . ':BorrarCuenta');
  $group->get('10', \PuntosController::class . ':ConsultarMovimientos');
});


// Run app
$app->run();

