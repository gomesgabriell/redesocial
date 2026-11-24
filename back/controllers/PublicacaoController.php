<?php

require_once __DIR__ . "/../models/PublicacaoModel.php";

function createPublicacao($con) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        empty($dados["id_usuario"]) ||
        empty($dados["legenda"])
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe o usuário e a legenda"]);
        return;
    }

    if (createPublicacaoModel($con, $dados)) {
        http_response_code(201);
        echo json_encode(["mensagem" => "Publicação criada com sucesso"]);
    }
}

function readPublicacao($con) {
    $id = $_GET["id_publicacao"] ?? null;

    $publicacoes = readPublicacaoModel($con, $id);

    echo json_encode($publicacoes);
}

function updatePublicacao($con) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        empty($dados["id_publicacao"]) ||
        empty($dados["legenda"])
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe o ID e a legenda"]);
        return;
    }

    if (updatePublicacaoModel($con, $dados)) {
        echo json_encode(["mensagem" => "Publicação atualizada com sucesso"]);
    }
}

function deletePublicacao($con) {
    $dados = json_decode(file_get_contents("php://input"), true);

    $id = $dados["id_publicacao"] ?? $_GET["id_publicacao"] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe o ID da publicação"]);
        return;
    }

    if (deletePublicacaoModel($con, $id)) {
        echo json_encode(["mensagem" => "Publicação excluída com sucesso"]);
    }
}
