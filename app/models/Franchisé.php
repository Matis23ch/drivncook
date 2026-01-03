<?php

class Franchisé
{
    public static function create(PDO $pdo, array $data)
    {
        // 🔍 Chercher franchisé existant (même inactif)
        $stmt = $pdo->prepare("
            SELECT id, actif 
            FROM franchises 
            WHERE email = ?
        ");
        $stmt->execute([$data['email']]);
        $franchise = $stmt->fetch(PDO::FETCH_ASSOC);

        // ✅ Existe déjà mais INACTIF → on réactive
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
            return;
        }

        // ❌ Existe déjà et actif → erreur
        if ($franchise && $franchise['actif']) {
            throw new Exception("Ce franchisé existe déjà");
        }

        // 🆕 Création franchisé
        $stmt = $pdo->prepare("
            INSERT INTO franchises (nom, email, actif)
            VALUES (?, ?, 1)
        ");
        $stmt->execute([
            $data['nom'],
            $data['email']
        ]);

        $franchise_id = $pdo->lastInsertId();

        // 🔗 Lier au user si existe
        $stmt = $pdo->prepare("
            UPDATE users
            SET franchise_id = ?
            WHERE email = ?
        ");
        $stmt->execute([
            $franchise_id,
            $data['email']
        ]);
    }

    public static function deactivate(PDO $pdo, int $id)
    {
        $stmt = $pdo->prepare("
            UPDATE franchises
            SET actif = 0
            WHERE id = ?
        ");
        $stmt->execute([$id]);
    }
}


