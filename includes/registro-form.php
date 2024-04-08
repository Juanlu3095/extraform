
<?php 
    global $wpdb;
  
    //Para obtener los formularios de la BD
    $query = "SELECT * FROM {$wpdb->prefix}formularios ORDER BY Id_formulario DESC";
    $query_secure = $wpdb->prepare($query);
    $results = $wpdb->get_results($query_secure, ARRAY_A); //Usamos ARRAY_A porque necesitamos que devuelva un array asociativo y no un array
    if(empty($results)) { 
        $results = array(); // se usa esto por si lo que devuelve el array está vacío
    }

    //Para enviar los datos a la BD al dar al botón de 'Añadir nuevo'
    $tabla = "{$wpdb->prefix}formularios";
    
    if(isset($_POST['btn-guardar']) && isset($_FILES['add_archivo_cliente'])){
        require_once ABSPATH . 'wp-admin/includes/file.php'; //WP_Filesystem necesita esta dependencia
        global $wp_filesystem;
        WP_Filesystem();

        $nombre_cliente = $_POST['add_nombre_cliente'];
        $fecha_cliente = $_POST['add_fecha_cliente'];
        $dni = $_POST['add_dni_cliente'];
        $id_pedido = $_POST['add_id_pedido_cliente'];
        
        $name_file = $_FILES['add_archivo_cliente']['name']; //Se obtiene el nombre del archivo
        $fecha_actual = date('d-m-Y');
        $new_name_file = $dni . '-' . $fecha_actual . '-' . $name_file;
		$tmp_name = $_FILES['add_archivo_cliente']['tmp_name']; //Se obtiene la ubicación temporal del archivo
		$allow_extensions = ['pdf', 'png', 'jpg']; //Array con las extensiones de archivo permitidas

		// Validación de los archivos
		$path_parts = pathinfo($new_name_file); //se obtiene la ubicación del archivo
		$ext = $path_parts['extension']; //se obtiene la extensión del archivo desde la ubicación

		if ( ! in_array($ext, $allow_extensions) ) { //Se comprueba la extensión del archivo
			echo "Error: La extensión del archivo no está admitida";
			return;
		}

        //Se establece la ruta donde se guardan los archivos
		$content_directory = $wp_filesystem->wp_content_dir() . 'uploads/solicitudes/'; //Concatenamos wp_content_dir para obtener la ruta de wp-content con el directorio que elijamos
		$wp_filesystem->mkdir( $content_directory ); //Se crea el directorio si no existe

		if( move_uploaded_file( $tmp_name, $content_directory . $new_name_file ) ) { //Mueve el archivo desde la ubicación temporal a la final
			echo "El archivo se ha subido sin problemas";
		} else {
			echo "El archivo no se ha subido";
		}

        $ruta_solicitud = $content_directory . '' . $new_name_file;

        $wpdb->insert( // insert acepta un array y sanitiza los datos, al contrario que $wpdb->query, que necesita un prepare();
            $tabla,
            array(
                'Nombre_cliente' => $nombre_cliente,
                'Fecha_registro' => $fecha_cliente,
                'DNI' => $dni,
                'Id_pedido' => $id_pedido,
                'Ruta_solicitud' => $ruta_solicitud
            ),
            array( // si esto no se especifica, todos los datos serán tratados como string
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            )
        );
        wp_redirect($_SERVER['REQUEST_URI']); //para recargar la página y así se muestren los datos ya
    }

    //Para modificar los datos desde el registro con la ventana modal que se muestra con el botón 'ver'
    if(isset($_POST['btn-modificar']) && isset($_FILES['modificar_archivo_cliente'])){
        require_once ABSPATH . 'wp-admin/includes/file.php'; //WP_Filesystem necesita esta dependencia
        global $wp_filesystem;
        WP_Filesystem();

        $modificar_id_registro = $_POST['modificar_id_registro'];
        $modificar_nombre_cliente = $_POST['modificar_nombre_cliente'];
        $modificar_fecha_cliente = $_POST['modificar_fecha_cliente'];
        $modificar_dni = $_POST['modificar_dni_cliente'];
        $modificar_id_pedido = $_POST['modificar_id_pedido_cliente'];

        $name_file = $_FILES['modificar_archivo_cliente']['name']; //Se obtiene el nombre del archivo
        $fecha_actual = date('d-m-Y');
        $new_name_file = $modificar_dni . '-' . $fecha_actual . '-' . $name_file;
		$tmp_name = $_FILES['modificar_archivo_cliente']['tmp_name']; //Se obtiene la ubicación temporal del archivo
		$allow_extensions = ['pdf', 'png', 'jpg']; //Array con las extensiones de archivo permitidas

		// Validación de los archivos
		$path_parts = pathinfo($new_name_file); //se obtiene la ubicación del archivo
		$ext = $path_parts['extension']; //se obtiene la extensión del archivo desde la ubicación

		if ( ! in_array($ext, $allow_extensions) ) { //Se comprueba la extensión del archivo
			echo "Error: La extensión del archivo no está admitida";
			return;
		}

        //Se establece la ruta donde se guardan los archivos
		$content_directory = $wp_filesystem->wp_content_dir() . 'uploads/solicitudes/'; //Concatenamos wp_content_dir para obtener la ruta de wp-content con el directorio que elijamos
		$wp_filesystem->mkdir( $content_directory ); //Se crea el directorio si no existe

		if( move_uploaded_file( $tmp_name, $content_directory . $new_name_file ) ) { //Mueve el archivo desde la ubicación temporal a la final
			echo "El archivo se ha subido sin problemas";
		} else {
			echo "El archivo no se ha subido";
		}

        $ruta_solicitud = $content_directory . '' . $new_name_file;

        $wpdb->update( // insert acepta un array y sanitiza los datos, al contrario que $wpdb->query, que necesita un prepare();
            $tabla,
            array(
                'Nombre_cliente' => $modificar_nombre_cliente,
                'Fecha_registro' => $modificar_fecha_cliente,
                'DNI' => $modificar_dni,
                'Id_pedido' => $modificar_id_pedido,
                'Ruta_solicitud' => $ruta_solicitud
            ),
            array('Id_formulario' => $modificar_id_registro),
            array(
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
            ),
            array(
                '%d'
            )
        );
        //echo '<script>console.log("Estos son los datos que has enviado: ' . $modificar_id_registro . '")</script>';
        wp_redirect($_SERVER['REQUEST_URI']); //para recargar la página y así se muestren los datos ya
    }

?>

<div class="wrap">

    <?php 
        echo '<h1 class="wp-heading-inline">' . get_admin_page_title() . '</h1>';
    
    ?>

    <a id="btn-nuevo-form" class="page-title-action">Añadir nuevo</a>
    
    <br><br><br>

    <table class="wp-list-table widefat fixed striped pages">

        <thead>
            <th>ID Formulario</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>DNI</th>
            <th>Id pedido</th>
            <th>Solicitud</th>
            <th>Acciones</th>
        </thead>

        <tbody id="the-list">
            <?php
            foreach ($results as $result){ ?>
                <tr>
                    <td><?php echo $result['Id_formulario'] ?></td>
                    <td><?php echo $result['Nombre_cliente'] ?></td>
                    <td><?php echo $result['Fecha_registro'] ?></td>
                    <td><?php echo $result['DNI'] ?></td>
                    <td><?php echo $result['Id_pedido'] ?></td>
                    <td>
                    <?php if($result['Ruta_solicitud']): ?>
                        <a href="<?php echo $result['Ruta_solicitud'];?>" target="_blank">Ver solicitud</a>
                    <?php endif; ?>
                    </td>
                    <td>
                        <a data-ver="<?php echo $result['Id_formulario'] ?>" class="page-title-action">Ver</a>
                        <a data-id="<?php echo $result['Id_formulario'] ?>" class="page-title-action">Borrar</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>

</div>

<!--MODAL CON FORMULARIO-->
<div class="modal fade" id="modal-form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Nuevo registro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      

        <form name="add_cliente" id="add_cliente" action="" method="post" enctype="multipart/form-data">
            <div class="modal-body">    
                <div class="mb-3">
                    <label for="cliente" class="form-label">Nombre del cliente:</label>
                    <input type="text" name="add_nombre_cliente" class="form-control" id="cliente" aria-describedby="emailHelp" required>
                </div>

                <div class="mb-3">
                    <label for="dni" class="form-label">DNI:</label>
                    <input type="text" name="add_dni_cliente" class="form-control" id="dni" required>
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha:</label>
                    <input type="date" name="add_fecha_cliente" class="form-control" id="fecha" required>
                </div>

                <div class="mb-3">
                    <label for="pedido" class="form-label">Id del pedido:</label>
                    <input type="number" name="add_id_pedido_cliente" class="form-control" id="pedido" required>
                </div>

                <div class="mb-3">
                        <label for="add_archivo_cliente" class="form-label">Solicitud cumplimentada:</label>
                        <input type="file" name="add_archivo_cliente" class="form-control" id="archivo" required>
                </div>
                
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" name="btn-guardar">Guardar</button>
            </div>
        </form>

      
    </div>
  </div>
</div>

<!--MODAL PARA VER REGISTROS-->
<div class="modal fade" id="modal-form-ver" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Información del registro</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      

        <form name="modificar_cliente" id="modificar_cliente" action="" method="post" enctype="multipart/form-data">
            <div class="modal-body">    
                <div class="mb-3">
                    <label for="cliente" class="form-label">Nombre del cliente:</label>
                    <input type="text" name="modificar_nombre_cliente" class="form-control" id="ver-cliente" aria-describedby="emailHelp">
                    <input type="hidden" name="modificar_id_registro" id="ver_id_registro">
                </div>

                <div class="mb-3">
                    <label for="dni" class="form-label">DNI:</label>
                    <input type="text" name="modificar_dni_cliente" class="form-control" id="ver-dni" aria-describedby="dniHelp">
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha:</label>
                    <input type="date" name="modificar_fecha_cliente" class="form-control" id="ver-fecha">
                </div>

                <div class="mb-3">
                    <label for="pedido" class="form-label">Id del pedido:</label>
                    <input type="number" name="modificar_id_pedido_cliente" class="form-control" id="ver-pedido">
                </div>

                <div class="mb-3">
                        <label for="modificar_archivo_cliente" class="form-label">Solicitud cumplimentada:</label>
                        <input type="file" name="modificar_archivo_cliente" class="form-control" id="ver-solicitud" required>
                </div>
                
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" name="btn-modificar">Guardar</button>
            </div>
        </form>

      
    </div>
  </div>
</div>
