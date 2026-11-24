<?php

function buscarUsuarioPorEmail($con, $email) {

    $stmt = $con->prepare(
        "SELECT id_usuario, nome, username, email, senha
         FROM usuarios
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->execute([$email]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}