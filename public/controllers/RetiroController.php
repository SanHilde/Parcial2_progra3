<?php
require_once './models/Retiro.php';
require_once './models/Archivos.php';
require_once './interfaces/IApiUsable.php';
require_once './models/CuentaBanco.php';

class RetiroController extends Retiro implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();

        if (isset($parametros['importe']))//, $archivo['imagen'])) 
        {

            $tipo = $parametros['tipoDeCuenta'].$parametros['moneda'];
            $cuentaBanco = CuentaBanco::obtenerObjeto($parametros['nroDeCuenta'], $tipo);
            if($cuentaBanco)
            {
                $retiro = new Retiro();
                $retiro->idCuenta = $cuentaBanco->id;
                $retiro->fecha = date("Y-m-d H:i:s");
                $retiro->importe = $parametros['importe'];
                $id = Retiro::obtenerUltimoId()+1;
                // $retiro->imagenTalonario = $parametros['tipoDeCuenta'].$parametros['moneda'].$parametros['nroDeCuenta'].$id; 
    
                if ($retiro->crearObjeto()) {
                    // if (Archivos::SubirImagen($archivo['imagen'], $retiro->imagenTalonario, "./ImagenesDeRetiros2023")) {
                        $payload = json_encode(array("mensaje" => "Depósito creado con éxito"));
                    // } else {
                        // $payload = json_encode(array("mensaje" => "Error al subir la imagen del talonario"));
                    // }
                } else {
                    $payload = json_encode(array("mensaje" => "Error al crear el depósito"));
                }
            } else
            {
                $payload = json_encode(array("mensaje" => "Cuenta inexistente"));
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
        
        if (isset($parametros['id'])) 
        {
            $retiro = Retiro::obtenerObjeto($parametros['id']);
            $payload = json_encode($retiro);
        } 
        else
        {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
        $lista = Retiro::obtenerTodos();
        $payload = json_encode(array("listaRetiros" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivo=$request->getUploadedFiles(); 
    
        if (isset($parametros['id'])) {
            if(isset($parametros['fecha']))
            {
                $fechaSQL = $this->transformarFechaSQL($parametros['fecha']);
            } else
            {
                $fechaSQL="sin fecha";
            }
    
            if ($fechaSQL !== null || $fechaSQL=="sin fecha") {
                $retiro = Retiro::obtenerObjeto($parametros['id']);
                if ($retiro) {
                    // $cuenta = CuentaBanco::obtenerObjeto($retiro->idCuenta);
                    if ($fechaSQL!="sin fecha")
                    {
                        $retiro->fecha = $fechaSQL ?? $retiro->fecha;
                    }
                    $retiro->importe = $parametros['importe'] ?? $retiro->importe;      
                    // if(isset($archivo['imagen']))
                    // {
                    //     Archivos::ReemplazarArchivo($retiro->imagenTalonario, $archivo['fotoPerfil'], "./ImagenesDeretiros2023.", ".jpeg");
                    // }
                    // $retiro->imagenTalonario = $cuenta->tipoDeCuenta.$cuenta->nroDeCuenta.$retiro->id??$retiro->imgenTalonario;
                    if ($retiro->modificarObjeto($parametros['id'])) {
                        $payload = json_encode(array("mensaje" => "Depósito modificado con éxito"));
                    } else {
                        $payload = json_encode(array("mensaje" => "Error al modificar el depósito"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Depósito no encontrado"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "Error al transformar la fecha, debe esta escrita en formato DD-MM-AAAA"));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        if(isset($parametros['id']))
        {
            $idRetiro = $parametros['id'];
            $retiro = Retiro::obtenerObjeto($idRetiro);

            if ($retiro) {
                if(Retiro::borrarObjeto($idRetiro))
                {
                    $payload = json_encode(array("mensaje" => "Depósito borrado con éxito"));
                } else {
                    $payload = json_encode(array("mensaje" => "Error al borrar el depósito"));
                }
            } else {
                $payload = json_encode(array("mensaje" => "Depósito no encontrado"));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Falta ingresar ID"));
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public static function CantidadDeRetiros($request, $response, $args)
    {  
        $contador = 0;
        $lista = Retiro::obtenerTodos();
        $queryParams = $request->getQueryParams();
        if(isset($queryParams["moneda"],$queryParams["tipoDeCuenta"]))
        {
            $tipo = strtoupper($queryParams["tipoDeCuenta"]);
            $moneda = strtoupper($queryParams["moneda"]);            
            if(isset($queryParams["fecha"]))
            {
                $fechaString = self::ConvertirStringAFecha($queryParams["fecha"]);
            } else
            {
                $fechaActual =  date("Y-m-d H:i:s");
                $fechaString=date("Y-m-d H:i:s", strtotime($fechaActual . " -1 day"));
            }
            
            if ($fechaString != null)
            {
                foreach ($lista as $retiro)
                {
                    $cuentaBanco = CuentaBanco::obtenerObjetoPorId($retiro->idCuenta);
                    $fechaSeteada=new DateTime($retiro->fecha);     
                    $diferencia = $fechaString->diff($fechaSeteada);
                    if($diferencia->days === 0  && $cuentaBanco->tipoDeCuenta==$tipo.$moneda)
                    {
                        $contador = $contador + $retiro->importe;
                    }
                }
                $fechaStringTexto = $fechaString->format('Y-m-d H:i:s');
                $payload = json_encode(array("mensaje" =>  "La cantidad de retiros para el tipo de cuenta '$tipo', en la moneda '$moneda', en la fecha '$fechaStringTexto', es de: $contador")); 
            } else
            {
                $payload = json_encode(array("mensaje" => "La fecha no existe. Debe ingresar la fecha en un formato dd-mm-aaaa.")); 
            }
        } else
        {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros")); 
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ListadoPorTipoDeCuenta($request, $response, $args)
    {
        $listaDeRetiro = Retiro::obtenerTodos();
        $queryParams = $request->getQueryParams();
        if(isset($queryParams["tipoDeCuenta"],$queryParams["moneda"]))
        {
            $array = array();
            foreach ($listaDeRetiro as $retiro)
            {
                $cuenta = CuentaBanco::obtenerObjetoPorId($retiro->idCuenta);
                 if ($cuenta->tipoDeCuenta==$queryParams["tipoDeCuenta"].$queryParams["moneda"]) 
                {
                    array_push($array,$retiro);
                }
            }
            $payload= json_encode($array);
        }else
        {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros")); 
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
       
    }
    public static function ListadoPorMoneda($request, $response, $args)
    {
        $listaDeRetiros = Retiro::obtenerTodos();
        $queryParams = $request->getQueryParams();
        if($queryParams["moneda"])
        {
            $array = array();
            foreach ($listaDeRetiros as $retiro)
            {
                $cuenta = CuentaBanco::obtenerObjetoPorId($retiro->idCuenta);
                 if ($cuenta->moneda==$queryParams["moneda"]) 
                {
                    array_push($array,$retiro);
                }
            }
            $payload= json_encode($array);
        }else
        {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros")); 
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
       
    }
    public static function ListadoRetirosUsuario($request, $response, $args)
    {
        $array=array();
        $lista = Retiro::obtenerTodos();
        $queryParams = $request->getQueryParams();
        if(isset($queryParams["usuario"]))
        {
            foreach ($lista as $retiro)
            {
                $cuentaBanco = CuentaBanco::obtenerObjetoPorId($retiro->idCuenta);
                if ($cuentaBanco->numeroDocumento==$queryParams["usuario"])
                {
                    array_push($array,$retiro);
                }
            }
            $payload= json_encode($array);
        }else
        {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros")); 
        }
       
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function ListadoEntreFechas($request, $response, $args)
    {
        $listaDeRetiros = Retiro::obtenerTodos();
        $queryParams = $request->getQueryParams();
        
        if (isset($queryParams["fechaUno"], $queryParams["fechaDos"])) {
            $array = [];
            $fechaUnoConvertida = self::ConvertirStringAFecha($queryParams["fechaUno"]);
            $fechaDosConvertida = self::ConvertirStringAFecha($queryParams["fechaDos"]);

            // Aseguramos que $fechaUnoConvertida sea menor o igual a $fechaDosConvertida
            if ($fechaUnoConvertida > $fechaDosConvertida) {
                $temp = $fechaUnoConvertida;
                $fechaUnoConvertida = $fechaDosConvertida;
                $fechaDosConvertida = $temp;
            }

            foreach ($listaDeRetiros as $retiro) {
                $fechaSeteada = new DateTime($retiro->fecha);
                $fechaSeteada->setTime(0, 0, 0);
                if ($fechaSeteada >= $fechaUnoConvertida && $fechaSeteada <= $fechaDosConvertida) {
                    array_push($array, $retiro);
                }
            }

            usort($array, array(self::class, 'CompararPorNombre'));

            $payload = json_encode($array);
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }





    public static function CompararPorNombre($retiro1, $retiro2) 
    {
        $cuenta1 = CuentaBanco::obtenerObjetoPorId($retiro1->idCuenta);
        $cuenta2 = CuentaBanco::obtenerObjetoPorId($retiro2->idCuenta);
        if ($cuenta1->nombre == $cuenta2->nombre) {
            return 0;
        }
        return ($cuenta1->nombre< $cuenta2->nombre) ? -1 : 1;
    }


    public function transformarFechaSQL($fecha)
    {
        $fechaObjeto = DateTime::createFromFormat('d-m-Y', $fecha);

        if ($fechaObjeto) {
            $fechaSQL = $fechaObjeto->format('Y-m-d');
            return $fechaSQL;
        } else {
            return null;
        }
    }
    private static function ConvertirStringAFecha($string)
    {
        $partes_fecha = explode("-", $string);
        $fechaRegistro = new DateTime();
        $fechaRegistro->setTimezone(new DateTimeZone('UTC'));
        $fechaRegistro->setDate($partes_fecha[2], $partes_fecha[1], $partes_fecha[0]);
        $fechaRegistro->setTime(0, 0, 0);
    
        if ($partes_fecha[0] > 31 || $partes_fecha[1] > 12) {
            $fechaRegistro = null;
        }
    
        return $fechaRegistro;
    }
    

}