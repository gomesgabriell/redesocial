
<?php

function createPublicacaoModel($con, $dados) {
    $sql = "INSERT INTO publicacoes (usuario_id, conteudo)
            VALUES (:usuario_id, :conteudo)";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":usuario_id" => $dados["usuario_id"],
        ":conteudo" => $dados["conteudo"]
    ]);
}

function readPublicacaoModel($con, $id = null) {
    if ($id) {
        $sql = "SELECT * FROM publicacoes WHERE id = :id";
        $stmt = $con->prepare($sql);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch();
    }

    $sql = "SELECT * FROM publicacoes ORDER BY id DESC";
    return $con->query($sql)->fetchAll();
}

function updatePublicacaoModel($con, $dados) {
    $sql = "UPDATE publicacoes
            SET conteudo = :conteudo
            WHERE id = :id";

    $stmt = $con->prepare($sql);

    return $stmt->execute([
        ":conteudo" => $dados["conteudo"],
        ":id" => $dados["id"]
    ]);
}

function deletePublicacaoModel($con, $id) {
    $sql = "DELETE FROM publicacoes WHERE id = :id";

    $stmt = $con->prepare($sql);

    return $stmt->execute([":id" => $id]);
}
