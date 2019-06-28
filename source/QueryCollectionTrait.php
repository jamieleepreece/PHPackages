<?

namespace website\api;

use core\data\SQL;

use core\EntityCollection;

trait QueryCollectionTrait {

	protected static $index_count = 0;

    /**
     * For use with the Entity Collection method, 'select'. 
     * Return viewable columns, in a collection. Set up to work statically, with the constructor.
     *
     * @param string $where
     * 
     * @return array
     */
    public function getCollectionViewable($where = null)
    {

    	$selectColumns = array();

    	foreach (self::$_columns as $column => $options) {

    		if (isset(self::$_columns[$column]['api_viewable']) && self::$_columns[$column]['api_viewable'] !== false) {

    			$selectColumns[] = $column;
    		}
    	}

    	return $selectColumns;
    }

    /**
     * For use with the Entity Collection method, 'index'. 
     * For use if the index cannot be sorted by id. Increments an integer.
     * No need to clear the increment at this point in time.
     *
     * @param string $where
     * 
     * @return array
     */
    public function getIndex() {

    	if (isset(self::$_columns['id']['api_viewable']) && self::$_columns['id']['api_viewable'] !== false) {
    		return $ref = 'id';
    	}
    	else{

	    	// Pass function through to a variable
	    	$ref = array($this, '_getIndex');

    		return $ref;	
    	}
    }

    /**
     * Child function called by getIndex()
     */
    public function _getIndex() {

    	$current = static::$index_count;
    	static::$index_count ++;

    	return $current;
    }
}