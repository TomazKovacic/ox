<?php

namespace ox\Model;

use ox\Database\Query\Builder as QueryBuilder;

use ox\Database\ConnectionResolverInterface as Resolver;


class Model {

    protected $connection;
    protected $table;
    protected $primaryKey = 'id';
    protected $perPage = 15;
    protected $attributes = array();
    protected $original = array();
    protected $relations = array(); //**

    protected $with = [];

    public $exists = false;

    protected static $resolver;


    public function __construct(array $attributes = array()) {

        ;
    }

    public function newInstance($attributes = array(), $exists = false) { //L

        $model = new static((array) $attributes);
        $model->exists = $exists;
        return $model;
    }

    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance([], true);

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->connection);

        return $model;
    }


    public static function create(array $attributes) {

        $model = new static($attributes);
        $model->save();
        return $model;
    }


    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->attributes = $attributes;

        if ($sync) {
            $this->syncOriginal();
        }

        return $this;
    }

    public function syncOriginal()
    {
        $this->original = $this->attributes;

        return $this;
    }

    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    //firstOrCreate
    //firstOrNew
    //firstByAttributes


    public static function query() {


        return with(new static)->newQuery();
    }

    //on


    public static function all($columns = array('*')) {

        //print 'Model: all()<br>';

        $instance = new static;

        //print '------------------ model instance (empty) <br>';  print_r2($instance);
        //$result = $instance->newQuery()->get($columns);
        //print '------------------ result: <br>';  print_r2($result);

        //return $result;
        return $instance->newQuery()->get($columns);
    }


	public static function find($id, $columns = array('*'))
	{
		if (is_array($id) && empty($id)) return new Collection;

		$instance = new static;

		return $instance->newQuery()->find($id, $columns);
	}


  public static function where($column, $operator = null, $value = null, $boolean = 'and') {

    //public function where($column, $operator = null, $value = null, $boolean = 'and')

    $instance = new static;

    return $instance->newQuery()->where($column, $operator, $value, $boolean);

  }



    public function newQuery($excludeDeleted = true) {

        //print 'Model::newQuery() <br> ';

        //$builder = $this->newOxBuilder($this->newBaseQueryBuilder());
		//$builder->setModel($this)->with($this->with);



        $builder = $this->newOxBuilder( $this->newQueryBuilder() );

        $builder->setModel($this)->with($this->with);

		if ($excludeDeleted && $this->softDelete)
		{
			//L: $builder->whereNull($this->getQualifiedDeletedAtColumn());

            //print ' Test Success <br>';

            $builder->orWhere('record_status', 1);
		}

        //if($excludeDeleted) { print 'Test -X-$excludeDeleted <br>'; }
        //if($this->softDelete) { print 'Test -X-$this->softDelete <br>'; }


        //$builder->setModel($this)->with($this->with);

        //print 'Builder M: '; print_r2($builder);
		return $builder;
    }


	public function newQueryWithDeleted()
	{
		return $this->newQuery(false);
	}


    // --

	public function newOxBuilder($query)
	{
		return new Builder($query);
	}

	public function newQueryBuilder()
	{

        $conn = $this->getConnection();

        //print 'CONN:'; print_r2($conn);//exit();
        $grammar = $conn->getQueryGrammar();

        return new QueryBuilder($conn, $grammar, $conn->getPostProcessor());

	}


	public static function withTrashed()
	{
		return with(new static)->newQueryWithDeleted();
	}


	public static function onlyTrashed()
	{
		$instance = new static;

		$column = $instance->getQualifiedDeletedAtColumn();

		return $instance->newQueryWithDeleted()->whereNotNull($column);
	}


    public function getTable()
    {
        if (isset($this->table)) {
            return $this->table;
        }

        return str_replace('\\', '', Str::snake(Str::plural(class_basename($this))));
    }


    protected function performUpdate(Builder $query) {

        $dirty = $this->getDirty();

        if (count($dirty) > 0) {

			if ($this->timestamps) {
				$this->updateTimestamps();
			}

            $dirty = $this->getDirty();

            $this->setKeysForSaveQuery($query)->update($dirty);

        }

        return true;
    }


	protected function performInsert(Builder $query)
	{

		if ($this->timestamps)
		{
			$this->updateTimestamps();
		}

		$attributes = $this->attributes;

		if ($this->incrementing)
		{
			$this->insertAndSetId($query, $attributes);
		}

		else
		{
			$query->insert($attributes);
		}

		$this->exists = true;


		return true;
	}


	protected function performDeleteOnModel()
	{
		$query = $this->newQuery()->where($this->getKeyName(), $this->getKey());

		if ($this->softDelete) {

            //manjka koda za soft delete DEL DEL DEL

			//$this->{static::DELETED_AT} = $time = $this->freshTimestamp();
			//$query->update(array(static::DELETED_AT => $this->fromDateTime($time)));

            $query->update( array('record_status' => 0) );
		}
		else {
			$query->delete();
		}
	}


	protected function setKeysForSaveQuery(Builder $query)
	{
		$query->where($this->getKeyName(), '=', $this->getKeyForSaveQuery());

		return $query;
	}


	protected function getKeyForSaveQuery()
	{
		if (isset($this->original[$this->getKeyName()]))
		{
			return $this->original[$this->getKeyName()];
		}
		else
		{
			return $this->getAttribute($this->getKeyName());
		}
	}


	public function usesTimestamps()
	{
		return $this->timestamps;
	}


	public function getDirty()
	{
		$dirty = array();

		foreach ($this->attributes as $key => $value)
		{
			if ( ! array_key_exists($key, $this->original) || $value !== $this->original[$key])
			{
				$dirty[$key] = $value;
			}
		}

		return $dirty;
	}


	protected function finishSave(array $options)
	{
		$this->syncOriginal();

		//$this->fireModelEvent('saved', false);
		//if (array_get($options, 'touch', true)) $this->touchOwners();
	}



    public function getConnection()
    {
        return static::resolveConnection($this->getConnectionName());
    }

    public function getConnectionName()
    {
        return $this->connection;
    }

    public function setConnection($name)
    {
        $this->connection = $name;

        return $this;
    }

    public static function resolveConnection($connection = null)
    {
        return static::$resolver->connection($connection);
    }


    public static function getConnectionResolver()
    {
        return static::$resolver;
    }

    public static function setConnectionResolver(Resolver $resolver)
    {
        static::$resolver = $resolver;
    }

    public static function unsetConnectionResolver()
    {
        static::$resolver = null;
    }

    /*public function newInstance($attributes = array(), $exists = false) {

        $model = new static((array) $attributes);

        $model->exists = $exists;
        return $model;
    }*/


	public static function __callStatic($method, $parameters)
	{
		$instance = new static;
        print 'DEBUG: CALL STATIC: m: '. $method .'<br>'; print_r2($parameters);

		return call_user_func_array(array($instance, $method), $parameters);
	}

	public function getKey()
	{
		return $this->getAttribute($this->getKeyName());
	}


    public function getKeyName()
    {
        return $this->primaryKey;
    }


	public function isSoftDeleting()
	{
		return $this->softDelete;
	}




    public function save(array $options = array()) {

        $query = $this->newQueryWithDeleted();

        //$query = $this->newQueryWithoutScopes();

        if ($this->exists) {
            $saved = $this->performUpdate($query);

        } else {
            $saved = $this->performInsert($query);
        }

        if ($saved) $this->finishSave($options);
        return $saved;
    }

    //public function where() {
    //    print 'Model: where()';
    //}

    // ====

    public function update(array $attributes = array()) {

		if ( ! $this->exists) {
			return $this->newQuery()->update($attributes);
		}

		return $this->fill($attributes)->save();
    }


    public function delete() {

        if ($this->exists) {

            $this->performDeleteOnModel();
            $this->exists = false;
        }

        return true;

    }

    public function fill(array $attributes) {

        // TK za razliko od L so vsa polj fillable

        //print 'Object FILL test: <br><br>';  //print_r2($this);

        //todo: 1. implamentiraj preverbo,  ali je $value res atribut
        //todo   2. implementiraj guarded

        foreach($attributes as $key => $value) {

            $this->setAttribute($key, $value);


        }

        //print '--------------------- <br><br>'; //print_r2($this);
    }

    //  ----------------------------------------



    public function getAttribute($key) {

        //laravel ima še mutatorje in relationvalue

        return $this->getAttributeValue($key);
    }

    public function getAttributeValue($key) {

        $value = $this->getAttributeFromArray($key);


        //koda te datume

        return $value;

    }

    protected function getAttributeFromArray($key) {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    public function hasGetMutator($key)
  	{
  		return method_exists($this, 'get'.studly_case($key).'Attribute');
  	}


    public function setAttribute($key, $value) {

        //laravel ima mutatorje, verjetno nepotrebno

        $this->attributes[$key] = $value;
        return $this;

    }

    public function __get($key) {
        return $this->getAttribute($key);
    }


    public function __set($key, $value) {
        $this->setAttribute($key, $value);
    }

    public function offsetExists($offset) {
        return isset($this->$offset);
    }


    public function offsetGet($offset) {
        return $this->$offset;
    }

    public function offsetSet($offset, $value) {
        $this->$offset = $value;
    }


    public function offsetUnset($offset) {
        unset($this->$offset);
    }


    public function __isset($key) {
        return (isset($this->attributes[$key]) || isset($this->relations[$key])) ||
                ($this->hasGetMutator($key) && ! is_null($this->getAttributeValue($key)));
    }

    public function __unset($key) {
        unset($this->attributes[$key], $this->relations[$key]);
    }

}
