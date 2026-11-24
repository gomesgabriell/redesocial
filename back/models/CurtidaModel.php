<?php

function createCurtidaModel($con, $dados) {
    $sql = "INSERT INTO curtidas
            (id_usuario, id_publicacao)
            VALUES (:id_usuario, :id_publicacao)";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":id_usuario" => $dados["id_usuario"],
        ":id_publicacao" => $dados["id_publicacao"]
    ]);
}

function readCurtidaModel($con, $id_publicacao = null) {
    if ($id_publicacao) {
        $sql = "SELECT * FROM curtidas
                WHERE id_publicacao = :id_publicacao";

        $stmt = $con->prepare($sql);

        $stmt->execute([
            ":id_publicacao" => $id_publicacao
        ]);

        return $stmt->fetchAll();
    }

    $sql = "SELECT * FROM curtidas
            ORDER BY data_curtida DESC";

    return $con->query($sql)->fetchAll();
}

function deleteCurtidaModel($con, $dados) {
    $sql = "DELETE FROM curtidas
            WHERE id_usuario = :id_usuario
            AND id_publicacao = :id_publicacao";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":id_usuario" => $dados["id_usuario"],
        ":id_publicacao" => $dados["id_publicacao"]
    ]);
}
