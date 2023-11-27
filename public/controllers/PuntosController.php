<?php
require_once './models/CuentaBanco.php';

class PuntosController
{
    public function CuentaAlta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $cuentaBancoController = new CuentaBancoController();
        if (isset($parametros['nroDeCuenta']))
        {
            $cuentaBancoController->ModificarUno($request, $response, $args);

        } else
        {
            $cuentaBancoController->CargarUno($request, $response, $args);
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['nroDeCuenta'], $parametros['tipoDeCuenta'])) {
            $cuentaBancoController = new CuentaBancoController();
            $respuesta = $cuentaBancoController->ExisteCuenta($parametros['tipoDeCuenta'], $parametros['nroDeCuenta']);
            if (gettype($respuesta) != "string") 
            {
                if (count($respuesta) > 0) {
                    $datosCuentas = array();
                    foreach ($respuesta as $cuenta) {
                        $datosCuentas[] = "Moneda: " . $cuenta->moneda . ", Saldo: " . $cuenta->saldoInicial;
                    }
                    $payload = json_encode(array("mensaje" => $datosCuentas));
                } else {
                    $payload = json_encode(array("mensaje" => "No existe ese numero de cuenta"));
                }                  
            } else {
                $payload = json_encode(array("mensaje" => $respuesta));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DepositoCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $cuentaBancoController = new CuentaBancoController();
        $depositoController = new DepositoController();
        if (isset($parametros['nroDeCuenta'], $parametros['moneda'], $parametros['tipoDeCuenta'])) {
            $datosValidados=$cuentaBancoController->ValidarDatos ($parametros['tipoDeCuenta'],$parametros['moneda']);
            if (gettype($datosValidados) != "string")
            {
                $cuentaBancoController-> SumarSaldo($request, $response, $args);
                $depositoController-> CargarUno($request, $response, $args);
            }else {
                $payload = json_encode(array("mensaje" => $datosValidados));
                $response->getBody()->write($payload);
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
            $response->getBody()->write($payload);
        }

        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ConsultarMovimientos($request, $response, $args)
    {
       // $parametros = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        $cuentaBancoController = new CuentaBancoController();
        $depositoController = new DepositoController();
        $retiroController= new RetiroController ();
        if (isset($queryParams['punto'])) 
        {
            switch($queryParams['punto'])
            {
                case "4a":
                    if(isset($queryParams['tipoDeCuenta'],$queryParams['moneda']))
                    {
                        $datosValidados=$cuentaBancoController->ValidarDatos ($queryParams['tipoDeCuenta'],$queryParams['moneda']);
                        if (gettype($datosValidados) != "string")
                        {
                            DepositoController::CantidadDeDepositos($request, $response, $args);
                        }else {
                            $payload = json_encode(array("mensaje" => $datosValidados));
                        }  
                    }else {
                        $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
                        $response->getBody()->write($payload);
                    }
                    break;
                    case "4b":
                        DepositoController::ListadoDepositosUsuario($request, $response, $args);
                        break;
                    case "4c":
                        DepositoController::ListadoEntreFechas($request, $response, $args);
                        break;
                    case "4d":
                        if(isset($queryParams['tipoDeCuenta'],$queryParams['moneda']))
                        {
                            $datosValidados=$cuentaBancoController->ValidarDatos ($queryParams['tipoDeCuenta'],$queryParams['moneda']);
                            if (gettype($datosValidados) != "string")
                            {
                                DepositoController::ListadoPorTipoDeCuenta($request, $response, $args);
                            }else {
                                $payload = json_encode(array("mensaje" => $datosValidados));
                            }  
                        }else {
                            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
                            $response->getBody()->write($payload);
                        }
                        break;
                    case "4e":
                        DepositoController::ListadoPorMoneda($request, $response, $args);
                        break;
                    case "10a":
                        if(isset($queryParams['tipoDeCuenta'],$queryParams['moneda']))
                        {
                            $datosValidados=$cuentaBancoController->ValidarDatos ($queryParams['tipoDeCuenta'],$queryParams['moneda']);
                            if (gettype($datosValidados) != "string")
                            {
                                RetiroController::CantidadDeRetiros($request, $response, $args);
                            }else {
                                $payload = json_encode(array("mensaje" => $datosValidados));
                            }  
                        }else {
                            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
                            $response->getBody()->write($payload);
                        }
                        break;
                    case "10b":
                        RetiroController::ListadoRetirosUsuario($request, $response, $args);
                        break;
                    case "10c":
                        RetiroController::ListadoEntreFechas($request, $response, $args);
                        break;
                    case "10d":
                        if(isset($queryParams['tipoDeCuenta'],$queryParams['moneda']))
                        {
                            $datosValidados=$cuentaBancoController->ValidarDatos ($queryParams['tipoDeCuenta'],$queryParams['moneda']);
                            if (gettype($datosValidados) != "string")
                            {
                                RetiroController::ListadoPorTipoDeCuenta($request, $response, $args);
                            }else {
                                $payload = json_encode(array("mensaje" => $datosValidados));
                            }  
                        }else {
                            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
                            $response->getBody()->write($payload);
                        }
                        break;
                    case "10e":
                        RetiroController::ListadoPorMoneda($request, $response, $args);
                        break;
                    case "10f":
                        $responseCuenta = clone $response;
                        $cuentaBancoController->TraerTodos($request, $responseCuenta, $args);
                            $listaDeCuentas = json_decode($responseCuenta->getBody());
                            if ($listaDeCuentas && isset($listaDeCuentas->listaCuentaBanco)) {

                                $arrayDeObjetos = [];
                                foreach ($listaDeCuentas->listaCuentaBanco as $cuenta) {
                                    $responseDepositos = clone $response;
                                    $responseRetiros = clone $response;
                                    $queryParams = $request->getQueryParams();
                                    $queryParams['usuario'] = $cuenta->numeroDocumento;

                                    DepositoController::ListadoDepositosUsuario($request->withQueryParams($queryParams), $responseDepositos, $args);
                                    $listaDeDepositos = json_decode($responseDepositos->getBody());

                                    RetiroController::ListadoRetirosUsuario($request->withQueryParams($queryParams), $responseRetiros, $args);
                                    $listaDeRetiros = json_decode($responseRetiros->getBody());
                        

                                    $nuevoObjeto = new stdClass();
                                    $nuevoObjeto->dni = $cuenta->numeroDocumento;
                                    $nuevoObjeto->cuenta = $cuenta->nroDeCuenta;
                                    $nuevoObjeto->listaDeDepositos = $listaDeDepositos;
                                    $nuevoObjeto->listaDeRetiros = $listaDeRetiros;

                                    $arrayDeObjetos[] = $nuevoObjeto;
                                }
                                $response->getBody()->write(json_encode($arrayDeObjetos));
                            } else {
                                $response->getBody()->write(json_encode(["mensaje" => "Error al obtener la lista de cuentas"]));
                            }
                        
                        break;
                    default:
                    $payload = json_encode(array("mensaje" => "No existe ese punto"));
                    $response->getBody()->write($payload);
                        break;
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
            $response->getBody()->write($payload);
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function ModificarCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $cuentaBancoController = new CuentaBancoController();
        $cuentaBancoController->ModificarUno($request, $response, $args);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function RetiroCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $cuentaBancoController = new CuentaBancoController();
        $retiroController = new RetiroController();
        if (isset($parametros['nroDeCuenta'], $parametros['moneda'], $parametros['tipoDeCuenta'])) {
            $datosValidados=$cuentaBancoController->ValidarDatos ($parametros['tipoDeCuenta'],$parametros['moneda']);
            if (gettype($datosValidados) != "string")
            {
                $cuentaBancoController-> RestarSaldo($request, $response, $args);
                $retiroController-> CargarUno($request, $response, $args);
            }else {
                $payload = json_encode(array("mensaje" => $datosValidados));
                $response->getBody()->write($payload);
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
            $response->getBody()->write($payload);
        }

        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function AjusteCuenta($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $cuentaBancoController = new CuentaBancoController();
        $depositoController = new DepositoController();
        $retiroController = new RetiroController();
        $ajusteController = new AjusteController();
    
        if (isset($parametros['tipoAAjustar'], $parametros['importe'])) {

            if ($parametros['tipoAAjustar'] == "retiro" || $parametros['tipoAAjustar'] == "deposito") {
                $tipoAjuste = $parametros['tipoAAjustar'];
                $controller = $tipoAjuste == "retiro" ? $retiroController : $depositoController;
        
                $responseTraerUno = clone $response;
                $controller->TraerUno($request, $responseTraerUno, $args);
                $operacion = json_decode($responseTraerUno->getBody());
                
                
                if ($operacion) {
                    $cuenta = CuentaBanco::obtenerObjetoPorId($operacion->idCuenta);
                    $nuevosParametros = $parametros;
                    $nuevosParametros["nroDeCuenta"] = $cuenta->nroDeCuenta;
                    $nuevosParametros["tipoDeCuenta"] = substr($cuenta->tipoDeCuenta, 0, 2);
                    $nuevosParametros["moneda"] =$cuenta->moneda;
                    if($operacion->importe + $parametros["importe"]>0)
                    {
                        $request = $request->withParsedBody($nuevosParametros);
                        $ajusteController->CargarUno($request, $response, $args);
                        $cuentaBancoController->SumarSaldo($request, $response, $args);
            
                        $nuevosParametros["importe"] = $operacion->importe + $parametros["importe"];

                        $request = $request->withParsedBody($nuevosParametros);
                        $controller->ModificarUno($request, $response, $args);
                    } else
                    {
                        $payload = json_encode(["mensaje" => "El monto es demasiado grande para ajustar"]);
                        $response->getBody()->write($payload);
                    }
                } else
                {
                    $payload = json_encode(["mensaje" => "La operación no existe"]);
                    $response->getBody()->write($payload);
                }
            } else {
                $payload = json_encode(["mensaje" => "El tipo de ajuste debe ser retiro o deposito"]);
                $response->getBody()->write($payload);
            }
        } else
        {
            $payload = json_encode(["mensaje" => "Faltan ingresar parámetros"]);
            $response->getBody()->write($payload);
        }
 
        return $response->withHeader('Content-Type', 'application/json');
    }
    

    public function BorrarCuenta($request, $response, $args)
    {
        $cuentaBancoController = new CuentaBancoController();
        $cuentaBancoController->BorrarUno($request, $response, $args);
        return $response->withHeader('Content-Type', 'application/json');

    }

    
}
?>

