<?php

class Retiro
{
    public $id;
    public $idCuenta;
    public $fecha;
    public $importe;

    
    public function crearObjeto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO retiro (idCuenta, fecha, importe) 
            VALUES (:idCuenta, :fecha, :importe)");
        
        $consulta->bindValue(':idCuenta', $this->idCuenta, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);
        
        return $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idCuenta, fecha, importe FROM retiro");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Retiro');
    }

    public static function obtenerObjeto($idRetiro)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM retiro WHERE id = :idRetiro");
        $consulta->bindValue(':idRetiro', $idRetiro, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Retiro');
    }

    public function modificarObjeto($idRetiro)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE retiro 
            SET fecha = :fecha, importe = :importe
            WHERE id = :idRetiro");

        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);  // Asumo que fecha es un string, ajusta según tu necesidad
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_STR);
        $consulta->bindValue(':idRetiro', $idRetiro, PDO::PARAM_INT);

        return $consulta->execute();
    }

    public static function borrarObjeto($idRetiro)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM retiro WHERE id = :idRetiro");
        $consulta->bindValue(':idRetiro', $idRetiro, PDO::PARAM_INT);

        return $consulta->execute();
    }
    public static function obtenerUltimoId()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT MAX(id) as ultimo_id FROM retiro");
        $consulta->execute();
    
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
    
        if ($resultado) {
            return $resultado['ultimo_id'];
        } else {
            return null;
        }
    }
    
}
