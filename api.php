<?php
header("Content-Type: application/json");
require_once('config.php');

// Récupérer la méthode de la requête
$method = $_SERVER['REQUEST_METHOD'];

// Traiter les différentes méthodes
switch ($method) {
    case 'POST':
        // Ajout d'une nouvelle tâche
        $data = json_decode(file_get_contents("php://input"));
        if (isset($data->titre, $data->description, $data->dateEcheance, $data->statut)) {
            $stmt = $connexion->prepare("INSERT INTO taches (titre, description, dateEcheance, statut) VALUES (:titre, :description, :dateEcheance, :statut)");
            $stmt->bindParam(':titre', $data->titre, PDO::PARAM_STR);
            $stmt->bindParam(':description', $data->description, PDO::PARAM_STR);
            $stmt->bindParam(':dateEcheance', $data->dateEcheance, PDO::PARAM_STR);
            $stmt->bindParam(':statut', $data->statut, PDO::PARAM_STR);

            if ($stmt->execute()) {
                response("Tâche ajoutée avec succès.", 201);
            } else {
                response("Échec de l'ajout de la tâche.", 500);
            }
            $stmt->closeCursor(); // Fermer le curseur pour éviter des problèmes potentiels
        } else {
            response("Les données sont incomplètes.", 400);
        }
        break;

    case 'GET':
        // Récupération des tâches ou d'une tâche spécifique par ID
        $id = isset($_GET['id']) ? $_GET['id'] : null;
    
        if ($id !== null) {
            $stmt = $connexion->prepare("SELECT * FROM taches WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $tache = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($tache) {
                response($tache, 200);
            } else {
                response("Aucune tâche trouvée avec l'ID spécifié.", 404);
            }
        } else {
            // Si aucun ID spécifié, récupérer toutes les tâches
            $stmt = $connexion->query("SELECT * FROM taches");
            $taches = $stmt->fetchAll(PDO::FETCH_ASSOC);
            response($taches, 200);
        }
        break;

    case 'PUT':
        // Mise à jour d'une tâche existante
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $data = json_decode(file_get_contents("php://input"));
        
        if ($id !== null && isset($data->titre, $data->description, $data->dateEcheance, $data->statut)) {
            $stmt = $connexion->prepare("UPDATE taches SET titre = :titre, description = :description, dateEcheance = :dateEcheance, statut = :statut WHERE id = :id");
            $stmt->bindParam(':titre', $data->titre, PDO::PARAM_STR);
            $stmt->bindParam(':description', $data->description, PDO::PARAM_STR);
            $stmt->bindParam(':dateEcheance', $data->dateEcheance, PDO::PARAM_STR);
            $stmt->bindParam(':statut', $data->statut, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            if ($stmt->execute()) {
                response("Tâche mise à jour avec succès.", 200);
            } else {
                response("Échec de la mise à jour de la tâche.", 500);
            }
            $stmt->closeCursor();
        } else {
            response("L'ID ou les données sont incomplètes.", 400);
        }
        break;
        

    case 'DELETE':
        // Suppression d'une tâche existante
        $id = isset($_GET['id']) ? $_GET['id'] : die(response("L'ID de la tâche est requis.", 400));
        $stmt = $connexion->prepare("DELETE FROM taches WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            response("Tâche supprimée avec succès.", 200);
        } else {
            response("Échec de la suppression de la tâche.", 500);
        }
        $stmt->closeCursor();
        break;

    default:
        // Méthode non prise en charge
        response("Méthode non autorisée.", 405);
}

function response($data, $status) {
    $response['data'] = $data;
    $response['status'] = $status;
    http_response_code($status);
    echo json_encode($response);
    exit;
}