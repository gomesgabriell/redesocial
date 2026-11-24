
<?php

require_once __DIR__ . "/../controllers/UsuarioController.php";
require_once __DIR__ . "/../controllers/PublicacaoController.php";
require_once __DIR__ . "/../controllers/CurtidaController.php";

$rota = $_GET["rota"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];

switch ($rota) {

    case "usuario":
        switch ($metodo) {
            case "POST":
                createUsuario($con);
                break;

            case "GET":
                readUsuario($con);
                break;

            case "PUT":
                updateUsuario($con);
                break;

            case "DELETE":
                deleteUsuario($con);
                break;
        }
        break;

    case "publicacao":
        switch ($metodo) {
            case "POST":
                createPublicacao($con);
                break;

            case "GET":
                readPublicacao($con);
                break;

            case "PUT":
                updatePublicacao($con);
                break;

            case "DELETE":
                deletePublicacao($con);
                break;
        }
        break;

    case "curtida":
        switch ($metodo) {
            case "POST":
                createCurtida($con);
                break;

            case "GET":
                readCurtida($con);
                break;

            case "DELETE":
                deleteCurtida($con);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo json_encode([
            "erro" => "Rota não encontrada"
        ]);
        break;
}
