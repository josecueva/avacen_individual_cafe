<?php
include ("cabecera.php");
include ("parcelas_funciones.php");
include ("certificaciones_funciones.php");
include ("grupos_funciones.php");
include ("subparcelas_funciones.php");
include ("analisis_funciones.php");
include ("asociaciones_funciones.php");
include ("socio.php");

if(!isset($_GET["criterio"]))
{
	$parcelas=parcelas_consultarCriterio("","");
	$criterio="";
	$encontrados="";

}else{
	$encontrados="ENCONTRADOS";
	if (!isset($_POST["busca"])) {
		$parcelas=parcelas_consultarCriterio($_GET["criterio"],"");
	}
	else{	
		$parcelas=parcelas_consultarCriterio($_GET["criterio"],$_POST["busca"]);
	}
$criterio="<h4>Criterio de búsqueda: <b>".$_GET["criterio"]."</b></h4>";		
}

if (is_array($parcelas)) {
	$cuenta=count($parcelas);
	foreach ($parcelas as $parcela)
	{
		$superficie_cafe[]=$parcela["sup_cafe"];
		if($parcela["coorX"]!=0 && $parcela["coorY"]!=0 && $parcela["alti"]!=0){
		$coordenadas[$parcela["id"]]="<img valign=top title='UTM ".$parcela["coorX"]."-".$parcela["coorY"]."\n(".$parcela["alti"]." msnm)' width=25 src=images/gps_si.png>";
		}	
	else{
		$coordenadas[$parcela["id"]]="<img valign=top title='Sin coordenadas GPS' width=25 src=images/gps_no.png>";
		}
		$subparcelas=consultarSubparcelas($parcela["id"]);

		foreach ($subparcelas as $subparcela) {
			if($subparcela["variedad"]!=""){
				$variedades[$parcela["id"]][]=$subparcela["variedad"];
			}
				if($subparcela["variedad2"]!=""){
					$variedades[$parcela["id"]][]=$subparcela["variedad2"];
				}
				if($subparcela["siembra"]=="1900"){
					$subparcela["siembra"]="hace más de 15 años";
				}else{
					$subparcela["siembra"]="en ".$subparcela["siembra"];
				}
				if($subparcela["siembra"]!=""){
					$siembras[$parcela["id"]][]=$subparcela["siembra"];
				}
				if(!isset($variedades[$parcela["id"]])){
					$variedades[$parcela["id"]][]="variedades sin especificar";
				}
				if(!isset($siembras[$parcela["id"]])){
					$siembras[$parcela["id"]][]="edad sin especificar";
				}
		}
	
	$asociaciones=asociaciones_consultar($parcela["id"]);

	if (is_array($asociaciones)) {
		foreach ($asociaciones as $asociacion ) {
		$asoc_cultivos[$parcela["id"]][]=$asociacion["concepto"];
		}
	}
	
	if(!isset($asoc_cultivos[$parcela["id"]])){
		$asoc_cultivos[$parcela["id"]][]="no existen";
	}
}
}else{
	$cuenta=$parcelas;
}



echo "<div align=center><h1>Listado de Parcelas</h1><br><br>";
echo "<table border=0 cellpadding=0 cellspacing=10><tr>";
//echo "<td align=center></td><td align=center></td><td align=center></td></tr><tr>";


echo "<td align=center><a href=parcelas.php?criterio=organico>";
echo "<img src=images/organico.png width=50><br><h4>Orgánicos</a>";
echo "</td>";

echo "<td align=center><a href=parcelas.php?criterio=no_organico>";
echo "<img src=images/noorganico.png width=50><br><h4>No Orgánicos</a>";
echo "</td>";

echo "<td align=center><h4>Socio<br>
<form name=form1 action=".$_SERVER['PHP_SELF']."?criterio=socio method='post'>";
echo "<select name=busca>";
$lista=consultarCriterio('parcelas','');
foreach ($lista as $elemento) {
	if($elemento["parcelas"]>0){
		if($elemento["parcelas"]>1){
			$lotes_t="parcelas";}
			else{
				$lotes_t="parcela";
			}
		$lotess="(".$elemento["parcelas"]." $lotes_t)";
		$mark="style='background-color:skyblue; color:blue;'";
	}else{$mark="";$lotess="";
}
	$socio_n=$elemento["codigo"]."-".$elemento["apellidos"].", ".$elemento["nombres"]." $lotess";
	echo "<option value=".$elemento["id_socio"].">$socio_n</option>";
}
echo "</select><br>";
echo "<input type='submit' value='buscar'>";
echo "</form></td>";


echo "<td align=center><h4>Grupo<br>
<form name=form2 action=".$_SERVER['PHP_SELF']."?criterio=localidad method='post'>";
echo "<select name=busca>";
$grupos=obtenerGrupos();
 foreach ($grupos as $grupo)
	{
		echo "<option value=".$grupo["grupo"].">".$grupo["grupo"]."</option>";
	}
echo "</select><br>";
echo "<input type='submit' value='filtrar'>";
echo "</form></td>";
echo "<td align=center><a href=ficha_parcela_nuevo.php>";
echo "<img src=images/add.png width=50><br><h4>nuevo</a>";
echo "</td>";


if(isset($superficie_cafe)){$total_cafe=array_sum($superficie_cafe);}else{$total_cafe="no se encuentran";}

echo "</tr></table>";

echo "<div align=center>$criterio<br>";
echo "<table class=tablas>";
	echo "<tr><th width=500px>";
	echo "<h4>PARCELAS $encontrados</h4> ($cuenta parcelas, $total_cafe ha de café)";
	echo "</th>";
	echo "<th width=20px><h6>opciones</h6></th></tr>";
if(isset($parcelas) && $cuenta>0)
{
	foreach ($parcelas as $parcela)
	{
		$datos_socio=consultarCriterio('id',$parcela["id_socio"]);		
		$estatus=certificacion($datos_socio["id_socio"]);
		if(isset($estatus)){
			$estatus_actual=max(array_keys($estatus));
			if(!is_null($estatus[$estatus_actual]["estatus"])){
				$estatus_t="<img title='socio CON certificación orgánica' src=images/organico.png width=25 valign=top>";
			}else{
				$estatus_t="<img title='socio SIN certificación orgánica' src=images/noorganico.png width=25 valign=top>";
			}
		}else{$estatus_t="";
	}
		
		//analisis de suelos*******************************************************************		
			//$sql_analisis="SELECT * FROM analisis WHERE id_subparcela in (SELECT id FROM subparcelas WHERE id_parcela=".$parcela["id"].")";
			$resultado_analisis=analisis_suelos($parcela["id"]);
			$cuenta_analisis=count($resultado_analisis);
			if($cuenta_analisis>0){
				$analisis_t="<img title='$cuenta_analisis análisis realizados' width=25 src=images/lab.png><font color=green>($cuenta_analisis)</font>";
			}else{$analisis_t="";
		}
		
		
		echo "<tr>";
		echo "<td><h4>".$datos_socio["codigo"]."-".$datos_socio["nombres"]." ".$datos_socio["apellidos"]."$estatus_t<br>
		".$datos_socio["poblacion"]."<br>
		".$coordenadas[$parcela["id"]]." <a href=ficha_parcela.php?parcela=".$parcela["id"]."><h3>Finca de ".$parcela["sup_total"]."ha<br><h4>".$parcela["sup_cafe"]." ha de café en ".count($siembras[$parcela["id"]])." subparcelas<br>
		<h6>
		".implode(", ",$variedades[$parcela["id"]])."<br>
		siembra ".implode(", ",$siembras[$parcela["id"]])."<br>
		otros aprovechamientos: ".implode(", ",$asoc_cultivos[$parcela["id"]])."<br>
		$analisis_t
		</td>";
		echo "</td>";
		echo "<td><a href=ficha_parcela_editar.php?parcela=".$parcela["id"]."><img title=editar src=images/pencil.png width=25></a>
				  <a href=ficha_parcela_borrar.php?parcela=".$parcela["id"]."><img title=borrar src=images/cross.png width=25></a>
				  </td></tr>";
				  
	}
}
else{"no hay resultados";}
echo "</table></div>";


include("pie.php");

?>