EXTRA FORM

Extra Form es un plugin de Wordpress personalizado de para añadir un formulario a una web mediante un shortcode en el que podemos recoger información específica de nuestros clientes,
para acto seguido añadir un concreto al carrito y llevarnos a la página del Checkout de nuestra tienda. Al realizar el pago, en el registro de nuestro formulario se añadirá la id del pedido para relacionar el pedido con los datos recogidos. 

Tener en cuenta que este plugin está pensado para un cliente del sector de la animación infantil.

Instalación:

El plugin se usa para el CMS Wordpress. Se requiere Woocommerce y Woocommerce Checkout Manager.
Al instalarlo desde el panel de administración de plugins en Wordpress y con los dos plugins arriba mencionados debemos hacer lo siguiente:

- El plugin debe modificarse para que añada el producto y la cantidad correspondiente en el archivo includes/form.php.
- Se debe crear el campo para el DNI en el checkout: Woocommerce->Finalizar compra->Facturación. Este campo se usará para relacionar los registro del formulario con Woocommerce.
- Se debe coger la id del campo creado anteriormente e introducirlo en la variable $dni del archivo extraform.php.
- Una vez hecho esto, podemos añadir el formulario a nuestra página mediante el shortcode [formulario-cumpleaños].

Para más información detallada, por favor consulta la documentación del plugin.
