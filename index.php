<?php
require(__DIR__ . "/db.php");

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function validateID() {
    global $conn;
    if (empty($_GET["id"])) {
        http_response_code(400);
        exit;
    }

    $id = $_GET["id"];

    if (!is_numeric($id)) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode(["message" => "ID is malformed"]);
        exit;
    }

    $id = intval($id, 10);

    $stmt = $conn->prepare("SELECT * FROM birbs WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!is_array($result)) {
        http_response_code(404);
        exit;
    }

    return $id;
}

// HENT ALLE PRODUKTER
if ($_SERVER["REQUEST_METHOD"] === "GET" && empty($_GET["id"])) {
    $limit = isset($_GET["limit"]) ? intval($_GET["limit"]) : 10;
    $offset = isset($_GET["offset"]) ? intval($_GET["offset"]) : 0;

    $stmt = $conn->prepare("SELECT COUNT(id) FROM birbs");
    $stmt->execute();
    $totalCount = (int)$stmt->fetchColumn();

    $stmt = $conn->prepare("SELECT id, name FROM birbs LIMIT :limit OFFSET :offset");
    $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $nextOffset = $offset + $limit;
    $prevOffset = $offset - $limit;

    $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/" . basename($_SERVER['SCRIPT_NAME']);
    $next = $base . "?offset=$nextOffset&limit=$limit";
    $prev = $base . "?offset=$prevOffset&limit=$limit";

    for ($i = 0; $i < count($results); $i++) {
        $results[$i]["url"] = $base . "?id=" . $results[$i]["id"];
        unset($results[$i]["id"]);
    }

    header("Content-Type: application/json; charset=utf-8");
    $output = [
        "count" => $totalCount,
        "next" => $nextOffset < $totalCount ? $next : null,
        "prev" => $offset <= 0 ? null : $prev,
        "results" => $results
    ];
    echo json_encode($output);
}

// HENT ENKELT PRODUKT (no JOINs — use existing birbs columns)
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $id = validateID();

    $stmt = $conn->prepare("SELECT id, name, habitat, diet, weight_in_grams, wingspan_in_meters, features FROM birbs WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(404);
        echo json_encode(["message" => "Not found"]);
        exit;
    }

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($row);
}

// OPRET ET PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"] ?? null;
    $habitat = $_POST["habitat"] ?? null;
    $diet = $_POST["diet"] ?? null;
    $features = $_POST["features"] ?? null;
    $weight = isset($_POST["weight"]) ? intval($_POST["weight"]) : null;
    $wingspan = isset($_POST["wingspan"]) ? floatval($_POST["wingspan"]) : null;

    if (empty($name) || $weight === null || $wingspan === null) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode(["message" => "missing required fields: name, weight, wingspan"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO birbs (`name`, `habitat`, `diet`, `wingspan_in_meters`, `features`, `weight_in_grams`)
                            VALUES(:name, :habitat, :diet, :wingspan, :features, :weight)");

    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":habitat", $habitat);
    $stmt->bindParam(":diet", $diet);
    $stmt->bindParam(":wingspan", $wingspan);
    $stmt->bindParam(":features", $features);
    $stmt->bindParam(":weight", $weight, PDO::PARAM_INT);

    $stmt->execute();
    http_response_code(201);
}

// REDIGER ET PRODUKT (PUT)
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $id = validateID();

    parse_str(file_get_contents("php://input"), $body);

    if (empty($body["name"])
        || empty($body["habitat"])
        || empty($body["diet"])
        || empty($body["features"])
        || !isset($body["wingspan"])
        || !isset($body["weight"])) {
        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode(["message" => "missing field(s). Required: name, habitat, diet, features, wingspan, weight"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE birbs
                            SET name = :name, habitat = :habitat, diet = :diet, features = :features, wingspan_in_meters = :wingspan, weight_in_grams = :weight
                            WHERE id = :id");

    $stmt->bindParam(":name", $body["name"]);
    $stmt->bindParam(":habitat", $body["habitat"]);
    $stmt->bindParam(":diet", $body["diet"]);
    $stmt->bindParam(":features", $body["features"]);
    $stmt->bindParam(":wingspan", $body["wingspan"]);
    $stmt->bindParam(":weight", $body["weight"], PDO::PARAM_INT);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();

    $stmt = $conn->prepare("SELECT id, name, habitat, diet, weight_in_grams, wingspan_in_meters, features FROM birbs WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}

// SLET ET PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $id = validateID();

    $stmt = $conn->prepare("DELETE FROM birbs WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();
    http_response_code(204);
}