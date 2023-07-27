<?php

class Clasificacion {
    public $id;
    public $kind;

    public function __construct($id, $kind) {
        $this->id = $id;
        $this->kind = $kind;
    }
}