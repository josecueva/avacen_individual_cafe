<?php
include ("cabecera.php");

		$SQL="SELECT * FROM pagos where lote='".$_GET["lote"]."'";
		$res_pago=mysqli_query($link, $SQL);
		$cuenta=mysqli_num_rows($res_pago);
		if($cuenta==0){
			$pago["exportable"]="<h4><font color=red>Pendiente</font></h4>";
			$pago["descarte"]="<h4><font color=red>Pendiente</font></h4>";
			$pago["calidad"]="<h4><font color=red>Pendiente</font></h4>";
			$total="<h4><font color=red>Pendiente</font></h4>";}
		else{$pago = mysqli_fetch_array($res_pago,MYSQLI_ASSOC);
			$total=$pago["exportable"]+$pago["descarte"]+$pago["calidad"];}

		$SQL="SELECT * FROM lotes where codigo_lote='".$_GET["lote"]."'";
		$res_lote=mysqli_query($link, $SQL);
		$lote = mysqli_fetch_array($res_lote,MYSQLI_ASSOC);
		$pilado=round((($lote["peso"]*(1-($lote["humedad"])/100))/0.88)*($lote["rto_pilado"])/100,2);
		$exportable=round($pilado*($lote["rto_exportable"]/100),2);
		$descarte=round($pilado-$exportable,2);

		if($lote["apto_cata"]==0){$cata["puntuacion"]="NO APTO";$input_q="NO APTO<input type='hidden' name=calidad value='0'>";}
		else{
		$SQL="SELECT * FROM catas where lote='".$_GET["lote"]."'";
		$res_cata=mysqli_query($link, $SQL);
		$cuenta_catas=mysqli_num_rows($res_cata);
		if($cuenta_catas==0){$cata["puntuacion"]="PEND";$input_q="PENDIENTE DE CATA<input type='hidden' name=calidad value='0'>";}
		else{$cata = mysqli_fetch_array($res_cata,MYSQLI_ASSOC);$input_q="$<input type='text' name=calidad value='".$pago["calidad"]."'>";
		if($cata["puntuacion"]<=84){$input_q="NO APTO<input type='hidden' name=calidad value='0'>";}}
		}


if(isset ($_POST["calidad"])){
	
$SQL_edit="UPDATE pagos SET 
				calidad='".$_POST["calidad"]."' 
				WHERE lote='".$_GET["lote"]."'";
$resultado=mysqli_query($link, $SQL_edit);

//echo "$SQL_edit";
//para el historial
$cadena=str_replace("'", "", $SQL_edit);
guarda_historial($cadena);


echo "<div align=center><h1>GUARDANDO, ESPERA...
<meta http-equiv='Refresh' content='2;url=pagos.php'></font></h1></div>";
	
}


else{
	

echo "<div align=center><h1>AÑADIR PAGO POR CALIDAD</h1><br>";

//muestra_array($socio);

echo "<form name=form action=".$_SERVER['PHP_SELF']."?lote=".$_GET["lote"]." method='post'>";
echo "<table class=tablas>";
echo "<tr><th><h4>Lote</th><td colspan=2><h4>".$_GET["lote"]."</td></tr>";
echo "<tr><th><h4>Fecha</th><td colspan=2>".$pago["fecha"]."</td></tr>";
echo "<tr><th><h4>Exportable</th><td>$exportable qq</td><td>$".$pago["exportable"]."</td></tr>";
echo "<tr><th><h4>Descarte</th><td>$descarte qq</td><td>$".$pago["descarte"]."</td></tr>";
echo "<tr><th><h4>Extra por calidad</th><td>".$cata["puntuacion"]."</td><td>$input_q</td></tr>";
echo "</table><br>";
echo "<input type='hidden' name=codigo_lote value='".$_GET["lote"]."'>";
echo "<input type='submit' value='Actualizar'>";
echo "</form>";
}

include("pie.php");
?>