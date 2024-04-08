jQuery(document).ready(function($){

    //console.log(solicitudesAjax);

    //Muestra ventana modal cuando hacemos click en botón 'Añadir nuevo'
    $("#btn-nuevo-form").click(function(){
        
        $('#modal-form').modal("show");
    })

    //Muestra ventana modal cuando hacemos click en botón 'Ver'
    $(document).on('click', 'a[data-ver]', function(){ 
        
        var id = this.dataset.ver; // dataset accede a los atributos de un elemento HTML del tipo data-...
        var url = solicitudesAjax.url; // le pasamos la url del array solicitudesAjax que indicamos en wp_localize_script.

        $.ajax({
            type:"POST",
            url: url,
            data: { //ARRAY DE DATOS
                action: "mostraregistro", // viene del alias del hook de extraform.php, en la función MostrarRegistro()
                nonce: solicitudesAjax.seguridad, // Wordpress necesita este token de seguridad que lo obtenemos de wp_localize_script
                id: id,
            },
            
            success: function(response){ // Función que se ejecuta si la solicitud es correcta
                jsonparse = JSON.parse(response); //Lo que se recibe es una cadena JSON y hay que convertirlo en un objeto
                
                /*Insertamos los valores en los input de la ventana modal*/
                $("#ver-cliente").val(jsonparse.Nombre_cliente);
                $("#ver-dni").val(jsonparse.DNI);
                $("#ver-fecha").val(jsonparse.Fecha_registro);
                $("#ver-pedido").val(jsonparse.Id_pedido);
                $("#ver_id_registro").val(jsonparse.Id_formulario);

                $('#modal-form-ver').modal("show");
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                // Envía el error si lo hay
            }
        });
    })

    //Para borrar registro del formulario al pinchar en el elemento a con el atributo personalizado data-id (es inventado)
    $(document).on('click', 'a[data-id]', function(){
        var id = this.dataset.id; // dataset accede a los atributos de un elemento HTML del tipo data-...
        var url = solicitudesAjax.url; // le pasamos la url del array solicitudesAjax que indicamos en wp_localize_script.

        //Solicitud AJAX mediante jQuery Ajax (https://www.freecodecamp.org/espanol/news/solicitud-http-en-javascript/)
        $.ajax({
            type:"POST",
            url: url,
            data: { //ARRAY DE DATOS
                action: "peticioneliminar", // viene del alias del hook de extraform.php, en la función EliminarRegistroForm()
                nonce: solicitudesAjax.seguridad, // Wordpress necesita este token de seguridad que lo obtenemos de wp_localize_script
                id: id,
            },
            success: function(){ // Función que se ejecuta si la solicitud es correcta
                alert(`Datos borrados con éxito`);
                location.reload();
            }
        });
    })
});
