<?

namespace website\packages;

use core\data\SQL;
use core\EntityCollection;

trait AdvancedQueryEntityTrait {

	/**
	 * Select entities from the database that match the specified criteria.
	 * Optionally specify order, limit/offset, columns, joins, etc.
	 * The result is an array of objects of the called entity type.
	 * 
	 * Changed to include a group-by argument.
	 */
	public static function find($where = null, $orderBy = null, $limit = 0, $offset = 0, $columns = null, $joins = null, $having = null, $glue = "\n", $groupBy = null) {

		if(empty($columns)) {
			$columns = static::qualifiedColumns();
		}

		if($database = static::database()) {

			$query = static::select($columns, $where, $groupBy, $having, $orderBy, $limit, $offset, $joins, $glue);

			if($result = $database->query($query($database))) {

				$entities = array();

				foreach($database->fetchRows($result) as $values) {
					$entities[] = static::create($values);
				}

				return $entities;
			}
		}
	}
}