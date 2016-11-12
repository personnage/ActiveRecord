<?php declare(strict_types=1);

namespace Personnage\ActiveRecord;

use Personnage\ActiveRecord\BaseModel;
use Personnage\ActiveRecord\Contracts\Incremental;
use Personnage\ActiveRecord\Contracts\Timestampable;
use Personnage\ActiveRecord\Timestamps;

abstract class Model extends BaseModel implements Incremental, Timestampable
{
    use Timestamps;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * {@inheritdoc}
     */
    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setKeyName(string $key): self
    {
        $this->primaryKey = $key;

        return $this;
    }
}
