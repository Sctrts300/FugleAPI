<?php
require("../db.php");

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

	$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
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
	$stmt = $conn->prepare("SELECT id, name FROM products");
	$stmt->execute();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Tilføj Hypermedia Controls
	for ($i = 0; $i < count($results); $i++) {
		$results[$i]["url"] = "http://localhost/webshop/products?id=" . $results[$i]["id"];
		unset($results[$i]["id"]);
	}

	header("Content-Type: application/json; charset=utf-8");
	$output = ["results" => $results];
	echo json_encode($output);
}

// HENT ENKELT PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
	$id = validateID();

	$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);
	$stmt->execute();

	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	header("Content-Type: application/json; charset=utf-8");
	echo json_encode($result);
}

// OPRET ET PRODUKT
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$name = $_POST["name"];
	$description = $_POST["description"];
	$price = $_POST["price"];
	$weight = $_POST["weight"];

	$stmt = $conn->prepare("INSERT INTO products (`name`, `description`, `price`, `weight_in_grams`)
													VALUES(:name, :description, :price, :weight)");
	
	$stmt->bindParam(":description", $description);
	$stmt->bindParam(":name", $name);
	$stmt->bindParam(":price", $price, PDO::PARAM_INT);
	$stmt->bindParam(":weight", $weight, PDO::PARAM_INT);

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
		|| empty($body["description"])
		|| empty($body["price"])
		|| empty($body["weight"])) {
			header("Content-Type: application/json; charset=utf-8");
			http_response_code(400);
			echo json_encode(["message" => "missing field(s). Required fields: 'name', 'description', 'price', 'weight'"]);
			exit;
	}
	
	$stmt = $conn->prepare("UPDATE products
			SET name = :name, description = :description, price = :price, weight_in_grams = :weight WHERE id = :id");
	
	$stmt->bindParam(":description", $body["description"]);
	$stmt->bindParam(":name", $body["name"]);
	$stmt->bindParam(":price", $body["price"], PDO::PARAM_INT);
	$stmt->bindParam(":weight", $body["weight"], PDO::PARAM_INT);
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);

	$stmt->execute();

	$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
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

	$stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
	$stmt->bindParam(":id", $id, PDO::PARAM_INT);

	$stmt->execute();
	http_response_code(204);
}