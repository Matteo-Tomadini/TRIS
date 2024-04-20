<?php
// Connessione al database
$conn = new mysqli("localhost", "tom_mat_user_1", "100-Arriverei", "tom_mat_db_1");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Creazione della tabella se non esiste
$sql = "CREATE TABLE IF NOT EXISTS UTENTI (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(30) NOT NULL,
    in_attesa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// Pulizia del database degli utenti in attesa
$sql = "DELETE FROM UTENTI WHERE in_attesa = TRUE AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$conn->query($sql);

$conn->close();
?>
