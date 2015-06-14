<?php
	//Implementada en linea 11 y 19 de envios.php
	function busquedas($criterio,$post){		
		require("conect.php");   				
    	$SQL="call SP_envios_con('".$criterio."','".$post."')";//Procedimiento Almacenado
    	$result=mysqli_query($link,$SQL);   		
    	$_texto=$post;
		return array($result,$_texto);    	
	}
	
	//Implementada en linea 23 de envios.php
	function resultado_sentencias($SQL){	
		require("conect.php");   	
		$cuenta=mysqli_num_rows($SQL);
		while ($row = mysqli_fetch_array($SQL,MYSQLI_ASSOC)){
			$envios[]=$row;	
		}
		return array($SQL,$cuenta,$envios);
	}

	//Implementada en linea 33 de envios.php
	function catalogo_fechas($criterio,$post){		
		require("conect.php");   	
		$SQL="call SP_envios_con('".$criterio."','".$post."')";//Procedimiento Almacenado		
		$r_fec=mysqli_query($link, $SQL);
		while ($rowfec = mysqli_fetch_array($r_fec,MYSQLI_ASSOC)){
			$fecha[]=$rowfec["fecha"];
		}
		return $fecha;
	}

	//Implementada en linea 63 de envios.php
	function presentacion_datos($envio,$criterio){			
		require("conect.php");   		
		$SQL="call SP_envios_con('".$criterio."','".$envio."')";//Procedimiento Almacenado
		$resultado=mysqli_query($link, $SQL);
		$cuenta_despachos=mysqli_num_rows($resultado);
		if($cuenta_despachos==0){
			$contenido[]="ENVIO SIN CONTENIDO";$cantidades[]=0;
		}
		else{
		while ($row = mysqli_fetch_array($resultado,MYSQLI_ASSOC))
				{$contenido[]=$row["cantidad"]." de ".$row["lote"];
				 $cantidades[]=$row["cantidad"];
				}
		}
		return array($contenido,$cantidades);			
	}

	//Implementada en linea 7 de ficha_envio_nuevo.php
	function ingresar_nuevo_envio($fecha,$destino,$chofer,$responsable)	{	
		require("conect.php");   				
		$SQL_edit="call SP_envios_ins(
			'".$fecha."',
			'".$destino."',
			'".$chofer."',
			'".$responsable."')";//Procedimientos Almacenado;
		$resultado=mysqli_query($link, $SQL_edit);
		$nuevo_id=mysqli_insert_id($link);
		$cadena=str_replace("'", "", $SQL_edit);
		return array($cadena, $resultado, $nuevo_id);
	}

	//Implementada en linea 6 de ficha_envio_editar.php
	function editar_envio_presentar($envio,$criterio)	{	
		require("conect.php");   		
		$SQL="call SP_envios_con('".$criterio."','".$envio."')";//Procedimiento Almacenado
		$resultado=mysqli_query($link, $SQL);
		$cuenta=mysqli_num_rows($resultado);
		$envio = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
		return array($resultado,$cuenta,$envio);
	}

	//Implementada en linea 9 de ficha_envio_editar.php
	function editar_envio_actualizar($fecha,$destino,$chofer,$responsable,$envio){	
		require("conect.php"); 
		$SQL_edit="call SP_envios_upd(
			'".$fecha."',
			'".$destino."',
			'".$chofer."',
			'".$responsable."',
			'".$envio."')";//Procedimientos Almacenado;

		$resultado=mysqli_query($link, $SQL_edit) or die(mysqli_error($link));
		$nuevo_id=mysqli_insert_id($link);
		$cadena=str_replace("'", "", $SQL_edit);
			return array($resultado,$nuevo_id,$cadena);
	}

	//Implementada en linea 8 de ficha_envio.php
	function ficha_envio_presentar($envio,$criterio)
		{	
			require("conect.php");
			$SQL="call SP_envios_con('".$criterio."','".$envio."')";//Procedimiento Almacenado
			$resultado=mysqli_query($link, $SQL);
			$row = mysqli_fetch_array($resultado,MYSQLI_ASSOC);
			$envios=$row;
			return $envios;
		}
	//Implementada en linea 17 de ficha_envio.php
	function ficha_envio_presentar2($envio,$criterio)
		{	
			require("conect.php");
			$SQL="call SP_envios_con('".$criterio."','".$envio."')";//Procedimiento Almacenado
			$resultado=mysqli_query($link, $SQL);
			$cuenta_despachos=mysqli_num_rows($resultado);			
			if($cuenta_despachos==0){
				$contenido[]="ENVIO SIN CONTENIDO";
				$cantidades[]=0;
				return array(null,$contenido,$cantidades,null,null);				
			}else{				
				while ($row = mysqli_fetch_array($resultado,MYSQLI_ASSOC))
					{
					 $ids[]=$row["id"];
					 $contenido[$row["id"]]["lote"]=$row["codigo_lote"];
					 $contenido[$row["id"]]["cantidad"]=$row["cantidad"];
					 $contenido[$row["id"]]["codigo"]=$row["codigo"];
					 $contenido[$row["id"]]["nombres"]=$row["nombres"];
					 $contenido[$row["id"]]["apellidos"]=$row["apellidos"];
					 $contenido[$row["id"]]["poblacion"]=$row["grupo"];
					 $contenido[$row["id"]]["humedad"]=$row["humedad"];
					 $contenido[$row["id"]]["puntuacion"]=$row["puntuacion"];
					 $cantidades[]=$row["cantidad"];
					 $puntuaciones[]=$row["puntuacion"];
					 $humedades[]=$row["humedad"];
				}
				return array($ids,$contenido,$cantidades,$puntuaciones,$humedades);
			}				
		}
		
		//Implementada en linea 51 de ficha_envio.php
		function ficha_envio_calculo($puntuaciones,$humedades){
			if(count(array_filter($puntuaciones))>0){
				$media_puntuaciones=round(array_sum($puntuaciones)/count(array_filter($puntuaciones)),2);
				}else{
					$media_puntuaciones=$puntuaciones[0];
			}
			if(count(array_filter($humedades))>0){
				$media_humedades=round(array_sum($humedades)/count(array_filter($humedades)),2);
				}else{
					$media_humedades=$humedades[0];
			}
			return array ($media_humedades,$media_puntuaciones);
		}
?>