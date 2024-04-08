<?php 
    global $wpdb;

    
    //Para enviar los datos a la BD
    $tabla = "{$wpdb->prefix}formularios";
    if(isset($_POST['btn-guardar']) && isset($_FILES['add_archivo_cliente'])){
        require_once ABSPATH . 'wp-admin/includes/file.php'; //WP_Filesystem necesita esta dependencia
        global $wp_filesystem;
        WP_Filesystem();

        $nombre_cliente = $_POST['add_nombre_cliente'];
        $dni_cliente = $_POST['add_dni_cliente'];
        $fecha_cliente = $_POST['add_fecha_cliente'];

        $name_file = $_FILES['add_archivo_cliente']['name']; //Se obtiene el nombre temporal del archivo
        $fecha_actual = date('d-m-Y');
        $new_name_file = $dni_cliente . '-' . $fecha_actual . '-' . $name_file;
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
                'DNI' => $dni_cliente,
                'Fecha_registro' => $fecha_cliente,
                'Ruta_solicitud' => $ruta_solicitud
            ),
            array( // si esto no se especifica, todos los datos serán tratados como string
                '%s',
                '%s',
                '%s',
                '%s',
            )
        );

        // Añadir producto al carrito
        $product_id = 15; // ID del producto que deseas añadir
        $quantity = 1; // Cantidad del producto
        WC()->cart->add_to_cart($product_id, $quantity);

        // Redireccionar al usuario al checkout
        //wp_safe_redirect(wc_get_checkout_url());

        //Temas específicos de Wordpress como Astra dan problemas en la redirección. Aquí una alternativa:
        ?>
        <script>window.location.href = '<?php echo wc_get_checkout_url(); ?>';</script>
        <?php
        exit;
    }
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<form name="add_cliente" id="add_cliente" action="" method="post" enctype="multipart/form-data">
    <div class="modal-body">    
        <div class="mb-3">
                        <label for="cliente" class="form-label">Nombre del cliente:</label>
                        <input type="text" name="add_nombre_cliente" class="form-control" id="cliente" aria-describedby="nombreHelp" required>
        </div>

        <div class="mb-3">
                        <label for="dni" class="form-label">DNI:</label>
                        <input type="text" name="add_dni_cliente" class="form-control" id="dni" aria-describedby="dniHelp" required>
        </div>

        <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input type="date" name="add_fecha_cliente" class="form-control" id="fecha" required>
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