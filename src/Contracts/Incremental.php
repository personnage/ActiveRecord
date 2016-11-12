<?php declare(strict_types=1);

namespace Personnage\ActiveRecord\Contracts;

interface Incremental
{
    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * Set the primary key for the model.
     *
     * @param  string  $key
     * @return $this
     */
    public function setKeyName(string $key);
}
