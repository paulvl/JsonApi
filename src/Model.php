<?php

namespace PaulVL\JsonApi;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

use PaulVL\Helpers\StringHelper;

class Model extends Eloquent
{
    use DispatchesJobs, ValidatesRequests;
    
	private $model_name;

	private $model_class;

	private $model_type;

	protected $displayable_relations = [];

	static protected $rules = [];

	private $with_relations = true;

	public function __construct(array $attributes = []){
		parent::__construct($attributes);
		$this->setModelClassNames();
		$this->model_type = strtolower($this->model_name);
	}

	public static function boot()
	{
		parent::boot();
	}

	public function getVisibleAttributes() {
		$all_attributes = $this->getAttributes();
	    $hidden = array_flip( $this->getHidden() );
	    $visible_attributes = array_diff_key($all_attributes, $hidden);
	    return $visible_attributes;
	}

	public function getApiJsonableAttributes() {
		$visible_attributes = $this->getVisibleAttributes();
	    $api_jsonable_attributes = array_diff_key($visible_attributes, [ $this->getKeyName() => $this->getKey() ]);
	    return $api_jsonable_attributes;
	}

	public function getApiJsonableAttributesKeys() {
		$api_jsonable_attributes = $this->getApiJsonableAttributes();
		$api_jsonable_attributes_keys = array_keys($api_jsonable_attributes);
	    return $api_jsonable_attributes_keys;
	}

	public function getApiJsonableAttributesValues() {
		$api_jsonable_attributes = $this->getApiJsonableAttributes();
		$api_jsonable_attributes_keys = array_keys($api_jsonable_attributes);
		$api_jsonable_attributes_values = array();
		foreach ($api_jsonable_attributes_keys as $key) {
			array_push($api_jsonable_attributes_values, $api_jsonable_attributes[$key]);
		}
	    return $api_jsonable_attributes_values;
	}

	public function loadDisplayableRelations() {
		if(!empty($this->displayable_relations) && $this->with_relations) {
			foreach ($this->displayable_relations as $relation) {
		    	$isRelationLoaded = array_key_exists($relation, $this->getRelations());
			    if(!$isRelationLoaded) {
			    	$this->load($relation);
			    }
		    }
		}
		$this->with_relations = true;
	}

	public function getRelationshipsAsApiJsonable() {
		$this->loadDisplayableRelations();
    	$relations = $this->getRelations();

    	$relationships = array();

    	foreach ($relations as $relation => $element) {
    		if( $element instanceof Model ) {
    			$relationships[$relation] = array(
    				'data' => [
    					'type' => $element->getModelType(),
						$element->getKeyName() => $element->getKey()
					]
				);
    		} elseif ( $element instanceof Collection ) {
    			$multi_data = array();
    			foreach ($element as $sub_element) {
    				array_push($multi_data, [
    					'type' => $sub_element->getModelType(),
						$sub_element->getKeyName() => $sub_element->getKey()
					]);
    			}
    			$relationships[$relation] = array(
    				'data' => $multi_data
				);
    		}
    	}

    	return $relationships;
	}

	public function setDisplayableRelations(Array $displayable_relations) {
		$this->displayable_relations = $displayable_relations;
	}

	public function withNoRelations() {
		$this->with_relations = false;
	}

	/**
	 * ACCESSORS
	 */
	
	private function setModelClassNames() {
		$model_class= get_class($this);
		$model_name = substr(strrchr($model_class, "\\"), 1);

		$this->model_class = $model_class;
		$this->model_name = $model_name;
	}

	public function getModelClass() {
		return $this->model_class;
	}

	public function getModelName() {
		return $this->model_name;
	}

	public function getModelType() {
		return $this->model_type;
	}

	public static function getRules() {
		return static::$rules;
	}

	public function getUpdateRules() {
		$update_rules = static::$rules;
		foreach ($update_rules as $key => $value) {
			if( StringHelper::contains($value, "unique") ) {
				$update_rules[$key] = $value.",".$this->getKey().",".$this->getKeyName();
			}
		}
		return $update_rules;
	}
}