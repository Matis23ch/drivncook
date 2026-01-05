<?php

class Franchisé
{
    public static function create(PDO $pdo, array $data)
    {
        $stmt = $pdo->prepare("
            SELECT id, actif 
            FROM franchises 
            WHERE email = ?
        ");
        $stmt->execute([$data['email']]);
        $franchise = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($franchise && !$franchise['actif']) {
            $stmt = $pdo->prepare("
                UPDATE franchises
                SET nom = ?, actif = 1
                WHERE id = ?
            ");
            $stmt->execute([
                $data['nom'],
                $franchise['id']
            ]);
            $franchise_id = $franchise['id'];
        } 
        elseif ($franchise && $franchise['actif']) {
            throw new Exception("Ce franchisé existe déjà");
        } 
        else {
            $stmt = $pdo->prepare("
                INSERT INTO franchises (nom, email, actif)
                VALUES (?, ?, 1)
            ");
            $stmt->execute([
                $data['nom'],
                $data['email']
            ]);
            $franchise_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        if ($user) {
            $stmt = $pdo->prepare("
                UPDATE users
                SET franchise_id = ?
                WHERE email = ?
            ");
            $stmt->execute([
                $franchise_id,
                $data['email']
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO users (email, password, role, franchise_id)
                VALUES (?, 'test', 'FRANCHISE', ?)
            ");
            $stmt->execute([
                $data['email'],
                $franchise_id
            ]);
        }
    }

    public static function desactiver(PDO $pdo, int $id)
    {
        $stmt = $pdo->prepare("
            UPDATE franchises
            SET actif = 0
            WHERE id = ?
        ");
        $stmt->execute([$id]);
    }
}



