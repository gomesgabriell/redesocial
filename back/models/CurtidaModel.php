
<?php

function createCurtidaModel($con, $dados) {
    $sql = "INSERT INTO curtidas (usuario_id, publicacao_id)
            VALUES (:usuario_id, :publicacao_id)";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":usuario_id" => $dados["usuario_id"],
        ":publicacao_id" => $dados["publicacao_id"]
    ]);
}

function readCurtidaModel($con, $publicacao_id = null) {
    if ($publicacao_id) {
        $sql = "SELECT * FROM curtidas
                WHERE publicacao_id = :publicacao_id";

        $stmt = $con->prepare($sql);
        $stmt->execute([":publicacao_id" => $publicacao_id]);

        return $stmt->fetchAll();
    }

    $sql = "SELECT * FROM curtidas ORDER BY id DESC";

    return $con->query($sql)->fetchAll();
}

function deleteCurtidaModel($con, $dados) {
    $sql = "DELETE FROM curtidas
            WHERE usuario_id = :usuario_id
            AND publicacao_id = :publicacao_id";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":usuario_id" => $dados["usuario_id"],
        ":publicacao_id" => $dados["publicacao_id"]
    ]);
}
