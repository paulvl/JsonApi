<?php

namespace PaulVL\JsonApi;

use Illuminate\Http\JsonResponse;

class HttpStatusCode
{
	// Respuesta a un exitoso GET, PUT, PATCH o DELETE. Puede ser usado también para un POST que no resulta en una creación.
    const  CODE_200_OK						= 200;
    
    // [Creada] Respuesta a un POST que resulta en una creación. Debería ser combinado con un encabezado Location, apuntando a la ubicación del nuevo recurso.
    const  CODE_201_CREATED					= 201;
    
    // [Sin Contenido] Respuesta a una petición exitosa que no devuelve un body (por ejemplo en una petición DELETE).
    const  CODE_204_NO_CONTENT				= 204;
    
    // [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo y el cliente puede usar datos cacheados.
    const  CODE_304_NOT_MODIFIED			= 304;
    
    // [Petición Errónea] La petición está malformada, como por ejemplo, si el contenido no fue bien parseado. El error se debe mostrar también en el JSON de respuesta.
    const  CODE_400_BAD_REQUEST				= 400;
    
    // [Desautorizada] Cuando los detalles de autenticación son inválidos o no son otorgados. También útil para disparar un popup de autorización si la API es usada desde un navegador.
    const  CODE_401_UNAUTHORIZED			= 401;
    
    // [Prohibida] Cuando la autenticación es exitosa pero el usuario no tiene permiso al recurso en cuestión.
    const  CODE_403_FORBIDDEN				= 403;
    
    // [No encontrada] Cuando un recurso se solicita un recurso no existente.
    const  CODE_404_NOT_FOUND				= 404;
    
    // [Método no permitido] Cuando un método HTTP que está siendo pedido no está permitido para el usuario autenticado.
    const  CODE_405_METHOD_NOT_ALLOWED		= 405;
    
    // [Método no permitido] Cuando un método HTTP que está siendo pedido no está permitido para el usuario autenticado.
    const  CODE_406_NOT_ACCEPTABLE			= 406;
    
    // [Conflicto] Cuando hay algún conflicto al procesar una petición, por ejemplo en PATCH, POST o DELETE.
    const  CODE_409_CONFLICT				= 409;
    
    // [Retirado] Indica que el recurso en ese endpoint ya no está disponible. Útil como una respuesta en blanco para viejas versiones de la API.
    const  CODE_410_GONE					= 410;
    
    // [Tipo de contenido no soportado] Si el tipo de contenido que solicita la petición es incorrecto.
    const  CODE_415_UNSUPPORTED_MEDIA_TYPE	= 415;
    
    // [Entidad improcesable] Utilizada para errores de validación, o cuando por ejemplo faltan campos en una petición.
    const  CODE_422_UNPROCESSABLE_ENTITY	= 422;
    
    // [Demasiadas peticiones] Cuando una petición es rechazada debido a la tasa límite.
    const  CODE_429_TOO_MANY_REQUESTS		= 429;
    
    // [Error Interno del servidor] Los desarrolladores de API NO deberían usar este código. En su lugar se debería loguear el fallo y no devolver respuesta.
    const  CODE_500_INTERNAL_SERVER_ERROR	= 500;
    
    // [Servicio no disponible] Los servidores están activos, pero saturados con solicitudes.
    const  CODE_503_SERVICE_UNAVAILABLE		= 503;

    static $http_status_codes = [
		self::CODE_200_OK						=> [ "method" => "ok", "title" => "Ok" , "status" => "200", "code" => 200 ],
		self::CODE_201_CREATED					=> [ "method" => "created", "title" => "Created" , "status" => "201", "code" => 201 ],
		self::CODE_204_NO_CONTENT				=> [ "method" => "noContent", "title" => "No Content" , "status" => "204", "code" => 204 ],
		self::CODE_304_NOT_MODIFIED				=> [ "method" => "notModified", "title" => "Not Modified" , "status" => "304", "code" => 304 ],
		self::CODE_400_BAD_REQUEST				=> [ "method" => "badRequest", "title" => "Bad Request" , "status" => "400", "code" => 400 ],
		self::CODE_401_UNAUTHORIZED				=> [ "method" => "unauthorized", "title" => "Unauthorized" , "status" => "401", "code" => 401 ],
		self::CODE_403_FORBIDDEN				=> [ "method" => "forbidden", "title" => "Forbidden" , "status" => "403", "code" => 403 ],
		self::CODE_404_NOT_FOUND				=> [ "method" => "notFound", "title" => "Not Found" , "status" => "404", "code" => 404 ],
		self::CODE_405_METHOD_NOT_ALLOWED		=> [ "method" => "methodNotAllowed", "title" => "Method Not Allowed" , "status" => "405", "code" => 405 ],
		self::CODE_406_NOT_ACCEPTABLE			=> [ "method" => "notAcceptable", "title" => "Not Acceptable" , "status" => "406", "code" => 406 ],
		self::CODE_409_CONFLICT					=> [ "method" => "conflict", "title" => "Conflict" , "status" => "409", "code" => 409 ],
		self::CODE_410_GONE						=> [ "method" => "gone", "title" => "Gone" , "status" => "410", "code" => 410 ],
		self::CODE_415_UNSUPPORTED_MEDIA_TYPE	=> [ "method" => "unsupportedMediaType", "title" => "Unsupported Media Type" , "status" => "415", "code" => 415 ],
		self::CODE_422_UNPROCESSABLE_ENTITY		=> [ "method" => "unprocessableEntity", "title" => "Unprocessable Entity" , "status" => "422", "code" => 422 ],
		self::CODE_429_TOO_MANY_REQUESTS		=> [ "method" => "tooManyRequests", "title" => "Too Many Requests" , "status" => "429", "code" => 429 ],
		self::CODE_500_INTERNAL_SERVER_ERROR	=> [ "method" => "internalServerError", "title" => "Internal Server Error" , "status" => "500", "code" => 500 ],
		self::CODE_503_SERVICE_UNAVAILABLE		=> [ "method" => "serviceUnavailable", "title" => "Service Unavailable", "status" => "503", "code" => 503 ],
    ];

    public static function getStatusCodeByMethod($method_name) {
        foreach (self::$http_status_codes as $key => $value) {
            if( $value["method"] == $method_name ) return $value;
        }
    }
}