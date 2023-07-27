<?php

class Usuario {
    public $id;
    public $nombre;
    public $email;
    public $password;

    public function __construct($id, $nombre, $email, $password) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
    }
}