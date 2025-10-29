<?php
require(__DIR__ . "/db.php");

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

    // Tilføj Hypermedia Controls
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

// HENT ENKELT PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $id = validateID();

    $stmt = $conn->prepare("SELECT 
                        birbs.id, birbs.name,
                        birbs.description, birbs.wingspan,
                        birbs.weight_in_grams, birbs.habitat, birbs.diet, birbs.features, media.url AS url
            FROM birbs
                INNER JOIN product_media ON product_media.birb_id = birbs.id
                INNER JOIN media ON media.id = product_media.media_id
            WHERE birbs.id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = [
        "id" => $results[0]["id"],
        "name" => $results[0]["name"],
        "habitat" => $results[0]["habitat"],
        "diet" => $results[0]["diet"],
        "weight" => $results[0]["weight_in_grams"],
        "wingspan" => $results[0]["wingspan"],
        "features" => $results[0]["features"],
        "url" => [],
	];

    for ($i = 0; $i < count($results); $i++) {
        $output["url"][] = $results[$i]["url"];
    }

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($output);
}

// OPRET ET PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $habitat = $_POST["habitat"];
    $diet = $_POST["diet"];
    $features = $_POST["features"];
    $weight = $_POST["weight"];
    $wingspan = $_POST["wingspan"];

	$stmt = $conn->prepare("INSERT INTO birbs (`name`, `habitat`, `diet`, `wingspan`, `features`, `weight_in_grams`)
													VALUES(:name, :habitat, :diet, :wingspan, :features, :weight)");
	
	$stmt->bindParam(":name", $name);
	$stmt->bindParam(":habitat", $habitat);
	$stmt->bindParam(":diet", $diet);
	$stmt->bindParam(":weight", $weight, PDO::PARAM_INT);
	$stmt->bindParam(":wingspan", $wingspan, PDO::PARAM_INT);
	$stmt->bindParam(":features", $features);

    $stmt->execute();
    http_response_code(201);
}

// REDIGER ET PRODUKT (PUT)
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $id = validateID();

    parse_str(file_get_contents("php://input"), $body);

/* 	if (empty($body["name"])) {
		header("Content-Type: application/json; charset=utf-8");
		http_response_code(400);
		echo json_encode(["message" => "missing field 'name'"]);
		exit;
	}
	if (empty($body["description"])) {
		header("Content-Type: application/json; charset=utf-8");
		http_response_code(400);
		echo json_encode(["message" => "missing field 'description'"]);
		exit;
	}
	if (empty($body["price"])) {
		header("Content-Type: application/json; charset=utf-8");
		http_response_code(400);
		echo json_encode(["message" => "missing field 'price'"]);
		exit;
	}
	if (empty($body["weight"])) {
		header("Content-Type: application/json; charset=utf-8");
		http_response_code(400);
		echo json_encode(["message" => "missing field 'weight'"]);
		exit;
	} */

	if (empty($body["name"])
		|| empty($body["habitat"])
		|| empty($body["diet"])
		|| empty($body["features"])
		|| empty($body["wingspan"])
		|| empty($body["weight"])) {
			header("Content-Type: application/json; charset=utf-8");
			http_response_code(400);
			echo json_encode(["message" => "missing field(s). Required fields: 'name', 'description', 'price', 'weight'"]);
			exit;
	}
	
	$stmt = $conn->prepare("UPDATE birbs
			SET name = :name, description = :description, wingspan = :wingspan, weight_in_grams = :weight WHERE id = :id");
	
	$stmt->bindParam(":name", $body["name"]);
	$stmt->bindParam(":habitat", $body["habitat"]);
	$stmt->bindParam(":diet", $body["diet"]);
	$stmt->bindParam(":features", $body["features"]);
	$stmt->bindParam(":weight", $body["weight"], PDO::PARAM_INT);
	$stmt->bindParam(":wingspan", $body["wingspan"], PDO::PARAM_INT);
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);

    $stmt->execute();

    $stmt = $conn->prepare("SELECT * FROM birbs WHERE id = :id");
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