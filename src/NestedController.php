<?php

namespace PaulVL\JsonApi;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use PaulVL\JsonApi\DataHandler;
use PaulVL\JsonApi\Response;
use PaulVL\Helpers\StringHelper;

class NestedController extends IlluminateController
{
	use DispatchesJobs, ValidatesRequests;

    /**
     * PaulVL\JsonApi\Model class name.
     * @var string
     */
    protected $model_class;

	/**
	 * Relationship name.
	 * @var string
	 */
	protected $relationship_name;

    /**
     * Display a listing of the resource.
     *
     * @return PaulVL\JsonApi\Response
     */
    public function index($modelId)
    {
        $class = $this->model_class;
        $relation = $this->relationship_name;

        $response = new Response();
        /* MANUAL DATA HANDLING
    	$handler = new DataHandler($class::all());
	    $response->data = $handler->getApiJsonableData(true);
        */
        $response->withoutRelations();
        $response->handleData($class::findOrFail($modelId)->$relation);
	    return $response->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return PaulVL\JsonApi\Response
     */
    public function store(Request $request)
    {
        $response = new Response;
        return $response->responseNotFound();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return PaulVL\JsonApi\Response
     */
    public function show($modelId, $relatedId)
    {
        $class = $this->model_class;
        $relation = $this->relationship_name;
        $response = new Response();
        /* MANUAL DATA HANDLING
        $handler = new DataHandler($class::findOrFail($id));
        $response->data = $handler->getApiJsonableData(true);
        */
        $response->handleData($class::findOrFail($modelId)->$relation()->findOrFail($relatedId));
        return $response->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return PaulVL\JsonApi\Response
     */
    public function update(Request $request, $id)
    {
        $response = new Response;
        return $response->responseNotFound();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return PaulVL\JsonApi\Response
     */
    public function destroy($id)
    {
        $response = new Response;
        return $response->responseNotFound();
    }

    /**
     * Return data needed for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $response = new Response;
        return $response->responseNotFound();
    }

    /**
     * Return data needed for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $response = new Response;
        return $response->responseNotFound();
    }

}