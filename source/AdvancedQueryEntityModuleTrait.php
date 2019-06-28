<?

namespace website\packages;

use core\data\SQL;
use core\EntityCollection;

trait AdvancedQueryEntityModuleTrait {

	/**
	 * Advanced Find Query (Override) 
	 *
	 * Base query does not allow `group by` functionality -- this does.
	 * TODO - Add this into a trait, which classes can use.
	 * Changes so far is the ability to group by
	 * 
	 * @param User $user
	 *
	 * @return EntityCollection
	 */
	protected function _find($request, $target, &$values) {

		$class = $this->classname();
		$columns = $this->_columns($request, $target);
		$joins = $this->_joins($request, $target);
		$having = $this->_having($request, $target);

		if(empty($request['order'])) {
			$orderBy = $this->_orderBy($request, $target);
		} else {
			$orderBy = $request['order'];
		}

		if(empty($request['where']) == false && is_array($request['where'])) {
			$_where = $request['where'];
		} else {
			$_where = array();
		}

		$_columns = $columns;

		if(empty($request['q']) == false) {
			if($fulltextSearch = $this->option('fulltext-search')) {

				$relevance = $this->option('relevance-column') ?: uniqid('_');

				if($this->option('qualify-search-columns')) {
					$fulltextSearch = $this->qualifyColumns($fulltextSearch);
				}

				$_columns[$relevance] = SQL::match($fulltextSearch, $request['q']);

				$orderBy[] = SQL::desc(SQL::identifier($relevance));

				$_where[] = SQL::apply(SQL::greaterThan(0), $_columns[$relevance]);

			} elseif($keywordSearch = $this->option('keyword-search')) {
				if(is_callable($keywordSearch)) {
					$_where[] = $keywordSearch($request, $target, $this);
				} else {

					$keywords = preg_split('/\s+/', $request['q']);

					if($this->option('qualify-search-columns')) {
						$keywordSearch = $this->qualifyColumns($keywordSearch);
					}

					$conditions = array_map(function ($keyword) use ($keywordSearch) {
						return array_map(function ($column) use ($keyword) {
							return SQL::apply(SQL::locate($keyword), is_string($column) ? SQL::identifier($column) : $column);
						}, $keywordSearch);
					}, $keywords);

					$_where[] = SQL::condition($conditions, 'AND');

				}
			} elseif($name = $this->option('entity-name')) {
				$_where[$name] = SQL::locate($request['q']);
			}
		}

		$where = array_merge($this->_where($request, $target), $_where);

		if(is_object($target) && $junction = $this->_junction($request, $target)) {
			$where[] = $junction;
		}

		if(($parent = $this->option('parent-column'))
		&& array_key_exists($parent, $columns)
		&& $this->option('parent-autofilter')) {
			if(empty($_where) && is_object($target) == false) {
				$where[$parent] = isset($request['parent']) ? $request['parent'] : 0;
			} else {
				// TODO filter out entries where parent has been discarded...
			}
		}

		$limit = 0;
		$offset = 0;

		if(isset($request['limit']) && $request['limit'] > 0) {

			$limit = $request['limit'];

			if(isset($request['offset']) && $request['offset'] > 0) {
				$offset = $request['offset'];
			}

		} elseif(empty($_where)
		&& ($ordinal = $this->option('ordinal-column'))
		&& array_key_exists($ordinal, $columns)
		&& $this->_sortable($request, $target)) {

			$values['sortable'] = true;

		} elseif(isset($request['page-size']) && $request['page-size'] > 0) {
			if($count = $class::count($where, null, $having, $joins)) {

				$limit = $request['page-size'];

				if(isset($request['page']) && $request['page'] > 0) {
					$offset = ($request['page'] - 1) * $limit;
				}

				$values['count'] = $count;
				$values['limit'] = $limit;
				$values['offset'] = $offset;
				$values['pages'] = (int) ceil($count / $limit);

			}
		}

		$groupBy = null;
		// Group by
		if(isset($request['group']) && is_array($request['group'])) {

			// echo "<pre>";
			// var_dump($request['group']);
			// die();
			// error_reporting(-1);

			$groupBy = $request['group'];
		}


		$targets = array();

		if($entities = $class::find($where, $orderBy, $limit, $offset, $_columns, $joins, $having, "\n", $groupBy)) {

			if($get = $this->option('get-entity')) {
				foreach($entities as $entity) {

					if($_entity = $get($request, $entity, $this)) {
						$entity = $_entity;
					}

					$targets[$this->_location($request, $entity)] = $entity;

				}
			} else {
				foreach($entities as $entity) {
					$targets[$this->_location($request, $entity)] = $entity;
				}
			}

			if(empty($request['tree']) == false
			&& ($parent = $this->option('parent-column'))
			&& array_key_exists($parent, $columns)) {

				foreach($targets as $_ => &$entity) {
					$entity->_children = 0;
				}

				$parentChildren = $class::count($this->_where($request, $target), array($parent), array('COUNT(*) > 0'));

				foreach($parentChildren as $parent => $children) {
					if(array_key_exists($parent, $targets)) {
						$targets[$parent]->_children = $children;
					}
				}

			}

		}

		return $targets;
	}
}