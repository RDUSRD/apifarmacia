<?php

class Farmaceuta {
    public $id;
    public $name;

    public function __construct($id, $name, $fechaRegistro) {
        $this->id = $id;
        $this->name = $name;
    }
}