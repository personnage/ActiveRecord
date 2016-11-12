<?php declare(strict_types=1);

namespace Personnage\ActiveRecord\Contracts;

interface Timestampable
{
    /**
     * Get a fresh timestamp for the model.
     *
     * @return DateTimeInterface
     */
    public function freshTimestamp();

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn();

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn();
}
