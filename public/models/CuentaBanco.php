<?php

class CuentaBanco
{   
    public $nombre;
    public $apellido;
    public $tipoDocumento;
    public $numeroDocumento;
    public $email;
    public $tipoDeCuenta;
    public $moneda;
    public $saldoInicial;
    public $nroDeCuenta;
    public $fotoDePerfil;
    public $activo;

    public function crearObjeto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO cuentaBanco (nombre, apellido, tipoDocumento, numeroDocumento, email, tipoDeCuenta, moneda, saldoInicial, nroDeCuenta, fotoDePerfil, activo) VALUES (:nombre, :apellido, :tipoDocumento, :numeroDocumento, :email, :tipoDeCuenta, :moneda, :saldoInicial, :nroDeCuenta, :fotoDePerfil, :activo)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDocumento', $this->tipoDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDocumento', $this->numeroDocumento, PDO::PARAM_INT);
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeCuenta', $this->tipoDeCuenta, PDO::PARAM_STR);
        $consulta->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
        $consulta->bindValue(':saldoInicial', $this->saldoInicial, PDO::PARAM_INT);
        $consulta->bindValue(':nroDeCuenta', $this->nroDeCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':fotoDePerfil', $this->fotoDePerfil, PDO::PARAM_STR);
        $consulta->bindValue(':activo', $this->activo, PDO::PARAM_INT);
        
        return $consulta->execute();
    }
    public static function obtenerObjeto($nroDeCuenta, $tipoDeCuenta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM cuentaBanco WHERE nroDeCuenta = :nroDeCuenta AND tipoDeCuenta = :tipoDeCuenta");
        $consulta->bindValue(':nroDeCuenta', $nroDeCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':tipoDeCuenta', $tipoDeCuenta, PDO::PARAM_STR);
        $consulta->execute();
    
        return $consulta->fetchObject('CuentaBanco');
    }
    
    public static function obtenerObjetoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM cuentaBanco WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('CuentaBanco');
    }


    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre, apellido, tipoDocumento, numeroDocumento, email, tipoDeCuenta, moneda, saldoInicial, nroDeCuenta, fotoDePerfil, activo FROM cuentaBanco WHERE activo = 1");
        $consulta->execute();
    
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'CuentaBanco');
    }

    public function modificarObjeto($nroDeCuenta, $tipoDeCuenta)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE cuentaBanco 
        SET nombre = :nombre, apellido = :apellido, tipoDocumento = :tipoDocumento, 
            numeroDocumento = :numeroDocumento, email = :email, saldoInicial = :saldoInicial, fotoDePerfil = :fotoDePerfil 
        WHERE nroDeCuenta = :nroDeCuenta AND tipoDeCuenta = :tipoDeCuenta");
    
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDocumento', $this->tipoDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDocumento', $this->numeroDocumento, PDO::PARAM_INT);
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeCuenta', $tipoDeCuenta, PDO::PARAM_STR);
        //$consulta->bindValue(':moneda', $this->moneda, PDO::PARAM_STR);
        $consulta->bindValue(':saldoInicial', $this->saldoInicial, PDO::PARAM_INT);
        $consulta->bindValue(':nroDeCuenta', $nroDeCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':fotoDePerfil', $this->fotoDePerfil, PDO::PARAM_STR);
        
        return $consulta->execute();
    
    }
    public static function borrarObjeto($nroDeCuenta, $tipoDeCuenta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE cuentaBanco SET activo = 0 WHERE nroDeCuenta = :nroDeCuenta AND tipoDeCuenta = :tipoDeCuenta");
        $consulta->bindValue(':nroDeCuenta', $nroDeCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':tipoDeCuenta', $tipoDeCuenta, PDO::PARAM_STR);
    
        return ($consulta->execute());
    }
    
}
