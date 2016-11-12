<?php declare(strict_types=1);

namespace Personnage\ActiveRecord;

use ArrayAccess;
use DateTime;
use DateTimeInterface;
use PDO;
use Personnage\ActiveRecord\Contracts\Incremental;
use Personnage\ActiveRecord\Contracts\Timestampable;

abstract class BaseModel implements ArrayAccess
{
    protected $table;

    protected $attributes = [];

    public $exists = false;

    abstract protected function getPdo();

    public function __construct(array $attributes = [])
    {
        $this->boot();

        $this->fill($attributes);
    }

    protected function boot()
    {
        //
    }

    public function isDirty($attributes = null)
    {
        return false;
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        if (isset($this->table)) {
            return $this->table;
        }

        $components = explode('\\', get_class($this));
        $className = array_pop($components);

        return mb_strtolower($className);
    }

    /**
     * Set the table associated with the model.
     *
     * @param  string  $table
     * @return $this
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
    }

    public function setAttribute($key, $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function save(array $options = [])
    {
        if (! $this->exists) {
            $saved = $this->performInsert();
        } else {
            $saved = $this->isDirty() ? $this->performUpdate($query, $options) : true;
        }

        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }

    protected function finishSave()
    {
        return true; // stub
    }

    public function update(array $attributes = [])
    {
        if (! $this->exists) {
            return false;
        }

        return $this->fill($attributes)->save();
    }

    public function statement($query, array $bindings = [])
    {
        $statement = $this->getPdo()->prepare($query);

        foreach ($bindings as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $value = $value->format(DateTime::ISO8601);
            } elseif ($value === false) {
                $value = 0;
            }

            $statement->bindValue(
                is_string($key) ? $key : $key + 1, $value,
                is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }

        return $statement->execute();
    }

    protected function performInsert()
    {
        if ($this->usesTimestamps()) {
            $this->updateTimestamp();
        }

        $attributes = $this->attributes;

        if ($this->isIncrementing()) {
            $id = $this->doInsertLastId($attributes, $keyName = $this->getKeyName());
            $this->setAttribute($keyName, $id);
        } else {
            $this->doInsert($attributes);
        }

        $this->exists = true;

        return true;
    }

    /**
     * Insert a new record into the database.
     *
     * @param  array  $values
     * @return bool
     */
    protected function doInsert(array $values): bool
    {
        if (empty($values)) {
            return true;
        }

        $sql = $this->compileInsert($values);

        $bindings = array_values($values);

        return $this->statement($sql, $bindings);
    }

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param  array   $values
     * @param  string  $sequence
     * @return int
     */
    protected function doInsertLastId(array $values, string $sequence = 'id'): int
    {
        $sql = $this->compileInsert($values).' returning '.$sequence;

        $bindings = array_values($values);

        if ($this->statement($sql, $bindings)) {
            return (int) $this->getPdo()->lastInsertId($sequence);
        }
    }

    private function compileInsert(array $values): string
    {
        $columns = array_map(function ($value): string {
            return '"'.str_replace('"', '""', $value).'"';
        }, array_keys($values));

        $parameters = array_fill(0, count($values), '?');

        $table = $this->getTable();
        $columns = implode(', ', $columns);
        $parameters = implode(', ', $parameters);

        return "insert into $table ($columns) values ($parameters)";
    }

    protected function performUpdate()
    {
        //
    }

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps()
    {
        return $this instanceof Timestampable;
    }

    /**
     * Determine if the IDs are incrementing.
     *
     * @return bool
     */
    public function isIncrementing()
    {
        return $this instanceof Incremental;
    }

    protected function updateTimestamp()
    {
        $time = $this->freshTimestamp();

        if (! $this->isDirty($this->getUpdatedAtColumn())) {
            $this->setUpdatedAt($time);
        }

        if (! $this->exists && ! $this->isDirty($this->getCreatedAtColumn())) {
            $this->setCreatedAt($time);
        }
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
}
