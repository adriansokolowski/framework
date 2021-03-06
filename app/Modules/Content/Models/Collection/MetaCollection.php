<?php

namespace App\Modules\Content\Models\Collection;

use Nova\Database\ORM\Collection as BaseCollection;
use Nova\Database\ORM\Model;
use Nova\Support\Str;

use App\Modules\Content\Models\PostMeta as MetaItem;

use InvalidArgumentException;


class MetaCollection extends BaseCollection
{
    /**
     * Keys of the models that the collection was constructed with.
     *
     * @var array
     */
    protected $originalModelKeys = array();


    /**
     * MetaItemCollection constructor.
     *
     * @param array $items
     */
    public function __construct($items = array())
    {
        parent::__construct($items);

        $this->originalModelKeys = $this->modelKeys();

        $this->observeDeletions($this->items);
    }

    /**
     * Get the array of primary keys.
     *
     * @return array
     */
    public function modelKeys()
    {
        $keys = array();

        foreach ($this->items as $item) {
            if ($item instanceof Model) {
                $keys[] = $item->getKey();
            }
        }

        return $keys;
    }

    /**
     * Get the array of primary keys the collection was constructed with.
     *
     * @return array
     */
    public function originalModelKeys()
    {
        return $this->originalModelKeys;
    }

    /**
     * Add an item to the collection.
     *
     * @param mixed $item
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add($item)
    {
        if ($item instanceof MetaItem) {
            if (! is_null($this->findItem($item->key))) {
                $key = $item->key;

                throw new InvalidArgumentException("Unique key constraint failed. [$key]");
            }

            $this->observeDeletions(array($item));
        }

        $this->items[] = $item;

        return $this;
    }

    /**
     * Add an item to the collection.
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addItem($name, $value)
    {
        $item = new MetaItem(array(
            'key'   => $name,
            'value' => $value,
        ));

        $this->add($item);

        return $this;
    }

    /**
     * Get an item from collection.
     *
     * @param mixed $name
     * @return mixed
     */
    public function getItem($name)
    {
        if (! is_null($key = $this->findItem($name))) {
            return $this->get($key);
        }
    }

    /**
     * Get the collection key form an item key.
     *
     * @param mixed $name
     * @return mixed
     */
    public function findItem($name)
    {
        $collection = $this->where('key', $name);

        if ($collection->count() > 0) {
            return $collection->keys()->first();
        }
    }

    /**
     * Set deletion listeners on an array of items.
     *
     * @param array $items
     */
    protected function observeDeletions(array $items)
    {
        foreach ($items as $item) {
            if ($item instanceof MetaItem) {
                $this->observeDeletion($item);
            }
        }
    }

    /**
     * Set a deletion listener on an item.
     *
     * @param \App\Modules\Content\Models\PostMeta $item
     */
    protected function observeDeletion(MetaItem $item)
    {
        $item::deleted(function ($model)
        {
            if (! is_null($key = $this->findItem($model->name))) {
                $this->forget($key);
            }
        });
    }

    /**
     * Resolve calls to check whether an item with a specific key name exists.
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return ! is_null($this->findItem($name));
    }

    /**
     * Resolve calls to unset an item with a specific key name.
     *
     * @param $name
     */
    public function __unset($name)
    {
        if (! is_null($key = $this->findItem($name))) {
            $this->forget($key);
        }
    }

    /**
     * Resolve calls to get an item with a specific key name.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (! is_null($key = $this->findItem($name))) {
            $item = $this->get($key);

            return $item->value;
        }
    }

    /**
     * Resolve calls to set a new item to the collection or update an existing key.
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (! is_null($key = $this->findItem($name))) {
            $item = $this->get($key);

            $item->value = $value;

            return;
        }

        $this->addItem($name, $value);
    }
}
