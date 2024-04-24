<?php
require_once ROOT . "/db/queries.php";
require ROOT . "/vendor/autoload.php";

use \Firebase\JWT\JWT;

class Control {
    public static function initSession(): void
    {
        session_start();
        if (!isset($_SESSION["jwt"])) {
            $_SESSION["jwt"] = "123default123";
            $_SESSION["username"] = "123default123";
        }
    }

    private static function updateSession(string $jwt, string $username): void
    {
        session_regenerate_id();
        $_SESSION["jwt"] = $jwt;
        $_SESSION["username"] = $username;
    }

    private static function generateJWT(int $id, string $username): string
    {
        $signatureKey = "123454321";
        $payload = [
            "id" => $id,
            "username" => $username,
            "session_id" => session_id()
        ];

        return JWT::encode($payload, $signatureKey,"HS256");
    }

    public static function get(): array
    {
        $cars = DB::getAllCars();

        if (count($cars) > 0) {
            return [
                "code" => 200,
                "success" => true,
                "error" => "",
                "body" => $cars
            ];
        } else {
            return [
                "code" => 200,
                "success" => true,
                "error" => "",
                "body" => "No cars found"
            ];
        }
    }

    public static function post(array $args, string $username, string $token): array
    {
        if (!self::checkLogin($username, $token)) { //check auth
            return [
                "code" => 401,
                "success" => false,
                "error" => "Unauthorized, wrong jwt or username",
                "body" => ["username" => $username, "jwt" => $token]
            ];
        }

        $success = DB::insertCar($args, $username);

        if ($success) {
            return [
                "code" => 201,
                "success" => true,
                "error" => "",
                "body" => ["fields" => $args, "byUser" => $username]
            ];
        } else {
            return [
                "code" => 500,
                "success" => false,
                "error" => "Unexpected error!",
                "body" => ""
            ];
        }
    }

    public static function delete(int $carTargetId, string $username, string $token): array
    {
        if (!self::checkLogin($username, $token)) { //check auth
            return [
                "code" => 401,
                "success" => false,
                "error" => "Unauthorized, wrong jwt or username",
                "body" => ["username" => $username, "jwt" => $token]
            ];
        }

        if (DB::search("cars", "id", $carTargetId)[0]->owner !== $username) {
            return [
                "code" => 401,
                "success" => false,
                "error" => "Unauthorized, you are not the owner of the target car",
                "body" => ["username" => $username, "carTargetId" => $carTargetId]
            ];
        }

        if ($carTargetId === -1) {
            return [
                "code" => 500,
                "success" => false,
                "error" => "Bad request, missing id of the target",
                "body" => ""
            ];
        }

        $success = DB::deleteCar($carTargetId);

        if ($success) {
            return [
                "code" => 200,
                "success" => true,
                "error" => "",
                "body" => $carTargetId
            ];
        } else {
            return [
                "code" => 500,
                "success" => false,
                "error" => "Bad request, target id not found",
                "body" => ""
            ];
        }
    }

    public static function register(array $args, string $username, string $password): array
    {
        $searchResult = DB::search("users", "username", $username);

        if (count($searchResult)) {
            return [
                "code" => 409,
                "success" => false,
                "error" => "User already exists",
                "body" => $username
            ];
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $success = DB::register($args, $username, $hashed_password);

        if ($success) {
            return [
                "code" => 201,
                "success" => true,
                "error" => "",
                "body" => [$args, $username, $hashed_password]
            ];
        } else {
            return [
                "code" => 500,
                "success" => false,
                "error" => "Unexpected Error!",
                "body" => ""
            ];
        }
    }

    public static function login(string $username, string $password): array
    {
        $searchResult = DB::search("users", "username", $username);

        if (!count($searchResult)) {
            return [
                "code" => 404,
                "success" => false,
                "error" => "User does not exist!",
                "body" => $username
            ];
        }

        $searchResult = $searchResult[0];

        $storedPassword = $searchResult->password;

        $loginSuccess = password_verify($password, $storedPassword);

        if ($loginSuccess) {
            $jwt = self::generateJWT($searchResult->id, $searchResult->username);
            self::updateSession($jwt, $searchResult->username);

            return [
                "code" => 200,
                "success" => true,
                "error" => "",
                "body" => ["jwt" => $jwt, "username" => $searchResult->username]
            ];
        } else {
            return [
                "code" => 401,
                "success" => false,
                "error" => "Wrong password!",
                "body" => ""
            ];
        }
    }

    public static function checkLogin(string $username, string $jwt): bool
    {
        if ($username === $_SESSION["username"] && $jwt === $_SESSION["jwt"]) {
            return true;
        }
        return false;
    }
}