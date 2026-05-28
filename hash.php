<?php

$hash = password_hash("12345", PASSWORD_BCRYPT);
echo $hash;

if (password_verify("hola", $hash)) {
echo "<p>Iguales</p>";
} else {
echo "<p>Distintos</p>";
}

?>
