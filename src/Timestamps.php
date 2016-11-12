<?php declare(strict_types=1);

namespace Personnage\ActiveRecord;

use DateTime;
use DateTimeInterface;

trait Timestamps
{
    /**
     * Get a fresh timestamp for the model.
     *
     * @return DateTimeInterface
     */
    public function freshTimestamp(): DateTimeInterface
    {
        return new DateTime;
    }

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedAt($value): self
    {
        $this->{$this->getCreatedAtColumn()} = $value;

        return $this;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value): self
    {
        $this->{$this->getUpdatedAtColumn()} = $value;

        return $this;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn(): string
    {
        return 'created_at';
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn(): string
    {
        return 'updated_at';
    }
}
