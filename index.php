<?php

if(file_exists("datos.txt")){
  $jsonClientes = file_get_contents("datos.txt");
  $aClientes = json_decode($jsonClientes, true); //Seteado en true, devuelve array asociativo
} else {
  $aClientes = [];
}

$id = isset($_GET["id"])? $_GET["id"] : ""; //isset pregunta si esta definida la variable

$aMensaje = ["mensaje" => "", "codigo" => ""];

if(isset($_GET["do"]) && $_GET["do"] == "eliminar"){
  if($aClientes[$id]["imagen"] != ""){
    unlink("files/" . $aClientes[$id]["imagen"]);
  }
 unset($aClientes[$id]);
 $jsonClientes = json_encode($aClientes);
 file_put_contents("datos.txt", $jsonClientes);
 $id="";
 $aMensaje = ["mensaje" => "Cliente eliminado exitosamente", "codigo" => "dark"];
}

if($_POST){
  
  $dni = trim($_POST["txtDNI"]);
  $nombre = trim($_POST["txtNombre"]);
  $telefono = trim($_POST["txtTelefono"]);
  $correo = trim($_POST["txtCorreo"]);
  $nombreImagen = "";

  if($dni == "" || $nombre == "" || $telefono == "" || $correo == ""){

    $aMensaje = ["mensaje" => "Complete todos los campos", "codigo" => "danger"];

  } else {

    if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
      $nombreAleatorio = date("Ymdhmsi");
      $archivoTmp = $_FILES["archivo"]["tmp_name"];
      $nombreArchivo = $_FILES["archivo"]["name"];
      $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
      $nombreImagen = "$nombreAleatorio.$extension";
      move_uploaded_file($archivoTmp, "files/$nombreImagen");
    }

    if(isset($_GET["id"])){

      $imagenAnterior = $aClientes[$id]["imagen"];

      //Si hay una imagen anterior, se elimina en caso de subir una nueva
      if($_FILES["archivo"]["error"] === UPLOAD_ERR_OK){
        if($imagenAnterior != ""){
          unlink("files/$imagenAnterior");
        }
      }

      //Conseva la imagen si no se sube una nueva
      if($_FILES["archivo"]["error"] !== UPLOAD_ERR_OK){
        $nombreImagen = $imagenAnterior;
      }
      
      $aClientes[$id] = array(
        "dni" => $dni,
        "nombre" => $nombre,
        "telefono" => $telefono,
        "correo" => $correo,
        "imagen" => $nombreImagen
        );
        $aMensaje = ["mensaje" => "Cliente modificado exitosamente", "codigo" => "primary"];
    } else {
        $aClientes[] = array(
        "dni" => $dni,
        "nombre" => $nombre,
        "telefono" => $telefono,
        "correo" => $correo,
        "imagen" => $nombreImagen
        );
        $aMensaje = ["mensaje" => "Cliente cargado exitosamente", "codigo" => "success"];
    }

  }

  $jsonClientes = json_encode($aClientes);
  file_put_contents("datos.txt", $jsonClientes);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/ico" href="favicon/favicon.ico">
 <title>ABM Clientes</title>
 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="css/fontawesome/css/fontawesome.min.css">
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="col-12 text-center pb-4">
        <h1>Registro de clientes</h1>
    </div>
  </div>
  <div class="row">
    <div class="col-12 col-md-6 col-xs-12">
      <?php if($aMensaje["mensaje"] != ""): ?>

        <div class="row">
            <div class="col-12">
                <div class="alert alert-<?php echo $aMensaje["codigo"] ?>" role="alert">
                  <?php echo $aMensaje["mensaje"]; ?>
                </div>
            </div>
        </div>

      <?php endif ?>
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="row">
          <div class="col-12 form-group">
            <label for="txtDNI">DNI:</label>
            <input class="form-control" type="text" name="txtDNI" id="txtDNI"  value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["dni"] : ""; ?>">
          </div>
          <div class="col-12 form-group">
            <label for="txtNombre">Nombre:</label>
            <input class="form-control" type="text" name="txtNombre" id="txtNombre"  value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["nombre"] : ""; ?>">
          </div>
          <div class="col-12 form-group">
            <label for="txtTelefono">Teléfono:</label>
            <input class="form-control" type="text" name="txtTelefono" id="txtTelefono"  value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["telefono"] : ""; ?>">
          </div>
          <div class="col-12 form-group">
            <label for="txtCorreo">Correo:</label>
            <input class="form-control" type="text" name="txtCorreo" id="txtCorreo"  value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["correo"] : ""; ?>">
          </div>
          <div class="col-12 form-group">
            <label for="archivo">Archivo adjunto:</label>
            <input class="form-control-file" type="file" name="archivo" id="archivo" value="<?php echo isset($aClientes[$id]) ? $aClientes[$id]["imagen"] : ""; ?>">
          </div>
          <div class="col-12 form-group">
            <input class="btn btn-primary" type="submit" name="btnGuardar" id="btnGuardar" value="Guardar">
          </div>
        </div>      
      </form>
    </div>
    <div class="col-12 col-md-6 col-sm-12 col-xs-12">
      <table class="table table-hover border">
        <tr>
          <th>Imagen</th>
          <th>DNI</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Acciones</th>
        </tr>
        <?php foreach($aClientes as $id => $cliente): ?>
          <tr>
           <td><img src="files/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
           <td><?php echo $cliente["dni"] ?></td>
           <td><?php echo $cliente["nombre"] ?></td>
           <td><?php echo $cliente["correo"] ?></td>
           <td>
           <a href="index.php?id=<?php echo $id; ?>"><i class="fas fa-edit" id="btnEditar"></i></a>
           <a href="index.php?id=<?php echo $id; ?>&do=eliminar"><i class="fas fa-trash" id="btnEliminar"></i></a>
           </td>
          </tr>       
        <?php endforeach; ?>
      </table>
    </div>
</div>
<a href="index.php"><i class="fas fa-plus btn btn-primary" id="pageReload"></i></a>
</body>
</html>