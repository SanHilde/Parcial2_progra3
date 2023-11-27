<?php
require_once './models/Ajuste.php';


class AjusteController extends Ajuste implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if (isset($parametros['importe'], $parametros['motivo'], $parametros['tipoAAjustar'], $parametros['id'])) {
            $ajuste = new Ajuste();
            $ajuste->motivo = $parametros['motivo'];
            $ajuste->idDeAjuste = $parametros['id'];
            $ajuste->operacionAAjustar = $parametros['tipoAAjustar'];
            $ajuste->monto = $parametros['importe'];

            if ($ajuste->crearObjeto()) {
                $payload = json_encode(array("mensaje" => "Ajuste creado con éxito"));
            } else {
                $payload = json_encode(array("mensaje" => "Error al crear el ajuste"));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();       
        $payload = json_encode(array("mensaje" => "Metodo incompleto"));  
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function TraerTodos($request, $response, $args)
    {
        $parametros = $request->getParsedBody();       
        $payload = json_encode(array("mensaje" => "Metodo incompleto"));  
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();       
        $payload = json_encode(array("mensaje" => "Metodo incompleto"));  
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();       
        $payload = json_encode(array("mensaje" => "Metodo incompleto"));  
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}