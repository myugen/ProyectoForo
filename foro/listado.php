<?php
session_start();
if(!isset($_GET["idTema"]))
	header("Location: ../foro.php");
if(!isset($_SESSION["usuario"]) && !isset($_SESSION["tipo"])) {
	$usuario = "anónimo";
	$id = 0;
	$tipo = "invitado";
}
else {
	$usuario = $_SESSION["usuario"];
	$tipo = $_SESSION["tipo"];
	$id = $_SESSION["id"];
}
require '../database/conexion.php';
$idTema = $_GET["idTema"];
if(!$conexion) {
	die("Error de conexión $conexion->errno: $conexion->error");
}
else {
	$peticion = "SELECT m.ID as idMensaje, m.usuarioID as idUsuario, u.usuario as usuario, m.opinion as opinion, m.fechahora as fecha
				 FROM mensaje as m
				 LEFT JOIN usuario as u
				 ON (m.usuarioID = u.ID)
				 WHERE m.temaID = $idTema";
}
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/rebel.png" />
    <!-- Font Awesone Icons -->
    <link rel="stylesheet" href="../assets/font-awesome-4.5.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="../estilo/main.css">
    <!-- SCRIPTS -->
    <script type="text/javascript" src="../scripts/menu.js"></script>
    <title>Opiniones - MiForo</title>
</head>
<body onload="cargar()">
    <div class="container">
    	<div class="navmenu">
    	    <div class="menu-bar">
    		    <a href="#" onclick="desplegarMenu()"><span class="fa fa-bars fa-fw"></span>&nbsp;Menú</a>
    		</div>
    		<a class="logo" href="../index.php"><span class="fa fa-ra fa-fw" aria-hidden="true"></span>
    			MiForo
    		</a>
  			<ul class="menu-izquierda" id="menu">
	    		<li>
	      			<a href="../index.php">Inicio</a>
	    		</li>
	    		<li class="activo">
	      			<a href="../foro.php">Foro</a>
	    		</li>
    		</ul>
    		<ul class="menu-derecha">
    			<?php
    			//Mensaje especial en la zona de usuario
    			if($usuario == "anónimo")
    				echo "<li><p>Bienvenido, $usuario</p></li>";
    			else {
    				echo "<li>
    						<a id='dropdownMenu'>
      							Bienvenido, $usuario &nbsp<span class='fa fa-caret-down'></span>
    						</a>
							<ul class='menu-oculto'>
    							<li><a href='../administracion/cambiar_pass.php'>Cambiar contraseña</a></li>
    							<li><a href='../administracion/cerrar_sesion.php'>Cerrar sesión</a></li>
  							</ul>
    					  </li>";
    			}
    			?>
    		</ul>
    	</div>
    	<div style="padding-top: 70px">
    		<div class="container">
    			<?php
    			$resultado = $conexion->query($peticion);
    			if(!$resultado) {
    				echo "<div class='alert alert-danger' role='alert'><strong>Error en la base de datos</strong>, imposible obtener consulta.</div>";
    			}
    			else {
    				$numeroFilas = $resultado->num_rows;
    				echo "<div class='panel panel-principal'>";
    				echo "<div class='panel-cabecera'>
    						<div class='columna-3-4'>
    						Opiniones <span class='badge'>$numeroFilas</span>
    						</div>";
    				if($usuario != "anónimo") {
    					echo "<div class='columna-1-4'><a href='crear_opinion.php?idTema=$idTema' role='button'>
                                <button class='boton-principal'>
								<span class='fa fa-edit' aria-hidden='true'></span>
								Nueva opinión
                                </button>
								</a></div>";
    				}
    				echo "</div>";
    				echo "<ul class='lista'>";
    				if ($numeroFilas == 0) {
    					echo "<li class='lista-elemento list-group-warning'>No hay opiniones</li>";
    				}
    				else {
    					while($fila = $resultado->fetch_array()) {
    						echo "<li class='lista-elemento'>";
    						echo "<div class='columna'>";
    						echo "<span>" . utf8_encode(nl2br($fila['opinion'])) . "</span>";
    						echo "</div>";
    						echo "<hr>";
    						echo "<div class='row'>";
    						echo "<div class='columna-1-4' style='text-align: left'>";
    						echo  "<span>Autor: <strong>". utf8_encode($fila["usuario"]) . "</strong></span>";
    						echo "</div>";
    						echo "<div class='columna-1-4' style='text-align: center'>";
    						echo  "<span>Creado: <strong>". utf8_encode($fila["fecha"]) . "</strong></span>";
    						echo "</div>";
    						if($id == $fila["idUsuario"] || $tipo == "administrador") {
	    						echo "<div class='columna-1-4' style='text-align: right'>";
	    						echo "<a href='editar_opinion.php?idTema=$idTema&idOpinion=" . $fila["idMensaje"] . "'>
                                        <button class='boton-aviso'>
										<span class='fa fa-pencil' aria-hidden='true'></span>&nbsp
										Editar opinión
                                        </button>
									  </a>";
	    						echo "</div>";
	    						echo "<div class='columna-1-4' style='text-align: right'>";
	    						echo "<a href='borrar_opinion.php?idOpinion=" . $fila["idMensaje"] . "'>
                                        <button class='boton-peligro'>
										<span class='fa fa-remove' aria-hidden='true'></span>&nbsp
										Eliminar opinión
                                        </button>
									  </a>";
	    						echo "</div>";
    						}
    						echo "</div>";
    						echo "</li>";
    					}
    				}
    				echo "</ul>";
    				echo "</div>";
    				$conexion->close();
    			}
    			?>
    		</div>
    	</div>
    </div>
</body>
</html>
