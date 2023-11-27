<?php

class Deposito
{
    public $id;
    public $idCuenta;
    public $fecha;
    public $importe;
    public $imagenTalonario;


    public function crearObjeto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO deposito (idCuenta, fecha, importe, imagenTalonario) 
            VALUES (:idCuenta, :fecha, :importe, :imagenTalonario)");
        
        $consulta->bindValue(':idCuenta', $this->idCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);
        $consulta->bindValue(':imagenTalonario', $this->imagenTalonario, PDO::PARAM_STR);
        
        return $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idCuenta, fecha, importe, imagenTalonario FROM deposito");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Deposito');
    }

    public static function obtenerObjeto($idDeposito)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM deposito WHERE id = :idDeposito");
        $consulta->bindValue(':idDeposito', $idDeposito, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Deposito');
    }

    public function modificarObjeto($idDeposito)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE deposito 
            SET fecha = :fecha, importe = :importe, imagenTalonario = :imagenTalonario 
            WHERE id = :idDeposito");

        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);  // Asumo que fecha es un string, ajusta según tu necesidad
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);
        $consulta->bindValue(':imagenTalonario', $this->imagenTalonario, PDO::PARAM_STR);
        $consulta->bindValue(':idDeposito', $idDeposito, PDO::PARAM_INT);

        return $consulta->execute();
    }

    public static function borrarObjeto($idDeposito)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM deposito WHERE id = :idDeposito");
        $consulta->bindValue(':idDeposito', $idDeposito, PDO::PARAM_INT);

        return $consulta->execute();
    }
    public static function obtenerUltimoId()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(id) as ultimo_id FROM deposito");
        $consulta->execute();
    
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        if ($resultado) {
            return $resultado['ultimo_id'];
        } else {
            return null;
        }
    }
    
}
