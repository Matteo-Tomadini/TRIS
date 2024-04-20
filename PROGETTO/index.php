<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tris Multiplayer Online</title>
</head>
<body>
    <h1>Benvenuto al Tris Multiplayer Online!</h1>
    <form method="post" action="index2.php">
        <label for="nickname">Inserisci il tuo nickname:</label><br>
        <input type="text" id="nickname" name="nickname"><br>
        <input type="submit" value="Inizia a giocare">
    </form>
</body>
</html>
