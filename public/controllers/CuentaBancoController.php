<?php
require_once './models/CuentaBanco.php';
require_once './models/Archivos.php';
require_once './interfaces/IApiUsable.php';

class CuentaBancoController extends CuentaBanco implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivo=$request->getUploadedFiles();     
        if (isset($parametros['nombre'], $parametros['apellido'], $parametros['tipoDocumento'],$parametros['numeroDocumento'], $parametros['email'], $parametros['tipoDeCuenta'],$parametros['moneda'],$archivo['fotoPerfil']))
        {
            $validarDatos = self::ValidarDatos ($parametros['tipoDeCuenta'], $parametros['moneda']);
            if (gettype($validarDatos) != "string")
            {
                $cuentaBanco = new CuentaBanco();
                $cuentaBanco->tipoDeCuenta = $parametros['tipoDeCuenta'].$parametros['moneda'];
                do {
                    $nroDeCuenta = self::GenerarNumeroRandom();
                } while (CuentaBanco::obtenerObjeto($nroDeCuenta, $cuentaBanco->tipoDeCuenta));
                $cuentaBanco->nroDeCuenta = $nroDeCuenta;
                $cuentaBanco->nombre = $parametros['nombre'];
                $cuentaBanco->apellido = $parametros['apellido'];
                $cuentaBanco->tipoDocumento = $parametros['tipoDocumento'];
                $cuentaBanco->numeroDocumento = $parametros['numeroDocumento'];
                $cuentaBanco->email = $parametros['email'];
                $cuentaBanco->moneda = $parametros['moneda'];
                if( isset($parametros['saldoInicial']))
                {
                    $cuentaBanco->saldoInicial = $parametros['saldoInicial'];                
                }else
                {
                    $cuentaBanco->saldoInicial = 0;                
                }
                $cuentaBanco->nroDeCuenta = $nroDeCuenta;
                $cuentaBanco->fotoDePerfil = $cuentaBanco->nroDeCuenta.$cuentaBanco->tipoDeCuenta;
                $cuentaBanco->activo = 1;

                if ($cuentaBanco->crearObjeto()) 
                {   
                    //if(true)  
                    if(Archivos::SubirImagen ($archivo['fotoPerfil'],$cuentaBanco->fotoDePerfil,"./ImagenesDeCuentas/2023"))
                    {
                        $payload = json_encode(array("mensaje" => "Cuenta de banco creada con éxito"));
                    } else
                    {
                        $payload = json_encode(array("mensaje" => "Error al crear la foto de perfil"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Error al crear la cuenta"));
                }
            } else
            {
                if($validarDatos!=false)
                {
                    $payload = json_encode(array("mensaje" => $validarDatos));
                } else
                {
                    $payload = json_encode(array("mensaje" => "Error al validar el tipo de cuenta y la moneda"));
                }
                
            }
           
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
  

    public function TraerUno($request, $response, $args)
    {
        // Buscamos CuentaBanco por nombre
        $parametros = $request->getParsedBody();
        if (isset($parametros['nroDeCuenta'], $parametros['moneda'], $parametros['tipoDeCuenta'])) 
        {
            $cuentaBanco = CuentaBanco::obtenerObjeto($parametros['nroDeCuenta'], $parametros['tipoDeCuenta'].$parametros['moneda']);
            $payload = json_encode($cuentaBanco);
        } else
        {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = CuentaBanco::obtenerTodos();
        $payload = json_encode(array("listaCuentaBanco" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivo=$request->getUploadedFiles();
        if (isset($parametros['nroDeCuenta'], $parametros['moneda'], $parametros['tipoDeCuenta'])) {
            $datosValidados=self::ValidarDatos ($parametros['tipoDeCuenta'],$parametros['moneda']);
            if (gettype($datosValidados) != "string")
            {
                $tipo = $parametros['tipoDeCuenta'].$parametros['moneda'];
                $cuentaBanco = CuentaBanco::obtenerObjeto($parametros['nroDeCuenta'], $tipo);
        
                if ($cuentaBanco) {
                    $cuentaBanco->nombre = $parametros['nombre'] ?? $cuentaBanco->nombre;
                    $cuentaBanco->apellido = $parametros['apellido'] ?? $cuentaBanco->apellido;
                    $cuentaBanco->tipoDocumento = $parametros['tipoDocumento'] ?? $cuentaBanco->tipoDocumento;
                    $cuentaBanco->numeroDocumento = $parametros['numeroDocumento'] ?? $cuentaBanco->numeroDocumento;
                    $cuentaBanco->email = $parametros['email'] ?? $cuentaBanco->email;
                    $cuentaBanco->saldoInicial = $parametros['saldoInicial'] ?? $cuentaBanco->saldoInicial;
                    // $cuentaBanco->tipoDeCuenta = $parametros['tipoDeCuenta'] ?? $cuentaBanco->tipoDeCuenta;
                    // $cuentaBanco->moneda = $parametros['moneda'] ?? $cuentaBanco->moneda;
                    //$cuentaBanco->nroDeCuenta = $parametros['nroDeCuenta'] ?? $cuentaBanco->nroDeCuenta; NO MODIFICO ESTOS TRES PARAMETROS
                    $cuentaBanco->fotoDePerfil =  $cuentaBanco->nroDeCuenta. $cuentaBanco->tipoDeCuenta;
                    if(isset($archivo['fotoPerfil']))
                    {
                        Archivos::ReemplazarArchivo($cuentaBanco->fotoDePerfil, $archivo['fotoPerfil'], "./ImagenesDeCuentas/2023", ".jpeg");
                    }
                    if($cuentaBanco->modificarObjeto($parametros['nroDeCuenta'], $tipo))
                    {
                        $payload = json_encode(array("mensaje" => "Cuenta de banco modificada con éxito"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Cuenta de banco no encontrada"));
                }
            }else
            {
                $payload = json_encode(array("mensaje" => $datosValidados));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function SumarSaldo($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivo=$request->getUploadedFiles();  
        if (isset($parametros['nroDeCuenta'], $parametros['moneda'], $parametros['tipoDeCuenta'], $parametros['importe'])) {
            $datosValidados=self::ValidarDatos ($parametros['tipoDeCuenta'],$parametros['moneda']);
            if (gettype($datosValidados) != "string")
            {
                $tipo = $parametros['tipoDeCuenta'].$parametros['moneda'];
                $cuentaBanco = CuentaBanco::obtenerObjeto($parametros['nroDeCuenta'], $tipo);
        
                if ($cuentaBanco) {
                    $cuentaBanco->saldoInicial = $cuentaBanco->saldoInicial+$parametros['importe'];
                    if($cuentaBanco->modificarObjeto($parametros['nroDeCuenta'], $tipo))
                    {
                        $payload = json_encode(array("mensaje" => "Saldo modificado con éxito"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Cuenta de banco no encontrada"));
                }
            }else
            {
                $payload = json_encode(array("mensaje" => $datosValidados));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function RestarSaldo($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $archivo=$request->getUploadedFiles(); 
        if (isset($parametros['nroDeCuenta'], $parametros['moneda'], $parametros['tipoDeCuenta'], $parametros['importe'])) {
            $datosValidados=self::ValidarDatos ($parametros['tipoDeCuenta'],$parametros['moneda']);
            if (gettype($datosValidados) != "string")
            {
                $tipo = $parametros['tipoDeCuenta'].$parametros['moneda'];
                $cuentaBanco = CuentaBanco::obtenerObjeto($parametros['nroDeCuenta'], $tipo);
        
                if ($cuentaBanco) {
                    if($cuentaBanco->saldoInicial-$parametros['importe']>0)
                    {
                        $cuentaBanco->saldoInicial = $cuentaBanco->saldoInicial-$parametros['importe'];
                        if($cuentaBanco->modificarObjeto($parametros['nroDeCuenta'], $tipo))
                        {
                            $payload = json_encode(array("mensaje" => "Saldo modificado con éxito"));
                        }
                    } else
                    {
                        $payload = json_encode(array("mensaje" => "El saldo es insuficiente para hacer el descuento"));
                    }         
                } else {
                    $payload = json_encode(array("mensaje" => "Cuenta de banco no encontrada"));
                }
            }else
            {
                $payload = json_encode(array("mensaje" => $datosValidados));
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
        $archivo=$request->getUploadedFiles();
        if (isset($parametros['nroDeCuenta'], $parametros['moneda'], $parametros['tipoDeCuenta'])) {
            $datosValidados=self::ValidarDatos ($parametros['tipoDeCuenta'],$parametros['moneda']);
            if (gettype($datosValidados) != "string")
            {
                $tipo = $parametros['tipoDeCuenta'].$parametros['moneda'];
                $cuentaBanco = CuentaBanco::obtenerObjeto($parametros['nroDeCuenta'], $tipo);
        
                if ($cuentaBanco) {
                    if(Archivos:: MoverArchivo ($cuentaBanco->fotoDePerfil, "./ImagenesBackupCuentas/2023", "./ImagenesDeCuentas/2023"))
                    {
                        if($cuentaBanco->borrarObjeto($parametros['nroDeCuenta'], $tipo))
                        {
                            $payload = json_encode(array("mensaje" => "Cuenta de banco modificada con éxito"));
                        }
                    } else
                    {
                        $payload = json_encode(array("mensaje" => "Foto de perfil inexistente"));
                    }
                } else {
                    $payload = json_encode(array("mensaje" => "Cuenta de banco no encontrada"));
                }
            }else
            {
                $payload = json_encode(array("mensaje" => $datosValidados));
            }
        } else {
            $payload = json_encode(array("mensaje" => "Faltan ingresar parámetros"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    




    public function ExisteCuenta($tipo,$nroDeCuenta)
    {
        
        $respuesta = null;
        $bandera = 0;
        $array=array();
        $listaDeCuentas=CuentaBanco::obtenerTodos();

        if (count($listaDeCuentas)>0)
        {
            foreach ($listaDeCuentas as $cuenta)
            {
                if ($cuenta->nroDeCuenta==$nroDeCuenta)
                {
                    $tipoUno=substr($cuenta->tipoDeCuenta, 0, 2);
                    $tipoDos=substr($tipo, 0, 2);
                    if($tipoUno==$tipoDos)
                    {               
                        array_push($array,$cuenta);
                    } else
                    {
                        $respuesta= "Tipo de cuenta incorrecto";
                    }
                } 
            }
        }
        if ($respuesta==NULL)
        {
            $respuesta = $array;
        }
        return $respuesta;
    }

    private function GenerarNumeroRandom() 
    {
        $numeroAleatorio = rand(0, 999999);
        $numeroFormateado = sprintf('%06d', $numeroAleatorio);
        return $numeroFormateado;
    }

    public function ValidarDatos ($tipoDeCuenta,$moneda)
    {
        $respuesta = false;
        if (strcasecmp($tipoDeCuenta, "CA")==0 || strcasecmp($tipoDeCuenta, "CC") == 0)
        {
            if(strcasecmp($moneda, "$")==0 || strcasecmp($moneda, 'U$S') == 0)
            {              
                $respuesta= true;
            } else
            {
                $respuesta= 'La moneda no existe, debe ser $ o U$S.';
            }
            
        } else
        {
            $respuesta= "El tipo de cuenta no existe, debe ser CA (Caja de Ahorro) o CC (Cuenta Corriente).";
        }
        return $respuesta;
    }

}