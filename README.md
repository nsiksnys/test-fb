# test-fb
**Probando traer perfiles de Facebook usando la Graph API**

## Instalación
1. clonar el repositorio
2. Ejecutar `composer install` dentro de la carpeta del proyecto
3. Crear un .env reemplazando los valores por defecto por los propios. 
  * APP_ID: id de la aplicación de Facebook. Obligatorio.
  * APP_SECRET: secret de la aplicación de Facebook. Obligatorio.
  * APP_TOKEN: token de la aplicación de Facebook. Obligatorio.
  * API_VERSION: 2.12 por defecto. Opcional.
4. Si se quieren ejecutar los tests unitarios, crear un phpunit.xml reemplazando los valores por defecto por los propios. Los valores pueden ser los mismo que en el .env, excepto el TEST_USER_ID, que corresponde a un usuario de prueba creado por Facebook al crear la aplicación.
5. Para probar el proyecto antes de instalar en el servidor, ejecutar `bin/console server:run`
6. Para ejecutar los tests unitarios
  6.1. Ejecutar `./vendor/bin/simple-phpunit` dentro de la carpeta del proyecto
  6.2. Si el comando previo devuelve el error `[RuntimeException] The "--no-suggest" option does not exist.` , hay que abrir el ejecutable simple-phpunit con un editor de texto y editar la línea 
 ```php
 $exit = proc_close(proc_open("$COMPOSER install --no-dev --prefer-dist --no-suggest --no-progress --ansi", array(), $p, getcwd(), null, array('bypass_shell' => true)));
 ```
 eliminar la opción --no-suggest, 
 ```php
 $exit = proc_close(proc_open("$COMPOSER install --no-dev --prefer-dist --no-progress --ansi", array(), $p, getcwd(), null, array('bypass_shell' => true)));
 ```
 Y después ejecutar nuevamente `./vendor/bin/simple-phpunit`. Composer instalará las dependencias necesarias.
 Una vez que termine, `./vendor/bin/simple-phpunit`, ejecutará los tests unitarios.
 
 ## Uso
 Para pedir un perfil, la ruta es `profile/facebook/{id}`
 
 **Ejemplo:** (usando el servidor de prueba de Symfony)
 * /profile/facebook/1 => { "message": "Some of the aliases you requested do not exist: 1" } (status 500)
 * /profile/facebook/me =>  { "message": An active access token must be used to query information about the current user." } (status 500)
 * /profile/facebook/12345 => {"name":"Eli Richlin","id":"12345"} (status 200)
