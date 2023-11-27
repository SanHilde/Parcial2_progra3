<?php
require_once './models/Deposito.php';
require_once './models/Archivos.php';
require_once './interfaces/IApiUsable.php';
require_once './models/CuentaBanco.php';

class DepositoController extends Deposito implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();

        if (isset($parametros['importe'], $archivo['imagen'])) {

            $tipo = $parametros['tipoDeCuenta'].$parametros['moneda'];
            $cuentaBanco = CuentaBanco::obtenerObjeto($parametros['nroDeCuenta'], $tipo);
            if($cuentaBanco)
            {
                $deposito = new Deposito();
                $deposito->idCuenta = $cuentaBanco->id;
                $deposito->fecha = date("Y-m-d H:i:s");
                $deposito->importe = $parametros['importe'];
                $id = Deposito::obtenerUltimoId()+1;
                $deposito->imagenTalonario = $parametros['tipoDeCuenta'].$parametros['moneda'].$parametros['nroDeCuenta'].$id; 
    
                if ($deposito->crearObjeto()) {
                    if (Archivos::SubirImagen($archivo['imagen'], $deposito->imagenTalonario, "./ImagenesDeDepositos2023")) {
                        $payload = json_encode(array("mensaje" => "Depósito creado con éxito"));
                    } else {
                        $payload = json_encode(array("mensaje" => "Error al subir la imagen del talonario"));
                    }
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
            $deposito = Deposito::obtenerObjeto($parametros['id']);
            $payload = json_encode($deposito);
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
        $lista = Deposito::obtenerTodos();
        $payload = json_encode(array("listaDepositos" => $lista));

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
                $deposito = Deposito::obtenerObjeto($parametros['id']);
                
                if ($deposito) {
                    // $cuenta = CuentaBanco::obtenerObjeto($deposito->idCuenta);
                    if ($fechaSQL!="sin fecha")
                    {
                        $retiro->fecha = $fechaSQL ?? $retiro->fecha;
                    }
                    $deposito->importe = $parametros['importe'] ?? $deposito->importe;      
                    // if(isset($archivo['imagen']))
                    // {
                    //     Archivos::ReemplazarArchivo($deposito->imagenTalonario, $archivo['fotoPerfil'], "./ImagenesDeDepositos2023.", ".jpeg");
                    // }
                    // $deposito->imagenTalonario = $cuenta->tipoDeCuenta.$cuenta->nroDeCuenta.$deposito->id??$deposito->imgenTalonario;
                    if ($deposito->modificarObjeto($parametros['id'])) {
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
            $idDeposito = $parametros['id'];
            $deposito = Deposito::obtenerObjeto($idDeposito);

            if ($deposito) {
                if(Deposito::borrarObjeto($idDeposito))
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


    public static function CantidadDeDepositos($request, $response, $args)
    {  
        $contador = 0;
        $lista = Deposito::obtenerTodos();
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
                foreach ($lista as $deposito)
                {
                    $cuentaBanco = CuentaBanco::obtenerObjetoPorId($deposito->idCuenta);
                    $fechaSeteada=new DateTime($deposito->fecha);     
                    $diferencia = $fechaString->diff($fechaSeteada);
                    if($diferencia->days === 0  && $cuentaBanco->tipoDeCuenta==$tipo.$moneda)
                    {
                        $contador = $contador + $deposito->importe;
                    }
                }
                $fechaStringTexto = $fechaString->format('Y-m-d H:i:s');
                $payload = json_encode(array("mensaje" =>  "La cantidad de depositos para el tipo de cuenta '$tipo', en la moneda '$moneda', en la fecha '$fechaStringTexto', es de: $contador")); 
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
        $listaDeDepositos = Deposito::obtenerTodos();
        $queryParams = $request->getQueryParams();
        if(isset($queryParams["tipoDeCuenta"],$queryParams["moneda"]))
        {
            $array = array();
            foreach ($listaDeDepositos as $deposito)
            {
                $cuenta = CuentaBanco::obtenerObjetoPorId($deposito->idCuenta);
                 if ($cuenta->tipoDeCuenta==$queryParams["tipoDeCuenta"].$queryParams["moneda"]) 
                {
                    array_push($array,$deposito);
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
        $listaDeDepositos = Deposito::obtenerTodos();
        $queryParams = $request->getQueryParams();
        if($queryParams["moneda"])
        {
            $array = array();
            foreach ($listaDeDepositos as $deposito)
            {
                $cuenta = CuentaBanco::obtenerObjetoPorId($deposito->idCuenta);
                 if ($cuenta->moneda==$queryParams["moneda"]) 
                {
                    array_push($array,$deposito);
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
    public static function ListadoDepositosUsuario($request, $response, $args)
    {
        $array=array();
        $lista = Deposito::obtenerTodos();
        $queryParams = $request->getQueryParams();
        // var_dump($queryParams);
        if(isset($queryParams["usuario"]))
        {
            foreach ($lista as $deposito)
            {
                $cuentaBanco = CuentaBanco::obtenerObjetoPorId($deposito->idCuenta);
                if ($cuentaBanco->numeroDocumento==$queryParams["usuario"])
                {
                    array_push($array,$deposito);
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
    $listaDeDepositos = Deposito::obtenerTodos();
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

        foreach ($listaDeDepositos as $deposito) {
            $fechaSeteada = new DateTime($deposito->fecha);
            $fechaSeteada->setTime(0, 0, 0);
            if ($fechaSeteada >= $fechaUnoConvertida && $fechaSeteada <= $fechaDosConvertida) {
                array_push($array, $deposito);
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





    public static function CompararPorNombre($deposito1, $deposito2) 
    {
        $cuenta1 = CuentaBanco::obtenerObjetoPorId($deposito1->idCuenta);
        $cuenta2 = CuentaBanco::obtenerObjetoPorId($deposito2->idCuenta);
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