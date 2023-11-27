<?php

class Ajuste
{
    public $motivo;
    public $idDeAjuste;
    public $operacionAAjustar;
    public $monto;
    public $id;

    public function crearObjeto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ajuste (motivo, idDeAjuste, operacionAAjustar, monto) 
            VALUES (:motivo, :idDeAjuste, :operacionAAjustar, :monto)");
        
        $consulta->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
        $consulta->bindValue(':idDeAjuste', $this->idDeAjuste, PDO::PARAM_INT);
        $consulta->bindValue(':operacionAAjustar', $this->operacionAAjustar, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_STR);
        
        return $consulta->execute();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, motivo, idDeAjuste, operacionAAjustar, monto FROM ajuste");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Ajuste');
    }

    public static function obtenerObjeto($idAjuste)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ajuste WHERE id = :idAjuste");
        $consulta->bindValue(':idAjuste', $idAjuste, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Ajuste');
    }

    public function modificarObjeto($idAjuste)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ajuste 
            SET motivo = :motivo, operacionAAjustar = :operacionAAjustar, monto = :monto 
            WHERE id = :idAjuste");

        $consulta->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
        $consulta->bindValue(':operacionAAjustar', $this->operacionAAjustar, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_STR);
        $consulta->bindValue(':idAjuste', $idAjuste, PDO::PARAM_INT);

        return $consulta->execute();
    }

    public static function borrarObjeto($idAjuste)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM ajuste WHERE id = :idAjuste");
        $consulta->bindValue(':idAjuste', $idAjuste, PDO::PARAM_INT);

        return $consulta->execute();
    }

}
