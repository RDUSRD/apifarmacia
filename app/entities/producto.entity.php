<?php

class Producto {
    public $id;
    public $nombre;
    public $imagen;

    public function __construct($id, $nombre, $imagen) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->imagen = $imagen;
    }
}