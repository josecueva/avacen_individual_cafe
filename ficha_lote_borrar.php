<?php
include ("cabecera.php");



if(isset ($_GET["lote"]) AND isset($_GET["borra"])){
	
$SQL_edit="DELETE FROM lotes WHERE id=".$_GET["lote"];
$resultado=mysqli_query($link, $SQL_edit);

$cadena=str_replace("'", "", $SQL_edit);
guarda_historial($cadena);
//echo "$SQL_edit";

echo "<div align=center><h1>BORRANDO, ESPERA...
<meta http-equiv='Refresh' content='2;url=lotes.php?criterio=socio&socio=".$_GET["socio"]."'></font></h1></div>";
	
}


else{
	


//muestra_array($socio);
$SQL="SELECT * FROM lotes where id=".$_GET["lote"];
$resultado=mysqli_query($link, $SQL);
$lote = mysqli_fetch_array($resultado,MYSQLI_ASSOC);

echo "<div align=center><h1>Borrar el lote</h1><br><h2>".nombre_socio($lote["id_socio"])."<br>".$lote["fecha"]."<br>".$lote["peso"]."kg </h2><br><br>";

echo "<notif>¿ESTA SEGURO?</notif><br><br>";

echo "<table class=tablas><tr>";
echo "<td width=50%><a href=ficha_lote_borrar.php?lote=".$lote["id"]."&borra=1&socio=".$lote["id_socio"]."><notifsi>SI</notifsi></a></td>";
echo "<td width=50%><a href=socios.php><notifno>NO</notifno></a></td>";
echo "</tr></table>";

}
include("pie.php");
?>