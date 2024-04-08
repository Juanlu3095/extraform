<?php
defined('ABSPATH') or die( "Bye bye" );

/*
 * Add my new menu to the Admin Control Panel
 */
// Hook the 'admin_menu' action hook, run the function named 'lbd_Add_My_Admin_Link()'
add_action( 'admin_menu', 'extra_Add_My_Admin_Link' );
// Add a new top level menu link to the ACP
function extra_Add_My_Admin_Link()
{
      add_menu_page( // Función integrada de Wordpress para añadir plugin al menú
        'Extra Form admin page', // Título de la página que se muestra en la pestaña del navegador
        'Extra Form', // Nombre que lleva el plugin en el menú de Wordpress
        'manage_options', // Capacidad o permiso que los usuarios deben tener para acceder a la página enlazada del plugin
        'extra-admin-page', // Menu_slug, es el slug de la página de configuración que se indica debajo
        'extra_Render_Admin_Page', // Callback, es la función que renderiza la página de configuración
        'dashicons-database', // Url del icono que tendrá el plugin en el menú de Wordpress
        '1' // Posición del plugin en el menú de Wordpress
    );

    //Añade un submenú con una función integrada de Wordpress, hay que poner uno por cada opción
    //Este es el de información
    add_submenu_page(
        'extra-admin-page', // Slug del menú principal
        'Información', // Título de la página
        'Información', // Título en el menú
        'manage_options', // Permisos
        plugin_dir_path(__FILE__) . 'informacion.php', //Slug al pinchar en el submenú
        null, // Callback de la función que renderiza la página del submenú
        '1', // Posición
    );
}

// Callback para renderizar la página de administración
function extra_Render_Admin_Page() {
    // Llama al archivo PHP donde se encuentra la estructura de la página de admin del plugin
    include_once ('registro-form.php');
}