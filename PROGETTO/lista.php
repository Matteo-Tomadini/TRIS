<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$conn = new mysqli("localhost", "tom_mat_user_1", "100-Arriverei", "tom_mat_db_1");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Ottieni il nickname dell'utente corrente
$stmt = $conn->prepare("SELECT nickname FROM UTENTI WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nickname);
$stmt->fetch();
$stmt->close();

// Cancella gli utenti in attesa che sono stati presenti da più di 1 minuto
$sql = "DELETE FROM UTENTI WHERE in_attesa = 1 AND created_at < (NOW() - INTERVAL 1 MINUTE)";
$conn->query($sql);

// Ottieni gli utenti in attesa escludendo l'utente corrente
$sql = "SELECT id,nickname,in_attesa,created_at FROM UTENTI WHERE in_attesa = 1 AND id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tris Multiplayer Online - Lista Utenti</title>
</head>
<body>
    <h1>Lista Utenti in Attesa</h1>
    <p>Nickname Utente Corrente: <?php echo $nickname; ?></p>
    <ul>
        <?php foreach ($users as $user): ?>
            <li>ID: <?php echo $user["id"]; ?> / Nickname: <?php echo $user["nickname"]; ?> / DataCreazione: <?php echo $user["created_at"]; ?> / <a href="partita.php?opponent_id=<?php echo $user["id"]; ?>">Sfida</a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>