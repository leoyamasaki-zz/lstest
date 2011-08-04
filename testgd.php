<?php
// Establecer el tipo de contenido
header('Content-Type: image/png');

// Crear la imagen
$im = imagecreatetruecolor(400, 30);

// Crear algunos colores
$blanco = imagecolorallocate($im, 255, 255, 255);
$gris = imagecolorallocate($im, 128, 128, 128);
$negro = imagecolorallocate($im, 0, 0, 0);
imagefilledrectangle($im, 0, 0, 399, 29, $blanco);

// El texto a dibujar
$texto = 'Testing...';
// Reemplace la ruta por la de su propia fuente
$fuente = 'arial.ttf';

// Añadir algo de sombra al texto
imagettftext($im, 20, 0, 11, 21, $gris, $fuente, $texto);

// Añadir el texto
imagettftext($im, 20, 0, 10, 20, $negro, $fuente, $texto);

// Usar imagepng() resultará en un texto más claro comparado con imagejpeg()
imagepng($im);
imagedestroy($im);
?>