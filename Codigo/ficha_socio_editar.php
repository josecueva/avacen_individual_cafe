<?php
include ("cabecera.php");
include ("socio.php");
include ("grupos_funciones.php");
include ("canton_funciones.php");
include ("provincias_funciones.php");



if(isset ($_POST["update"])){
actualizarsocio($_POST["update"],$_POST["nombres"],$_POST["apellidos"],calcular_codigo($_POST["poblacion"]),$_POST["cedula"],$_POST["celular"],$_POST["f_nacimiento"]
,$_POST["direccion"],$_POST["poblacion"],$_POST["canton"],$_POST["provincia"],$_POST["genero"],$_POST["email"]);	



echo "<div align=center><h1>ACTUALIZANDO, ESPERA...
<meta http-equiv='Refresh' content='2;url=ficha_socio.php?user=".$_POST["update"]."'></font></h1></div>";
	
}

else{
	
$socio = consultarCriterio('id',$_GET["user"]);

if(isset($_GET["foto"])){

	$socio["foto"]=$_GET["foto"];
}

echo "<div align=center><h1>Edición de la Ficha del socio</h1><br><h2>".$socio["nombres"]." ".$socio["apellidos"]."</h2><br><br>";


echo "<form name=form1 action=".$_SERVER['PHP_SELF']." method='post'>";
if($socio["foto"]==""){
	$socio["foto"]="sinfoto.jpg";
}
echo "<a href=galery2.php?socio=".$socio["id_socio"]."><img src=fotos/th/small_".$socio["foto"]." width=100></a><br><h5>click en la foto para cambiarla</h5><br>";
echo "<table class=tablas>";
echo "<input type='hidden' name='update' value=".$_GET["user"].">";
echo "<tr><th align=right><h4>Nombres</td><td><input type='text' name=nombres value='".$socio["nombres"]."'></td></tr>";
echo "<tr><th align=right><h4>Apellidos</td><td><input type='text' name=apellidos value='".$socio["apellidos"]."'></td></tr>";
echo "<tr><th align=right><h4>Código</th><td>*se calculará automáticamente</td></tr>";
echo "<tr><th align=right><h4>Grupo</th><td>";
echo "<input list='grupos' name='poblacion'>";	
echo "<datalist  id='grupos'>";	
//echo "<option value=".$socio["poblacion"].">".$socio["poblacion"]."</option>";
 $grupos=obtenerGrupos();
 foreach ($grupos as $grupo)
	{
		echo "<option>".$grupo["grupo"]."</option>";
	}
echo "</datalist></td></tr>";
echo "<tr><th align=right><h4>Cédula</td><td><input type='text' name=cedula value='".$socio["cedula"]."'></td></tr>";
echo "<tr><th align=right><h4>Celular</td><td><input type='text' name=celular value='".$socio["celular"]."'></td></tr>";
echo "<tr><th align=right><h4>Fecha de nacimiento</td><td><input type='date' name=f_nacimiento value='".$socio["f_nacimiento"]."'>\"aaaa-mm-dd\"</td></tr>";
echo "<tr><th align=right><h4>email</td><td><input type='email' name=email value='".$socio["email"]."'></td></tr>";
echo "<tr><th align=right><h4>Dirección</td><td><input type='text' name=direccion value='".$socio["direccion"]."'></td></tr>";
//echo "<tr><th align=right><h4>Cantón</td><td><input type='text' name=canton value='".$socio["canton"]."'></td></tr>";
echo "<tr><th align=right><h4>Canton</th><td>";
echo "<input list='cantones' name='canton'>";	
echo "<datalist  id='cantones'>";	

 $cantones=listar_cantones();
 foreach ($cantones as $canton)
	{
		echo "<option>".$canton["canton"]."</option>";
	}
echo "</datalist></td></tr>";

echo "<tr><th align=right><h4>Provincias</th><td>";
echo "<input list='provincias' name='provincia'>";	
echo "<datalist  id='provincias'>";	
 $provincias=listar_provincias();
 foreach ($provincias as $provincia)
	{
		echo "<option>".$provincia["provincia"]."</option>";
	}
echo "</datalist></td></tr>";

//echo "<tr><th align=right><h4>Provincia</td><td><input type='text' name=provincia value='".$socio["provincia"]."'></td></tr>";
echo "<tr><th><h4>Género</th><td><select name=genero>
		<option value='".$socio["genero"]."'>".$socio["genero"]."</option>
		<option value='masculino'>M</option>
		<option value='femenino'>F</option>
		</select></td></tr>";
echo "</table><br>";
echo "<input type='submit' value='Guardar'>";
echo "</form>";
}

include("pie.php");
?>