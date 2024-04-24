<?php
require_once ROOT . "/common/config.php";
require ROOT . "/vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;

class DB {
    private static $initialized;
    private static Illuminate\Database\Capsule\Manager $db;
    private static function initialize(): void
    {
        if (self::$initialized)
            return;

        self::$initialized = true;
        $capsule = new Capsule;
        $capsule->addConnection([
            "driver" => DB_DRIVER,
            "host" => DB_HOST,
            "database" => DB_NAME,
            "username" => DB_USER,
            "password" => DB_PASS
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$db = $capsule;
    }

    public static function getAllCars(): \Illuminate\Support\Collection
    {
        self::initialize();

        return self::$db::table("cars")
            ->select("*")
            ->get();
    }

    public static function search(string $table, string $field, string $search): \Illuminate\Support\Collection
    {
        self::initialize();

        return self::$db::table($table)
            ->where($field, $search)
            ->get();
    }

    public static function insertCar(array $args, string $username): bool
    {
        self::initialize();

        return self::$db::table("cars")
            ->insert(["owner" => $username, "brand" => $args["brand"], "model"=> $args["model"], "year"=> $args["year"], "price"=> $args["price"],
                "engine"=> $args["engine"], "fuel"=> $args["fuel"], "power"=> $args["power"], "image"=> $args["image"], "description"=> $args["description"]]);

    }

    public static function deleteCar(int $id): int
    {
        self::initialize();

        return self::$db::table("cars")
            ->where("id", "=", $id)
            ->delete();

    }

    public static function register(array $args, string $username, string $hashed_password): bool
    {
        self::initialize();

        return self::$db::table("users")
            ->insert(["firstname" => $args["firstname"], "lastname"=> $args["lastname"], "email"=> $args["email"],
                "username"=> $username, "password"=> $hashed_password, "isadmin"=> $args["isadmin"]]);
    }

//    public static function updateJWT(int $id, string $jwt): int
//    {
//        self::initialize();
//
//        return self::$db::table("users")
//            ->where("id", $id)
//            ->update(["jwt" => $jwt]);
//
//    }
}