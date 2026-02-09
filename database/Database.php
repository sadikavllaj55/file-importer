<?php

namespace Database;

use PDO;
use PDOException;

final class Database
{

    public static function connect(): PDO
    {
        $host    = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port    = $_ENV['DB_PORT'] ?? 3306;
        $db      = $_ENV['DB_DATABASE'] ?? '';
        $user    = $_ENV['DB_USERNAME'] ?? '';
        $pass    = $_ENV['DB_PASSWORD'] ?? '';
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
        $driver  = $_ENV['DB_DRIVER'] ?? 'mysql';

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $driver,
            $host,
            $port,
            $db,
            $charset
        );

        try {
            return new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            fwrite(STDERR, "Database connection error: {$e->getMessage()}" . PHP_EOL);
            exit(1);
        }
    }
}
