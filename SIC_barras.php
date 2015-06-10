<?php
$link = mysqli_connect("localhost", "root", "", "sig");
mysqli_query($link, "SET NAMES 'utf8'");
require_once ('src/jpgraph.php');
require_once ('src/jpgraph_bar.php');

//*****************************************************SQLS*****************************************************
	switch ($_GET["limit"])
		{
		case "top10":
			$limit= "LIMIT 10";
			$limite= "Top 10";
			break;
		case "todos":
			$limit="";
			$limite="Todos";
		}

	switch ($_GET["criterio"])
		{
		case "produccion":
			if($_GET["agrupacion"]=="socio")
			{
			$sql="SELECT CONCAT_WS(' ',socios.nombres,socios.apellidos) as socio, socios.codigo as codigo, socios.poblacion as zona, ROUND(".$_GET["operador"]."(lotes.peso),2) as produccion FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.codigo order by produccion ".$_GET["orden"]." $limit";
			}
			if($_GET["agrupacion"]=="zona")
			{
			$sql="SELECT socios.poblacion as zona, ROUND(".$_GET["operador"]."(lotes.peso),2) as produccion FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.poblacion order by produccion ".$_GET["orden"]." $limit";
			}
			break;

		case "cata":	
			if($_GET["agrupacion"]=="socio")
			{
			$sql="SELECT CONCAT_WS(' ',socios.nombres,socios.apellidos) as socio, socios.codigo as codigo, socios.poblacion as zona, ROUND(".$_GET["operador"]."(catas.puntuacion),2) as cata FROM catas LEFT JOIN lotes ON catas.lote=lotes.codigo_lote LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.codigo ORDER BY cata ".$_GET["orden"]." $limit";
			}
			if($_GET["agrupacion"]=="zona")
			{
			$sql="SELECT socios.poblacion as zona, ROUND(".$_GET["operador"]."(catas.puntuacion),2) as cata FROM catas LEFT JOIN lotes ON catas.lote=lotes.codigo_lote LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.poblacion ORDER BY cata ".$_GET["orden"]." $limit";
			}
			break;

		case "humedad":	
			if($_GET["agrupacion"]=="socio")
			{
			$sql="SELECT CONCAT_WS(' ',socios.nombres,socios.apellidos) as socio, socios.codigo as codigo, socios.poblacion as zona, ROUND(".$_GET["operador"]."(lotes.humedad),2) as humedad FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.codigo order by humedad ".$_GET["orden"]." $limit";
			}
			if($_GET["agrupacion"]=="zona")
			{
			$sql="SELECT socios.poblacion as zona, ROUND(".$_GET["operador"]."(lotes.humedad),2) as humedad FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.poblacion order by humedad ".$_GET["orden"]." $limit";
			}
			break;
									
		case "descarte_prc":	
			if($_GET["agrupacion"]=="socio")
			{
			$sql="SELECT CONCAT_WS(' ',socios.nombres,socios.apellidos) as socio, socios.codigo as codigo, socios.poblacion as zona, ROUND(".$_GET["operador"]."(100*lotes.rto_descarte/(lotes.rto_descarte+lotes.rto_exportable)),2) as descarte_prc FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.codigo order by descarte_prc ".$_GET["orden"]." $limit";
			}
			if($_GET["agrupacion"]=="zona")
			{
			$sql="SELECT socios.poblacion as zona, ROUND(".$_GET["operador"]."(100*lotes.rto_descarte/(lotes.rto_descarte+lotes.rto_exportable)),2) as descarte_prc FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.poblacion order by descarte_prc ".$_GET["orden"]." $limit";
			}
			break;
									
		case "exportable_prc":	
			if($_GET["agrupacion"]=="socio")
			{
			$sql="SELECT CONCAT_WS(' ',socios.nombres,socios.apellidos) as socio, socios.codigo as codigo, socios.poblacion as zona, ROUND(".$_GET["operador"]."(100*lotes.rto_exportable/(lotes.rto_descarte+lotes.rto_exportable)),2) as exportable_prc FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.codigo order by exportable_prc ".$_GET["orden"]." $limit";
			}
			if($_GET["agrupacion"]=="zona")
			{
			$sql="SELECT socios.poblacion as zona, ROUND(".$_GET["operador"]."(100*lotes.rto_exportable/(lotes.rto_descarte+lotes.rto_exportable)),2) as exportable_prc FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.poblacion order by exportable_prc ".$_GET["orden"]." $limit";
			}
			break;
									
		case "rto_trillado":	
			if($_GET["agrupacion"]=="socio")
			{
			$sql="SELECT CONCAT_WS(' ',socios.nombres,socios.apellidos) as socio, socios.codigo as codigo, socios.poblacion as zona, ROUND(".$_GET["operador"]."(100*(lotes.rto_descarte+lotes.rto_exportable)/".$config["gr_muestra"]."),2) as rto_trillado FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.codigo order by rto_trillado ".$_GET["orden"]." $limit";
			}
			if($_GET["agrupacion"]=="zona")
			{
			$sql="SELECT socios.poblacion as zona, ROUND(".$_GET["operador"]."(100*(lotes.rto_descarte+lotes.rto_exportable)/".$config["gr_muestra"]."),2) as rto_trillado FROM lotes LEFT JOIN socios ON lotes.id_socio=socios.codigo GROUP BY socios.poblacion order by rto_trillado ".$_GET["orden"]." $limit";
			}
			break;
									
		}
//****************FIN SQLS********************************************************************************
$resultado=mysqli_query($link, $sql);
while($datos = mysqli_fetch_array($resultado,MYSQLI_ASSOC)){
	//muestra_array($datos);
		$x[]=$datos[$_GET["agrupacion"]];
		$y[]=$datos[$_GET["criterio"]];
		}

$graph = new Graph(800,600,'auto');
$graph->SetScale("intlin",floor(min($y)),ceil(max($y)));

// Create the graph. These two calls are always required

//$graph->yaxis->SetTickPositions(array(0,30,60,90,120,150), array(15,45,75,105,135));
$graph->SetBox(false);

$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels($x);
$graph->xaxis->SetLabelAngle(90);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,8);
$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,10);
$graph->legend->SetAbsPos(10,10,'right','top');

$b1plot = new BarPlot($y);

// ...and add it to the graPH
$graph->Add($b1plot);

$b1plot->SetColor("white");
$b1plot->SetFillColor("skyblue");
$b1plot->SetLegend($_GET["criterio"]);

$graph->Stroke();
?>