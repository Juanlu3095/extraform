<?php
/*
Plugin Name: Extra Form
Plugin URI: http://jcooldevelopment.es
Description: Permite incluir un formulario personalizado para recoger información del cliente antes de comprar un producto.
Version: 1.0.0
Author: jcooldevelopment
Author URI: mailto:jcooldevelopment@gmail.com
License: GPL2
*/

// Include mfp-functions.php, use require_once to stop the script if lbd-functions.php is not found
require_once plugin_dir_path(__FILE__) . 'includes/extra-functions.php';

function activar() {
    global $wpdb;

    $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}formularios(
        Id_formulario INT NOT NULL AUTO_INCREMENT,
        Nombre_cliente VARCHAR(100) NULL,
        Fecha_registro VARCHAR(20) NULL,
        DNI VARCHAR(10) NOT NULL,
        Ruta_solicitud (VARCHAR(400) NULL,
        Id_pedido (VARCHAR(10) NULL,
        PRIMARY KEY (Id_formulario)
    );";

    $query_secure = $wpdb->prepare($sql);
    $results = $wpdb->query($query_secure); 
    
}

function desactivar() {}

function borrar() {
    include(plugin_dir_path(__FILE__) . 'uninstall.php');
};

//Función para cargar bootstrap
function bootstrap($hook){

    if($hook != "toplevel_page_extra-admin-page"){ // Si no estamos en el panel del plugin no se carga bootstrap
        return ;
    }
    wp_enqueue_script('bootstrapJS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array('jquery'));
    wp_enqueue_style('bootstrapCSS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
}

add_action('admin_enqueue_scripts', 'bootstrap');

//Función para cargar nuestro archivo Javascript propio
function script_propio($hook){
    
    if($hook != "toplevel_page_extra-admin-page"){ // Si no estamos en el panel del plugin no se cargan los scripts
        return ;
    }

    wp_enqueue_script('JS', plugins_url( 'js/extra-form.js', __FILE__ ), array('jquery'));
    wp_enqueue_style('CSS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
    wp_localize_script('JS', 'solicitudesAjax', [ // Función de wordpress para pasar datos desde el lado del servidor (PHP) al lado del cliente (JavaScript), el primer parámetro es el alias del hook de la línea anterior, el segundo es un alias para esta función
        'url' => admin_url('admin-ajax.php'), // Wordpress ejecuta todas las solicitudes AJAX desde este documento, por ello hay que indicarlo
        'seguridad' => wp_create_nonce('seg'), // seg es un alias que le ponemos
    ]);
}

add_action('admin_enqueue_scripts', 'script_propio');

//Función para eliminar los registros de los formularios cuando pinchamos en 'borrar'
function EliminarEncuesta(){
    $nonce = $_POST['nonce']; // este valor viene de los datos que se envían a través de la solicitud Ajax
    if(!wp_verify_nonce($nonce, 'seg')){ // verificamos si el nonce es correcto
        die('No tiene permisos para ejecutar esta petición.');
    }

    $id = $_POST['id'];
    
    global $wpdb;
    $tabla = "{$wpdb->prefix}formularios";
    $wpdb->delete($tabla, array('Id_formulario' =>$id));

     return true;
}

add_action('wp_ajax_peticioneliminar','EliminarEncuesta');

//Función para mostrar los datos del registro en la ventana modal al pulsar el botón 'Ver'
function MostrarRegistro(){
    $nonce = $_POST['nonce']; // este valor viene de los datos que se envían a través de la solicitud Ajax
    if(!wp_verify_nonce($nonce, 'seg')){ // verificamos si el nonce es correcto
        die('No tiene permisos para ejecutar esta petición.');
    }
    
    $id_ajax = $_POST['id'];
    //echo 'id del post: ' . $id_ajax;
    global $wpdb;
    $tabla = "{$wpdb->prefix}formularios";
    // Realizar la consulta SQL para obtener los datos del registro según el ID
    $query = $wpdb->prepare("SELECT * FROM $tabla WHERE Id_formulario = %d", $id_ajax);

    // Ejecutar la consulta
    $registro = $wpdb->get_row($query); //get_row se usa cuando sólo se espera una sola fila y get_results siempre devuelve un array

     echo json_encode($registro);
     die();
}

add_action('wp_ajax_mostraregistro','MostrarRegistro');

// Shortcode
function form_shortcode() {
    
    include(plugin_dir_path(__FILE__) . 'includes/form.php');
}

// Registra el shortcode con WordPress
add_shortcode('formulario_cumpleaños', 'form_shortcode');

//Actualizamos ID del pedido al realizar el checkout
//Tener en cuenta que se registra la id del último pedido y esto no es muy fiable, lo mejor sería añadir un campo que sepamos que va a
//ser único y se pueda asociar a un campo del form del checkoout, por ejemplo el DNI.
function id_pedido_form($order_id){
    global $wpdb;
    $pedido = wc_get_order($order_id);
    $dni = get_post_meta( $order_id, '_billing_wooccm11', true);
    $tabla = "{$wpdb->prefix}formularios";

    // Obtenemos el ID del pedido
    $pedido_id = $pedido->get_id();

    // Obtenemos el último registro de la tabla wp_formularios
    $ultimo_registro = $wpdb->get_row("SELECT * FROM $tabla WHERE DNI = '$dni' ORDER BY Id_formulario DESC LIMIT 1");

    // Guardamos la id del pedido en el último registro de wp_formularios
    if ($ultimo_registro) {
        $ultimo_registro_id = $ultimo_registro->Id_formulario; 
        $wpdb->update(
            $tabla,
            array('Id_pedido' => $pedido_id),
            array('Id_formulario' => $ultimo_registro_id),
            array('%d'),
            array('%d')
            
        );
    }
}

add_action('woocommerce_new_order', 'id_pedido_form');

// Hook para vincular los botones del panel de plugins de wordpress con las funciones de arriba
// __FILE__ envía la dirección del archivo
// El segundo parámetro es el callback que llama a la función activar que se ejecutará cuando se active el plugin
register_activation_hook( __FILE__, 'activar' );

// Hook para enlazar la función de desactivado
register_deactivation_hook( __FILE__, 'desactivar' );

// Hook para enlazar la función de borrado del plugin
register_uninstall_hook( __FILE__, 'borrar' );
