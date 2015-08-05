<?php

namespace PaulVL\JsonApi;

use Illuminate\Database\Eloquent\Collection;

class PaginationHelper
{

	public static function validatePagination($per_page, $current_page) {
		$valid = false;
		if ( is_numeric($per_page) && is_numeric($current_page) ) {
			$per_page = intval($per_page);
			$current_page = intval($current_page);

			if ( ($per_page > 0) && ($current_page > 0) ) {
				$valid = true;
			}
		}

		return $valid;
	}

	public static function getPaginationInfo( Collection $collection, $per_page, $current_page, $url = null, $url_has_parameters = false ) {

		$per_page = intval($per_page);
		$current_page = intval($current_page);

		$total = count($collection);

	    $last_page = intval(ceil($total / $per_page));

	    if( $current_page < $last_page ) {

		    $from = $per_page * $current_page - $per_page + 1;
		    $to = $from - 1 + $per_page;

	    } elseif( $current_page == $last_page ) {

		    $from = $per_page * $current_page - $per_page + 1;
		    $to = $total;

	    } else {

	    	$from = null;
	    	$to = null;

	    }

	    $urls = self::makePaginationURLs( $url, $url_has_parameters, $per_page, $last_page, $current_page );

	    $pagination_information = [
			"total" => $total,
			"per_page" => $per_page,
			"current_page" => $current_page,
			"last_page" => $last_page,
			"next_page_url" => $urls['next_page_url'],
			"prev_page_url" => $urls['prev_page_url'],
			"from" => $from,
			"to" => $to
	    ];

	    return $pagination_information;

	}

	private static function makePaginationURLs( $url, $url_has_parameters, $per_page, $last_page, $current_page ) {		

		$url = ( ($url_has_parameters) ? $url . '&_paginate=' : $url.'?_paginate=' ) . $per_page;

		$next_page_url = ( ($current_page >= $last_page) || ($current_page < 1) ) ? null : $url . '&_page=' . ($current_page + 1) ;

		$prev_page_url = ( ($current_page <= 1) || ($current_page > $last_page) ) ? null : $url . '&_page=' . ($current_page - 1) ;

		$urls = [
			"next_page_url" => $next_page_url,
			"prev_page_url" => $prev_page_url
		];

		return $urls;

	}
}