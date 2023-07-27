<?php

class Inventario {
    public $id;
    public $productoId;
    public $clasificacionId;
    public $cantidad;
    public $fechaVencimiento;
    public $numeroLote;
    public $proveedorId;
    public $farmaceutaId;
    public $usuarioId;
    public $fechaIngreso;
    public $numeroFactura;

    public function __construct($id, $productoId, $clasificacionId, $cantidad, $fechaVencimiento, $numeroLote, $proveedorId, $farmaceutaId, $usuarioId, $fechaIngreso, $numeroFactura) {
        $this->id = $id;
        $this->productoId = $productoId;
        $this->clasificacionId = $clasificacionId;
        $this->cantidad = $cantidad;
        $this->fechaVencimiento = $fechaVencimiento;
        $this->numeroLote = $numeroLote;
        $this->proveedorId = $proveedorId;
        $this->farmaceutaId = $farmaceutaId;
        $this->usuarioId = $usuarioId;
        $this->fechaIngreso = $fechaIngreso;
        $this->numeroFactura = $numeroFactura;
    }
}