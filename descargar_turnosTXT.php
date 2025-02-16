<?php
session_start();

if (empty($_SESSION['Usuario_Nombre']) ) { // si el usuario no esta logueado no lo deja entrar
  header('Location: cerrarsesion.php');
  exit;
}

//creo la carpeta de logs, donde voy a crear mi archivo

if (!is_dir('logs')) {     //pregunto si no existe el directorio
    mkdir('logs');         //si no existe, lo creo
    chmod('logs', 0777);   //le doy permisos de lectura/escritura
}
//variables a utilizar
$FechaHoraHoy = date('Ymd_His');
// Incluye fecha y hora con segundos para que sea único
// Mensaje a escribir en el archivo:
if (!empty($_SESSION['Descarga'])) {
  $ArchivoLog = fopen("logs/Lista_Turnos_$FechaHoraHoy.log", 'x+');
  // Usa 'x+' para crear un archivo nuevo
  fwrite($ArchivoLog, $_SESSION['Descarga']);
  fclose($ArchivoLog);
  $_SESSION['Mensaje'] = "<strong>Se ha guardado el .txt de turnos.</strong> Puedes verlo <a href='logs/Lista_Turnos_$FechaHoraHoy.log' target='_blank'>aquí</a>"; 
} else { $_SESSION['Mensaje'] = "No hay datos para guardar en el archivo."; 
}

header('Location: listados_turnos.php');
  exit;
?>


