<?php
// Connessione al database
$conn = new mysqli("localhost", "tom_mat_user_1", "100-Arriverei", "tom_mat_db_1");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query per eliminare tutti gli utenti
$sql = "DELETE FROM utenti";
if ($conn->query($sql) === TRUE) {
    echo "All users deleted successfully.";
} else {
    echo "Error deleting users: " . $conn->error;
}

// Chiudi la connessione
$conn->close();
?>