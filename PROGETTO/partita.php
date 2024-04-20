<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET["opponent_id"])) {
    // Se l'ID dell'avversario non è presente nei parametri GET, reindirizza alla pagina lista.php
    header("Location: lista.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$opponent_id = $_GET["opponent_id"];

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

// Ottieni il nickname dell'avversario
$stmt = $conn->prepare("SELECT nickname FROM UTENTI WHERE id = ?");
$stmt->bind_param("i", $opponent_id);
$stmt->execute();
$stmt->bind_result($opponent_nickname);
$stmt->fetch();
$stmt->close();

// Logica del gioco di Tris
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cell_index"])) {
    $cell_index = $_POST["cell_index"];

    // Salvataggio del movimento nel database (da completare con la tua logica di salvataggio)
    // ...

    // Controllo dello stato del gioco e determinazione del vincitore (da completare con la tua logica di gioco)
    // ...
}
?>


<?php


if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET["opponent_id"])) {
    // Se l'ID dell'avversario non è presente nei parametri GET, reindirizza alla pagina lista.php
    header("Location: lista.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$opponent_id = $_GET["opponent_id"];

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

// Ottieni il nickname dell'avversario
$stmt = $conn->prepare("SELECT nickname FROM UTENTI WHERE id = ?");
$stmt->bind_param("i", $opponent_id);
$stmt->execute();
$stmt->bind_result($opponent_nickname);
$stmt->fetch();
$stmt->close();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET["opponent_id"])) {
    header("Location: lista.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$opponent_id = $_GET["opponent_id"];

$conn = new mysqli("localhost", "tom_mat_user_1", "100-Arriverei", "tom_mat_db_1");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

function controllaVincitore($conn, $user_id, $opponent_id) {
    $winning_combinations = array(
        array(0, 1, 2), array(3, 4, 5), array(6, 7, 8), // Righe
        array(0, 3, 6), array(1, 4, 7), array(2, 5, 8), // Colonne
        array(0, 4, 8), array(2, 4, 6) // Diagonali
    );

    $symbols = array('X', 'O');

    foreach ($symbols as $symbol) {
        foreach ($winning_combinations as $combination) {
            $count = 0;
            foreach ($combination as $cell_index) {
                $result = 0; // Inizializza $result a 0
                $stmt = $conn->prepare("SELECT COUNT(*) FROM partita WHERE (p1 = ? OR p2 = ?) AND cell_index = ? AND symbol = ?");
                $stmt->bind_param("iiis", $user_id, $user_id, $cell_index, $symbol);
                $stmt->execute();
                $stmt->bind_result($result);
                $stmt->fetch();
                $count += $result;
                $stmt->close();
            }
            if ($count === 3) {
                return true; // C'è un vincitore
            }
        }
    }

    return false; // Nessun vincitore
}

function controllaPareggio($conn) {
   
$num_moves = 0; // Inizializza $num_moves a 0
    $stmt = $conn->prepare("SELECT COUNT(*) FROM partita");
    $stmt->execute();
    $stmt->bind_result($num_moves);
    $stmt->fetch();
    $stmt->close();

    return $num_moves >= 9; // Se ci sono stati almeno 9 movimenti, la partita è finita in pareggio
}

// Logica del gioco di Tris
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cell_index"])) {
    $cell_index = $_POST["cell_index"];

    // Controllo se la cella è già occupata
    $stmt = $conn->prepare("SELECT COUNT(*) FROM partita WHERE cell_index = ?");
    $stmt->bind_param("i", $cell_index);
    $stmt->execute();
    $stmt->bind_result($occupied);
    $stmt->fetch();
    $stmt->close();
    if ($occupied > 0) {
        echo "Questa cella è già occupata.";
        exit();
    }

    // Salva il movimento nel database
    $stmt = $conn->prepare("INSERT INTO partita (p1, p2, cell_index, symbol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $opponent_id, $cell_index, $_SESSION["symbol"]);
    $_SESSION["symbol"] = ($_SESSION["symbol"] == 'X') ? 'O' : 'X'; // Alterna i simboli dei giocatori
    $stmt->execute();
    $stmt->close();

    // Controlla se c'è un vincitore
    $winner = controllaVincitore($conn, $user_id, $opponent_id);
    if ($winner) {
        echo "Hai vinto!";
        exit();
    }

    // Controlla se la partita è finita in pareggio
    $draw = controllaPareggio($conn);
    if ($draw) {
        echo "La partita è finita in pareggio.";
        exit();
    }

    echo "Movimento registrato correttamente.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tris</title>
    <style>
        .board {
            display: grid;
            grid-template-columns: repeat(3, 100px);
            grid-template-rows: repeat(3, 100px);
            gap: 2px;
            background-color: #f0f0f0;
        }
        .cell {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            cursor: pointer;
            font-size: 24px;
            border: 2px solid #000000;
        }
        .cell:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
<h1>Partita contro <?php echo $opponent_nickname; ?></h1>
    <p>Tuo Nickname: <?php echo $nickname; ?></p>
    <p>Nickname Avversario: <?php echo $opponent_nickname; ?></p>
    
    <div class="board" id="board">
        <!-- Create the cells dynamically using JavaScript -->
    </div>

    <script>
        // Initialize the game board
        const board = document.getElementById("board");
        const cells = [];

        // Create the cells and add click event listeners
        for (let i = 0; i < 9; i++) {
            const cell = document.createElement("div");
            cell.classList.add("cell");
            cell.dataset.index = i;
            cell.addEventListener("click", handleCellClick);
            cells.push(cell);
            board.appendChild(cell);
        }

        // Track the current player (X or O)
        let currentPlayer = "X";

        // Function to handle cell clicks
        function handleCellClick() {
            if (!this.textContent) {
                this.textContent = currentPlayer;
                checkForWinner();
                currentPlayer = currentPlayer === "X" ? "O" : "X";
            }
        }

        // Function to check for a winner
        function checkForWinner() {
            const winningCombinations = [
                [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
                [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
                [0, 4, 8], [2, 4, 6] // Diagonals
            ];

            for (const combination of winningCombinations) {
                const [a, b, c] = combination;
                if (cells[a].textContent && cells[a].textContent === cells[b].textContent && cells[a].textContent === cells[c].textContent) {
                    alert(`${cells[a].textContent} wins!`);
                    resetBoard();
                    return;
                }
            }

            // Check for a draw
            if ([...cells].every(cell => cell.textContent)) {
                alert("It's a draw!");
                resetBoard();
            }
        }

        // Function to reset the board
        function resetBoard() {
            cells.forEach(cell => cell.textContent = "");
        }

        
        setTimeout(deleteUsers, 10000);
        header("Location: delete_users.php");

       
       
        
    </script>

    
</body>
</html>
