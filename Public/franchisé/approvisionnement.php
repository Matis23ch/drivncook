<?php
session_start();
$franchise_id = $_SESSION['franchise_id'];

$taux_dc = 78; // simulé
?>
<!DOCTYPE html>
<html>
<body class="container">
<h2>Approvisionnement</h2>

<p>Taux Driv’n Cook : <?= $taux_dc ?>%</p>

<?php if ($taux_dc < 80): ?>
<div class="alert alert-danger">
Non conforme au contrat
</div>
<?php else: ?>
<div class="alert alert-success">
Contrat respecté
</div>
<?php endif; ?>
</body>
</html>
