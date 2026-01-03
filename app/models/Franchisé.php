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
            $franchise_id = $franchise['id'];
        } 
        // ❌ Existe déjà et actif → erreur
        elseif ($franchise && $franchise['actif']) {
            throw new Exception("Ce franchisé existe déjà");
        } 
        // 🆕 Création franchisé
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

        // 🔗 Lier ou créer le user associé
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        if ($user) {
            // lier si déjà existant
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
            // créer user avec mdp par défaut "test"
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

    // Méthode pour désactiver une franchise (au lieu de delete)
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



