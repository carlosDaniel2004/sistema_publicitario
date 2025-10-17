<?php
$contrasena_plana = 'admin123';
$hash = password_hash($contrasena_plana, PASSWORD_DEFAULT);
echo "Copia este hash y pégalo en la base de datos:<br><br>";
echo "<strong>" . $hash . "</strong>";
?>
