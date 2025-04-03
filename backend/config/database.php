<?php

namespace app\Config;

use PDO;
use PDOException;

class Database
{
    private static $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            // Adaptez ces valeurs à votre configuration
            $host = 'localhost';
            $dbname = 'gestion-horaire';
            $user = 'sgbd';
            $pass = 'sgbd';

            try {
                self::$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
