<?php
require("./db.php");

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
	$count = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$stmt = $conn->prepare("SELECT id, name FROM birbs LIMIT :limit OFFSET :offset");
	$stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
	$stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
	$stmt->execute();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$nextOffset = $offset + $limit;
	$prevOffset = $offset - $limit;

	$base = "http://localhost/FugleAPI"; // or include /index.php if you prefer
	$next = "$base?offset=$nextOffset&limit=$limit";
	$prev = "$base?offset=$prevOffset&limit=$limit";

	// Tilføj Hypermedia Controls
	for ($i = 0; $i < count($results); $i++) {
		$results[$i]["url"] = "$base?id=" . $results[$i]["id"];
		unset($results[$i]["id"]);
	}

	header("Content-Type: application/json; charset=utf-8");
	$output = [
		"count" => $count["COUNT(id)"],
		"next" => $nextOffset < $count["COUNT(id)"] ? $next : null,
		"prev" => $offset <= 0 ? null : $prev,
		"results" => $results
	];
	echo json_encode($output);
}

// HENT ENKELT PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $id = validateID();

    $stmt = $conn->prepare("
        SELECT
            birbs.id,
            birbs.name,
            birbs.habitat,
            birbs.wingspan_in_meters,
            birbs.diet,
            birbs.features,
            birbs.weight_in_grams,
            media.url AS url
        FROM birbs
            LEFT JOIN birb_media ON birb_media.birb_id = birbs.id
            LEFT JOIN media ON media.id = birb_media.media_id
        WHERE birbs.id = :id
    ");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows || count($rows) === 0) {
        http_response_code(404);
        exit;
    }

    // build output from first row's birb columns and gather all media urls
    $output = [
        "id" => (int)$rows[0]["id"],
        "name" => $rows[0]["name"],
        "habitat" => $rows[0]["habitat"],
        "diet" => $rows[0]["diet"],
        "weight" => isset($rows[0]["weight_in_grams"]) ? (int)$rows[0]["weight_in_grams"] : null,
        "wingspan" => isset($rows[0]["wingspan_in_meters"]) ? $rows[0]["wingspan_in_meters"] : null,
        "features" => $rows[0]["features"],
        "url" => []
    ];

    foreach ($rows as $r) {
        if (!empty($r["url"])) $output["url"][] = $r["url"];
    }
    // remove duplicates
    $output["url"] = array_values(array_unique($output["url"]));

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($output);
}





// OPRET ET PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$name = $_POST["name"];
	$habitat = $_POST["habitat"];
    $diet = $_POST["diet"];
	$wingspan = $_POST["wingspan"];
	$weight = $_POST["weight"];
    $features = $_POST["features"];

	$stmt = $conn->prepare("INSERT INTO birbs (`name`, `habitat`, `diet`, `weight_in_grams`, `wingspan_in_meters`, `features`)
													VALUES(:name, :habitat, :diet, :weight, :wingspan, :features)");
	
	$stmt->bindParam(":name", $name);
	$stmt->bindParam(":habitat", $habitat);
    $stmt->bindParam(":diet", $diet);
	$stmt->bindParam(":wingspan", $wingspan, PDO::PARAM_DECIMAL);
	$stmt->bindParam(":weight", $weight, PDO::PARAM_INT);
    $stmt->bindParam(":features", $features);

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
		|| empty($body["weight"])
		|| empty($body["wingspan"])
		|| empty($body["features"])) {
			header("Content-Type: application/json; charset=utf-8");
			http_response_code(400);
			echo json_encode(["message" => "missing field(s). Required fields: 'name', 'habitat', 'diet', 'weight', 'wingspan', 'features'"]);
			exit;
	}
	
	$stmt = $conn->prepare("UPDATE birbs
			SET name = :name, habitat = :habitat, diet = :diet, wingspan_in_meters = :wingspan, weight_in_grams = :weight, features = :features WHERE id = :id");
	
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->bindParam(":name", $body["name"]);
	$stmt->bindParam(":habitat", $body["habitat"]);
	$stmt->bindParam(":diet", $body["diet"]);
	$stmt->bindParam(":weight", $body["weight"], PDO::PARAM_INT);
	$stmt->bindParam(":wingspan", $body["wingspan"], PDO::PARAM_DECIMAL);
	$stmt->bindParam(":features", $body["features"]);

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
	if (empty($_GET["id"])) {
		http_response_code(400);
		exit;
	}

	$id = $_GET["id"];

	$stmt = $conn->prepare("DELETE FROM birbs WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);

	$stmt->execute();
	http_response_code(204);
}