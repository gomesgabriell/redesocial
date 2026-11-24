<?php

require_once __DIR__ . "/../models/LoginModel.php";

function loginUsuario($con) {

    $dados = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (
        !is_array($dados) ||
        empty($dados["email"]) ||
        empty($dados["senha"])
    ) {
        http_response_code(400);

        echo json_encode([
            "erro" => "Informe o e-mail e a senha"
        ]);

        return;
    }

    $email = trim($dados["email"]);
    $senha = $dados["senha"];

    $usuario = buscarUsuarioPorEmail($con, $email);

    if (
        !$usuario ||
        !password_verify($senha, $usuario["senha"])
    ) {
        http_response_code(401);

        echo json_encode([
            "erro" => "E-mail ou senha incorretos"
        ]);

        return;
    }

    unset($usuario["senha"]);

    echo json_encode([
        "mensagem" => "Login realizado com sucesso!",
        "usuario" => $usuario
    ]);
}