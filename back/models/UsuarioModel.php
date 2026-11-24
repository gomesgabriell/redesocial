<?php

function inserirUsuario($con, $dados) {
    $stmt = $con->prepare(
        "INSERT INTO usuarios (nome, username, email, senha)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->execute([
        $dados["nome"],
        $dados["username"],
        $dados["email"],
        password_hash($dados["senha"], PASSWORD_DEFAULT)
    ]);

    return $con->lastInsertId();
}

function listarUsuario($con) {
    $stmt = $con->query("SELECT * FROM usuarios");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function atualizarUsuario($con, $id, $dados) {
    $stmt = $con->prepare(
        "UPDATE usuarios SET
         nome = ?,
         username = ?,
         email = ?,
         senha = ?
         WHERE id_usuario = ?"
    );

    $stmt->execute([
        $dados["nome"],
        $dados["username"],
        $dados["email"],
        password_hash($dados["senha"], PASSWORD_DEFAULT),
        $id
    ]);
}

function removerUsuario($con, $id) {
    $stmt = $con->prepare(
        "DELETE FROM usuarios WHERE id_usuario = ?"
    );

    $stmt->execute([$id]);
}