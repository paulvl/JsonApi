<?php

namespace PaulVL\JsonApi;

use Illuminate\Http\JsonResponse;
use PaulVL\Helpers\ArrayHelper;

class Response
{

    /**
     * Response body
     * @var array
     */
    protected $body = array(
    	'data' => null,
    	'jsonapi' => null
    );

    protected $json_api_format;

    protected $api_version;

    protected $meta;

    protected $http_status_code;

    protected $options = JSON_UNESCAPED_UNICODE;

    private $execute_with_relations = true;

    private $header = [];

    public function __construct($http_status_code = HttpStatusCode::CODE_200_OK)
    {
        $this->http_status_code = $http_status_code;
        $this->json_api_format = config("json-api.json-api-format", true);
        $this->api_version = config("json-api.api-version", "1.0");
        $this->meta = config("json-api.meta", [ 'copyright' => "Copyright 2015 Your Bussiness App.",'authors' => ["Paul Vidal"] ]);
        $this->header = [
            'Content-Type' => config("json-api.content-type", 'application/vnd.api+json')
        ];
    }

    public function __set($key, $value)
    {
    	if($key == "data") {
        	$this->body = $value;
    	}
    }

    public function __call($function, $args) {
        if (strpos($function, 'response') !== false) {
            $function = lcfirst( str_replace('response', '', $function) );            
            $status_code_data = HttpStatusCode::getStatusCodeByMethod($function);
            if( empty($function) ) {
                return $this->prepareCorrectResponse();
            }elseif ( $function == 'ok' || $function == 'created' || $function == 'noContent' || $function == 'notModified' ) {
                return $this->prepareCorrectResponse($status_code_data['code']);
            }
            return $this->prepareErrorResponse($status_code_data['code'], $status_code_data['status'], $status_code_data['title'], $args);
        }
    }

    public function handleData($data) {
        $handler = new DataHandler($data);
        if(!$this->execute_with_relations) {
            $handler->withoutRelations();
            $this->execute_with_relations = true;
        }
        $this->data = $handler->getApiJsonableData($this->json_api_format);
    }

    private function prepareCorrectResponse($http_status_code = HttpStatusCode::CODE_200_OK, $options = JSON_UNESCAPED_UNICODE)
    {
        $this->http_status_code = $http_status_code;
        $this->options = $options;

        return $this->makeResponse();
    }

    private function prepareErrorResponse($http_status_code, $status, $title, $detail = null, $code = null, $options = JSON_UNESCAPED_UNICODE)
    {
        $this->http_status_code = $http_status_code;
        $this->options = $options;
        $this->body = [ 'errors' =>
            [
                'status' => $status,
                'code' => $code,
                'title' => $title,
                'detail' => $detail
            ]
        ];
        return $this->makeResponse();
    }

    private function makeResponse() {
        if( $this->http_status_code != HttpStatusCode::CODE_200_OK ) {
            $this->body = ArrayHelper::recursive_filter($this->body);
        }
        $this->body['meta'] = $this->meta;
        $this->body['jsonapi'] = [ 'version' => $this->api_version ];
        return new JsonResponse($this->body, $this->http_status_code, $this->header, $this->options);
    }

    public function withoutRelations() {
        $this->execute_with_relations = false;
    }

    public function addMeta(array $meta) {
        $this->meta = array_merge($meta, $this->meta);
    }
}