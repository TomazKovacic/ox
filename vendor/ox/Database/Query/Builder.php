<?php

namespace ox\Database\Query;

use Closure;
//use ox\Support\Collection;
use ox\Database\ConnectionInterface;
use ox\Database\Query\Grammars\Grammar;
use ox\Database\Query\Processors\Processor;

use ox\Support\Arr;
use ox\Support\Str;

class Builder {
    
    protected $connection;
    
    protected $grammar;
    
    protected $processor;
    
    protected $bindings = [
        'select' => [],
        'join'   => [],
        'where'  => [],
        'having' => [],
        'order'  => [],
        'union'  => [],
    ];
    
    public $aggregate;
    
    public $columns;
    
    public $distinct = false;
    
    public $from;
    
    public $joins;
    
    public $wheres;
    
    public $groups;
    
    public $havings;
    
    public $orders;
    
    public $limit;
    
    public $offset;
    
    public $unions;
    
    public $lock;
    
    public $unionLimit;
    
    public $unionOffset;
    
    public $unionOrders;
    

    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'like binary', 'not like', 'between', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to',
    ];
    
    protected $useWritePdo = false;
    
    
	public function __construct(ConnectionInterface $connection,
                                Grammar $grammar,
                                Processor $processor)
	{
		$this->grammar = $grammar;
		$this->processor = $processor;
		$this->connection = $connection;
	}
    
    public function select($columns = ['*']) {
        
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }
    
    public function selectRaw($expression, array $bindings = []) {
        $this->addSelect(new Expression($expression));

        if ($bindings) {
            $this->addBinding($bindings, 'select');
        }

        return $this;
    }
    
    public function addSelect($column) {
        
        $column = is_array($column) ? $column : func_get_args();
        $this->columns = array_merge((array) $this->columns, $column);
        return $this;
    }
    
    public function distinct() {
        
        $this->distinct = true;
        return $this;
    }
    
    public function from($table) {
        
        $this->from = $table;
        return $this;
    }
    
    //join
	public function join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false) {
		if ($one instanceof Closure)
		{
			$this->joins[] = new JoinClause($this, $type, $table);

			call_user_func($one, end($this->joins));
		}

		else
		{
			$join = new JoinClause($this, $type, $table);

			$this->joins[] = $join->on(
				$one, $operator, $two, 'and', $where
			);
		}

		return $this;
	}
    
    
	public function joinWhere($table, $one, $operator, $two, $type = 'inner') {
		return $this->join($table, $one, $operator, $two, $type, true);
	}
    
    
	public function leftJoin($table, $first, $operator = null, $second = null) {
		return $this->join($table, $first, $operator, $second, 'left');
	}
    
	public function leftJoinWhere($table, $one, $operator, $two) {
		return $this->joinWhere($table, $one, $operator, $two, 'left');
	}
    
    //when ??
    
	public function where($column, $operator = null, $value = null, $boolean = 'and')
	{
        //print 'Q-builder::where ... <br>';
        //print  'QB: func_num_args:' . func_num_args() . '<br>';
        //print_r2( func_get_args() );
        //$arr = compact('column', '$perator', 'value', 'boolean'); print_r2( $arr );
        
		if (func_num_args() == 2)
		{
			list($value, $operator) = array($operator, '=');
		}
		elseif ($this->invalidOperatorAndValue($operator, $value))
		{
			throw new \InvalidArgumentException("Value must be provided.");
		}


		if ($column instanceof Closure)
		{
			return $this->whereNested($column, $boolean);
		}


		if ( ! in_array(strtolower($operator), $this->operators, true))
		{
			list($value, $operator) = array($operator, '=');
		}

		if ($value instanceof Closure)
		{
			return $this->whereSub($column, $operator, $value, $boolean);
		}

		if (is_null($value))
		{
			return $this->whereNull($column, $boolean, $operator != '=');
		}

		$type = 'Basic';

		$this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean'); 
        //print_r2($this->wheres);

		if ( ! $value instanceof Expression)
		{
			$this->bindings[] = $value;
		}

		return $this;
	}
    
    
    //L   
    protected function addArrayOfWheres($column, $boolean)
    {
        return $this->whereNested(function ($query) use ($column) {
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    call_user_func_array([$query, 'where'], $value);
                } else {
                    $query->where($key, '=', $value);
                }
            }
        }, $boolean);
    }
    
    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'or');
    }
    
    
    protected function invalidOperatorAndValue($operator, $value)
    {
        $isOperator = in_array($operator, $this->operators);

        return $isOperator && $operator != '=' && is_null($value);
    }
    
    public function whereRaw($sql, array $bindings = [], $boolean = 'and')
    {
        $type = 'raw';

        $this->wheres[] = compact('type', 'sql', 'boolean');

        $this->addBinding($bindings, 'where');

        return $this;
    }
    
    public function orWhereRaw($sql, array $bindings = [])
    {
        return $this->whereRaw($sql, $bindings, 'or');
    }
    
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $type = 'between';

        $this->wheres[] = compact('column', 'type', 'boolean', 'not');

        $this->addBinding($values, 'where');

        return $this;
    }
    
    public function orWhereBetween($column, array $values)
    {
        return $this->whereBetween($column, $values, 'or');
    }

    
    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        return $this->whereBetween($column, $values, $boolean, true);
    }
    
    public function orWhereNotBetween($column, array $values)
    {
        return $this->whereNotBetween($column, $values, 'or');
    }
    

    public function whereNested(Closure $callback, $boolean = 'and')
    {
        $query = $this->forNestedWhere();

        call_user_func($callback, $query);

        return $this->addNestedWhereQuery($query, $boolean);
    }
    
    public function forNestedWhere()
    {
        $query = $this->newQuery();

        return $query->from($this->from);
    }
    
    public function addNestedWhereQuery($query, $boolean = 'and')
    {
        if (count($query->wheres)) {
            $type = 'Nested';

            $this->wheres[] = compact('type', 'query', 'boolean');

            $this->addBinding($query->getBindings(), 'where');
        }

        return $this;
    }
    
        protected function whereSub($column, $operator, Closure $callback, $boolean)
    {
        $type = 'Sub';

        $query = $this->newQuery();
        call_user_func($callback, $query);

        $this->wheres[] = compact('type', 'column', 'operator', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }
    
    
    public function whereExists(Closure $callback, $boolean = 'and', $not = false)
    {
        $query = $this->newQuery();
        call_user_func($callback, $query);

        return $this->addWhereExistsQuery($query, $boolean, $not);
    }
   
    
    public function orWhereExists(Closure $callback, $not = false)
    {
        return $this->whereExists($callback, 'or', $not);
    }
    
    public function whereNotExists(Closure $callback, $boolean = 'and')
    {
        return $this->whereExists($callback, $boolean, true);
    }
    
    
    public function orWhereNotExists(Closure $callback)
    {
        return $this->orWhereExists($callback, true);
    }

    
    public function addWhereExistsQuery(Builder $query, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotExists' : 'Exists';

        $this->wheres[] = compact('type', 'operator', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }
    
    
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotIn' : 'In';

        if ($values instanceof static) {
            return $this->whereInExistingQuery(
                $column, $values, $boolean, $not
            );
        }

        if ($values instanceof Closure) {
            return $this->whereInSub($column, $values, $boolean, $not);
        }

        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $this->wheres[] = compact('type', 'column', 'values', 'boolean');

        $this->addBinding($values, 'where');

        return $this;
    }
    
    
    public function orWhereIn($column, $values)
    {
        return $this->whereIn($column, $values, 'or');
    }
    
    
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        return $this->whereIn($column, $values, $boolean, true);
    }
    
    
    public function orWhereNotIn($column, $values)
    {
        return $this->whereNotIn($column, $values, 'or');
    }
    
    
    protected function whereInSub($column, Closure $callback, $boolean, $not)
    {
        $type = $not ? 'NotInSub' : 'InSub';

        call_user_func($callback, $query = $this->newQuery());

        $this->wheres[] = compact('type', 'column', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }
    
    
    protected function whereInExistingQuery($column, $query, $boolean, $not)
    {
        $type = $not ? 'NotInSub' : 'InSub';

        $this->wheres[] = compact('type', 'column', 'query', 'boolean');

        $this->addBinding($query->getBindings(), 'where');

        return $this;
    }
    

    public function whereNull($column, $boolean = 'and', $not = false)
    {
        $type = $not ? 'NotNull' : 'Null';

        $this->wheres[] = compact('type', 'column', 'boolean');

        return $this;
    }
    
    
    public function orWhereNull($column)
    {
        return $this->whereNull($column, 'or');
    }
    
    
    public function whereNotNull($column, $boolean = 'and')
    {
        return $this->whereNull($column, $boolean, true);
    }
    
    public function orWhereNotNull($column)
    {
        return $this->whereNotNull($column, 'or');
    }
    
    public function whereDate($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Date', $column, $operator, $value, $boolean);
    }
    
    
    public function orWhereDate($column, $operator, $value)
    {
        return $this->whereDate($column, $operator, $value, 'or');
    }
    
    
    public function whereDay($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Day', $column, $operator, $value, $boolean);
    }
    
    public function whereMonth($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Month', $column, $operator, $value, $boolean);
    }
    
    public function whereYear($column, $operator, $value, $boolean = 'and')
    {
        return $this->addDateBasedWhere('Year', $column, $operator, $value, $boolean);
    }
    

    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and')
    {
        $this->wheres[] = compact('column', 'type', 'boolean', 'operator', 'value');

        $this->addBinding($value, 'where');

        return $this;
    }
    

    public function dynamicWhere($method, $parameters)
    {
        $finder = substr($method, 5);

        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $connector = 'and';

        $index = 0;

        foreach ($segments as $segment) {
            
            if ($segment != 'And' && $segment != 'Or') {
                $this->addDynamic($segment, $connector, $parameters, $index);

                $index++;
            }
            else {
                $connector = $segment;
            }
        }

        return $this;
    }
    

    protected function addDynamic($segment, $connector, $parameters, $index)
    {
        $bool = strtolower($connector);

        $this->where(Str::snake($segment), '=', $parameters[$index], $bool);
    }

    public function groupBy()
    {
        foreach (func_get_args() as $arg) {
            $this->groups = array_merge((array) $this->groups, is_array($arg) ? $arg : [$arg]);
        }

        return $this;
    }
    
    public function having($column, $operator = null, $value = null, $boolean = 'and')
    {
        $type = 'basic';

        $this->havings[] = compact('type', 'column', 'operator', 'value', 'boolean');

        if (! $value instanceof Expression) {
            $this->addBinding($value, 'having');
        }

        return $this;
    }
    
    
    public function orHaving($column, $operator = null, $value = null)
    {
        return $this->having($column, $operator, $value, 'or');
    }
    
    
    public function havingRaw($sql, array $bindings = [], $boolean = 'and')
    {
        $type = 'raw';

        $this->havings[] = compact('type', 'sql', 'boolean');

        $this->addBinding($bindings, 'having');

        return $this;
    }
    
    
    public function orHavingRaw($sql, array $bindings = [])
    {
        return $this->havingRaw($sql, $bindings, 'or');
    }
    

    public function orderBy($column, $direction = 'asc')
    {
        $property = $this->unions ? 'unionOrders' : 'orders';
        $direction = strtolower($direction) == 'asc' ? 'asc' : 'desc';

        $this->{$property}[] = compact('column', 'direction');

        return $this;
    }
    
    
    public function latest($column = 'created_at')
    {
        return $this->orderBy($column, 'desc');
    }
    
    
    public function oldest($column = 'created_at')
    {
        return $this->orderBy($column, 'asc');
    }
    
    
    public function orderByRaw($sql, $bindings = [])
    {
        $property = $this->unions ? 'unionOrders' : 'orders';

        $type = 'raw';

        $this->{$property}[] = compact('type', 'sql');

        $this->addBinding($bindings, 'order');

        return $this;
    }
    
    
    public function offset($value)
    {
        $property = $this->unions ? 'unionOffset' : 'offset';

        $this->$property = max(0, $value);

        return $this;
    }
    
    
    public function skip($value)
    {
        return $this->offset($value);
    }
    
    
    public function limit($value)
    {
        $property = $this->unions ? 'unionLimit' : 'limit';

        if ($value >= 0) {
            $this->$property = $value;
        }

        return $this;
    }
    
    
    public function take($value)
    {
        return $this->limit($value);
    }
    
    
    public function forPage($page, $perPage = 15)
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }
    
    
    public function forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
    {
        return $this->where($column, '>', $lastId)
                    ->orderBy($column, 'asc')
                    ->take($perPage);
    }
    
    
    public function union($query, $all = false)
    {
        if ($query instanceof Closure) {
            call_user_func($query, $query = $this->newQuery());
        }

        $this->unions[] = compact('query', 'all');

        $this->addBinding($query->getBindings(), 'union');

        return $this;
    }
    

    public function unionAll($query)
    {
        return $this->union($query, true);
    }
    
    
    public function lock($value = true)
    {
        $this->lock = $value;

        if ($this->lock) {
            $this->useWritePdo();
        }

        return $this;
    }
    
    
    public function lockForUpdate()
    {
        return $this->lock(true);
    }
    
    
    public function sharedLock()
    {
        return $this->lock(false);
    }
    
    
    public function toSql()
    {
        return $this->grammar->compileSelect($this);
    }
    
    
    public function find($id, $columns = ['*'])
    {
        return $this->where('id', '=', $id)->first($columns);
    }
    
    
    public function value($column)
    {
        $result = (array) $this->first([$column]);

        return count($result) > 0 ? reset($result) : null;
    }
    

    public function first($columns = ['*'])
    {
        $results = $this->take(1)->get($columns);

        return count($results) > 0 ? reset($results) : null;
    }
    
    
    public function get($columns = ['*'])
    {
        $original = $this->columns;

        if (is_null($original)) {
            $this->columns = $columns;
        }

        $results = $this->processor->processSelect($this, $this->runSelect());

        $this->columns = $original;

        return $results;
    }
    
    
    protected function runSelect()
    {
        return $this->connection->select($this->toSql(), $this->getBindings(), ! $this->useWritePdo);
    }
    
    
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $total = $this->getCountForPagination($columns);

        $results = $this->forPage($page, $perPage)->get($columns);

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
    

    public function simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page')
    {
        $page = Paginator::resolveCurrentPage($pageName);

        $this->skip(($page - 1) * $perPage)->take($perPage + 1);

        return new Paginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }
    
    public function getCountForPagination($columns = ['*'])
    {
        $this->backupFieldsForCount();

        $this->aggregate = ['function' => 'count', 'columns' => $this->clearSelectAliases($columns)];

        $results = $this->get();

        $this->aggregate = null;

        $this->restoreFieldsForCount();

        if (isset($this->groups)) {
            return count($results);
        }

        return isset($results[0]) ? (int) array_change_key_case((array) $results[0])['aggregate'] : 0;
    }
    
    
    protected function backupFieldsForCount()
    {
        foreach (['orders', 'limit', 'offset', 'columns'] as $field) {
            $this->backups[$field] = $this->{$field};

            $this->{$field} = null;
        }

        foreach (['order', 'select'] as $key) {
            $this->bindingBackups[$key] = $this->bindings[$key];

            $this->bindings[$key] = [];
        }
    }
    
    
    protected function clearSelectAliases(array $columns)
    {
        return array_map(function ($column) {
            return is_string($column) && ($aliasPosition = strpos(strtolower($column), ' as ')) !== false
                    ? substr($column, 0, $aliasPosition) : $column;
        }, $columns);
    }
    

    protected function restoreFieldsForCount()
    {
        foreach (['orders', 'limit', 'offset', 'columns'] as $field) {
            $this->{$field} = $this->backups[$field];
        }

        foreach (['order', 'select'] as $key) {
            $this->bindings[$key] = $this->bindingBackups[$key];
        }

        $this->backups = [];
        $this->bindingBackups = [];
    }
    
    
    
    public function chunk($count, callable $callback)
    {
        $results = $this->forPage($page = 1, $count)->get();

        while (count($results) > 0) {

            if (call_user_func($callback, $results) === false) {
                return false;
            }

            $page++;

            $results = $this->forPage($page, $count)->get();
        }

        return true;
    }
    
    
    
    public function chunkById($count, callable $callback, $column = 'id')
    {
        $lastId = null;

        $results = $this->forPageAfterId($count, 0, $column)->get();

        while (! empty($results)) {
            if (call_user_func($callback, $results) === false) {
                return false;
            }

            $lastId = last($results)->{$column};

            $results = $this->forPageAfterId($count, $lastId, $column)->get();
        }

        return true;
    }
    
    
    
    public function each(callable $callback, $count = 1000)
    {
        if (is_null($this->orders) && is_null($this->unionOrders)) {
            throw new RuntimeException('You must specify an orderBy clause when using the "each" function.');
        }

        return $this->chunk($count, function ($results) use ($callback) {
            foreach ($results as $key => $value) {
                if ($callback($value, $key) === false) {
                    return false;
                }
            }
        });
    }
    
    
    
    public function pluck($column, $key = null)
    {
        $results = $this->get(is_null($key) ? [$column] : [$column, $key]);

        // If the columns are qualified with a table or have an alias, we cannot use
        // those directly in the "pluck" operations since the results from the DB
        // are only keyed by the column itself. We'll strip the table out here.
        return Arr::pluck(
            $results,
            $this->stripTableForPluck($column),
            $this->stripTableForPluck($key)
        );
    }
    
    
    
    public function lists($column, $key = null)
    {
        return $this->pluck($column, $key);
    }
    
    protected function stripTableForPluck($column)
    {
        return is_null($column) ? $column : last(preg_split('~\.| ~', $column));
    }
    
    public function implode($column, $glue = '')
    {
        return implode($glue, $this->pluck($column));
    } 
    
    public function exists()
    {
        $sql = $this->grammar->compileExists($this);

        $results = $this->connection->select($sql, $this->getBindings(), ! $this->useWritePdo);

        if (isset($results[0])) {
            $results = (array) $results[0];

            return (bool) $results['exists'];
        }

        return false;
    }
    
    public function count($columns = '*')
    {
        if (! is_array($columns)) {
            $columns = [$columns];
        }

        return (int) $this->aggregate(__FUNCTION__, $columns);
    } 
    
    public function min($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }
    
    public function max($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }
    
    public function sum($column)
    {
        $result = $this->aggregate(__FUNCTION__, [$column]);

        return $result ?: 0;
    }
    
    public function avg($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }
    
    public function average($column)
    {
        return $this->avg($column);
    }
    
   public function aggregate($function, $columns = ['*'])
    {
        $this->aggregate = compact('function', 'columns');

        $previousColumns = $this->columns;

        $previousSelectBindings = $this->bindings['select'];

        $this->bindings['select'] = [];

        $results = $this->get($columns);

        $this->aggregate = null;

        $this->columns = $previousColumns;

        $this->bindings['select'] = $previousSelectBindings;

        if (isset($results[0])) {
            $result = array_change_key_case((array) $results[0]);

            return $result['aggregate'];
        }
    }

    
    public function insert(array $values)
    {
        if (empty($values)) {
            return true;
        }
        
        if (! is_array(reset($values))) {
            $values = [$values];
        }
        else {
            foreach ($values as $key => $value) {
                ksort($value);
                $values[$key] = $value;
            }
        }
        
        $bindings = [];

        foreach ($values as $record) {
            foreach ($record as $value) {
                $bindings[] = $value;
            }
        }
        
        $sql = $this->grammar->compileInsert($this, $values);
        
        $bindings = $this->cleanBindings($bindings);

        return $this->connection->insert($sql, $bindings);
    }


    public function insertGetId(array $values, $sequence = null)
    {
        $sql = $this->grammar->compileInsertGetId($this, $values, $sequence);

        $values = $this->cleanBindings($values);

        return $this->processor->processInsertGetId($this, $sql, $values, $sequence);
    }
    

    public function update(array $values)
    {
        $bindings = array_values(array_merge($values, $this->getBindings()));

        $sql = $this->grammar->compileUpdate($this, $values);

        return $this->connection->update($sql, $this->cleanBindings($bindings));
    }
    

    public function updateOrInsert(array $attributes, array $values = [])
    {
        if (! $this->where($attributes)->exists()) {
            return $this->insert(array_merge($attributes, $values));
        }

        return (bool) $this->where($attributes)->take(1)->update($values);
    }
    
    
    public function increment($column, $amount = 1, array $extra = [])
    {
        $wrapped = $this->grammar->wrap($column);

        $columns = array_merge([$column => $this->raw("$wrapped + $amount")], $extra);

        return $this->update($columns);
    }
    
    public function decrement($column, $amount = 1, array $extra = [])
    {
        $wrapped = $this->grammar->wrap($column);

        $columns = array_merge([$column => $this->raw("$wrapped - $amount")], $extra);

        return $this->update($columns);
    }
    
    public function delete($id = null)
    {
        if (! is_null($id)) {
            $this->where('id', '=', $id);
        }

        $sql = $this->grammar->compileDelete($this);

        return $this->connection->delete($sql, $this->getBindings());
    }

    public function truncate()
    {
        foreach ($this->grammar->compileTruncate($this) as $sql => $bindings) {
            $this->connection->statement($sql, $bindings);
        }
    }
    
    public function mergeWheres($wheres, $bindings)
    {
        $this->wheres = array_merge((array) $this->wheres, (array) $wheres);

        $this->bindings['where'] = array_values(array_merge($this->bindings['where'], (array) $bindings));
    }
    

    protected function cleanBindings(array $bindings)
    {
        return array_values(array_filter($bindings, function ($binding) {
            return ! $binding instanceof Expression;
        }));
    }
    

    public function raw($value)
    {
        return $this->connection->raw($value);
    }
    

    public function getBindings()
    {
        return Arr::flatten($this->bindings);
    }
    
    
    public function getRawBindings()
    {
        return $this->bindings;
    }
    
    
    public function setBindings(array $bindings, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        $this->bindings[$type] = $bindings;

        return $this;
    }
    
    public function addBinding($value, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new InvalidArgumentException("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }
    
    public function mergeBindings(Builder $query)
    {
        $this->bindings = array_merge_recursive($this->bindings, $query->bindings);

        return $this;
    }
    
    public function getConnection()
    {
        return $this->connection;
    }
    
    
    public function getProcessor()
    {
        return $this->processor;
    }
    
    
    public function getGrammar()
    {
        return $this->grammar;
    }
    
    public function useWritePdo()
    {
        $this->useWritePdo = true;

        return $this;
    }
    
    public function __call($method, $parameters)
    {
        //if (static::hasMacro($method)) {
        //    return $this->macroCall($method, $parameters);
        //}

        if (Str::startsWith($method, 'where')) {
            return $this->dynamicWhere($method, $parameters);
        }

        $className = static::class;

        throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }
    
}