<?php

function createPublicacaoModel($con, $dados) {
    $sql = "INSERT INTO publicacoes
            (id_usuario, legenda, imagem_url)
            VALUES (:id_usuario, :legenda, :imagem_url)";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":id_usuario" => $dados["id_usuario"],
        ":legenda" => $dados["legenda"],
        ":imagem_url" => $dados["imagem_url"] ?? null
    ]);
}

function readPublicacaoModel($con, $id = null) {
    if ($id) {
        $sql = "SELECT * FROM publicacoes
                WHERE id_publicacao = :id";

        $stmt = $con->prepare($sql);
        $stmt->execute([":id" => $id]);

        return $stmt->fetch();
    }

    $sql = "SELECT * FROM publicacoes
            ORDER BY data_publicacao DESC";

    return $con->query($sql)->fetchAll();
}

function updatePublicacaoModel($con, $dados) {
    $sql = "UPDATE publicacoes
            SET legenda = :legenda,
                imagem_url = :imagem_url
            WHERE id_publicacao = :id_publicacao";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":legenda" => $dados["legenda"],
        ":imagem_url" => $dados["imagem_url"] ?? null,
        ":id_publicacao" => $dados["id_publicacao"]
    ]);
}

function deletePublicacaoModel($con, $id) {
    $sql = "DELETE FROM publicacoes
            WHERE id_publicacao = :id";

    $stmt = $con->prepare($sql);

    return $stmt->execute([":id" => $id]);
}
