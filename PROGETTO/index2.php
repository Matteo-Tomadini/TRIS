<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nickname"])) {
    $nickname = $_POST["nickname"];
    $conn = new mysqli("localhost", "tom_mat_user_1", "100-Arriverei", "tom_mat_db_1");
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }
    $stmt = $conn->prepare("INSERT INTO UTENTI (nickname, in_attesa, created_at) VALUES (?, 1, NOW())");
    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();
    $_SESSION["user_id"] = $user_id;
    header("Location: lista.php"); // Redirect alla pagina lista.php dopo aver creato l'utente
    exit();
}
?>
