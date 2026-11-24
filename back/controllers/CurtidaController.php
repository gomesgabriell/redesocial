<?php

require_once __DIR__ . "/../models/CurtidaModel.php";

function createCurtida($con) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        empty($dados["id_usuario"]) ||
        empty($dados["id_publicacao"])
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe o usuário e a publicação"]);
        return;
    }

    if (createCurtidaModel($con, $dados)) {
        http_response_code(201);
        echo json_encode(["mensagem" => "Curtida adicionada com sucesso"]);
    }
}

function readCurtida($con) {
    $id = $_GET["id_publicacao"] ?? null;

    $curtidas = readCurtidaModel($con, $id);

    echo json_encode($curtidas);
}

function deleteCurtida($con) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        empty($dados["id_usuario"]) ||
        empty($dados["id_publicacao"])
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe o usuário e a publicação"]);
        return;
    }

    if (deleteCurtidaModel($con, $dados)) {
        echo json_encode(["mensagem" => "Curtida removida com sucesso"]);
    }
}
