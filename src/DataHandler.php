<?php

namespace PaulVL\JsonApi;

use PaulVL\JsonApi\Model;
use Illuminate\Database\Eloquent\Collection;

class DataHandler
{
	protected $data;

	private $execute_with_relations = true;

	public function __construct($data){
		$this->data = $data;
	}

	public function getApiJsonableData($valid_api_format = true) {
		$jsonable_data = array();
		$data = null;
		if($valid_api_format) {
			if($this->data instanceof Model) {
				$data = $this->getModelDataAsJsonApi($this->data);
			}elseif($this->data instanceof Collection) {
				$data = $this->getCollectionDataAsJsonApi($this->data);
			}else {
				return 'Mostar error';
			}
		}else {
			if($this->data instanceof Model) {
				$data = $this->getRawModelData($this->data);
			}elseif($this->data instanceof Collection) {
				$data = $this->getRawCollectionData($this->data);
			}else {
				return 'Mostar error';
			}
		}
		$jsonable_data['data'] = $data;

		return $jsonable_data;
	}

	private function getModelDataAsJsonApi(Model $model) {
		$data = array();
		$data['type'] = $model->getModelType();
		$data[$model->getKeyName()] = $model->getKey();
		$data['attributes'] = $model->getApiJsonableAttributes();
		$data['relationships'] = ( $this->execute_with_relations ) ? $model->getRelationshipsAsApiJsonable() : [];
		return $data;
	}

	private function getCollectionDataAsJsonApi(Collection $collection) {
		$data = array();
		foreach ($collection as $element) {
			array_push( $data, $this->getModelDataAsJsonApi($element) );
		}
		return $data;
	}

	private function getRawModelData(Model $model) {
		( $this->execute_with_relations ) ? $model->loadDisplayableRelations() : null;
		return  $model->toArray();
	}

	private function getRawCollectionData(Collection $collection) {
		foreach ($collection as $element) {
			( $this->execute_with_relations ) ? $element->loadDisplayableRelations() : null;
		}
		return $collection->toArray();
	}

	public function withoutRelations() {
		$this->execute_with_relations = false;
	}

}