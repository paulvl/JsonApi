<?php

namespace PaulVL\JsonApi;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class QueryHelper
{
	static $valid_conditions = [
		'where',
		'orWhere',
		'whereBetween',
		'whereNotBetween',
		'whereIn',
		'whereNotIn',
		'whereNull',
		'whereNotNull',
		'groupBy',
		'orderBy',
	];

	public static function getRawQueriesFromRequest(Request $request)
	{

		$_conditions = explode("|", $request->get('q', null));

		if(is_null($_conditions)) return null;

		$conditions = array();

		foreach ($_conditions as $key => $parameters) {
			parse_str($parameters, $conditions[$key]);
		}

		$queries = array();

		foreach($conditions as $item) {
			$condition = key($item);
			$values = current($item);

			if(!isset($result[$condition])) {
				$result[$condition] = array();
			}
			$queries[$condition][] = explode(",", $values);
		}

	    return $queries;
	}

	public static function validateRawQueriesArray(array $queries)
	{
		foreach ($queries as $condition => $values) {
			if(!in_array($condition, self::$valid_conditions)) {
				return false;
			}
		}
		return true;
	}

	public static function rawQueryData(Builder $builder, array $conditions)
	{
		foreach ($conditions as $condition => $array) {
			try {

				foreach ($array as $value) {

					switch ($condition) {
						case 'groupBy':
							$builder = $builder->groupBy( $value[0] );
							return $builder;
							break;
						case 'orderBy':
							$builder = $builder->orderBy( $value[0], $value[1] );
							return $builder;
							break;
						case 'where':
							if(count($value) > 2){
								$builder = $builder->where( $value[0], $value[1], $value[2] );
							}else{
								$builder = $builder->where( $value[0], $value[1] );
							}						
							return $builder;
							break;
						case 'whereBetween':
							$builder = $builder->whereBetween( $value[0], array( $value[1], $value[2] ) );
							return $builder;
							break;
						case 'whereIn':
							$in = array_diff([$value[0]], $value);
							$builder = $builder->whereIn($value[0], $in);
							return $builder;
							break;
						case 'whereNotBetween':
							$builder = $builder->whereNotBetween( $value[0], array( $value[1], $value[2] ) );
							return $builder;
							break;
						case 'whereNotIn':
							$notIn = array_diff([$value[0]], $value);
							$builder = $builder->whereNotIn($value[0], $notIn);
							return $builder;
							break;
						case 'whereNotNull':
							$builder = $builder->whereNotNull( $value[0] );
							return $builder;
							break;
						case 'whereNull':
							$builder = $builder->whereNull( $value[0] );
							return $builder;
							break;
						case 'orWhere':
							if(count($value) > 2){
								$builder = $builder->orWhere( $value[0], $value[1], $value[2] );
							}else{
								$builder = $builder->orWhere( $value[0], $value[1] );
							}	
							return $builder;
							break;
						
						default:
							return false;
							break;
					}

				}
				
			} catch (Exception $e) {
				return false;	
			}			
		}
	}

	public static function columnQueryData(Builder $builder, array $inputs, array $valid_columns, $url_has_parameters)
	{
		$url = $url_has_parameters ? '&' : '?';

		$has_parameters = false;

		$counter = 0;

		foreach ($inputs as $column => $value) {
			if(in_array($column, $valid_columns))
			{
				$builder = $builder->where($column, $value);
				$prefix = ($counter > 0) ? '&' : '';
				$url .= $prefix . $column . '=' . $value;
				$has_parameters = true;
			}
			$counter++;
		}
		$column_query['builder'] = $builder;
		$column_query['url'] = $url;
		$column_query['has_parameters'] = $has_parameters;
		return $column_query;
	}

}