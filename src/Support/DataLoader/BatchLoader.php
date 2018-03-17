<?php

namespace Nuwave\Lighthouse\Support\DataLoader;

use GraphQL\Deferred;

abstract class BatchLoader
{
    /**
     * Keys to resolve.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $keys;

    /**
     * Arguments passed in to loader.
     *
     * @var array
     */
    protected $args = [];

    /**
     * Check if data has been loaded.
     *
     * @var bool
     */
    protected $hasLoaded = false;

    /**
     * Create new instance of data loader.
     */
    public function __construct()
    {
        $this->keys = collect();
    }

    /**
     * Load object by key.
     *
     * @param mixed  $key
     * @param string $relation
     * @param mixed  $root
     * @param array  $args
     *
     * @return Deferred
     */
    public function load($key, $relation, $root = null, array $args = [])
    {
        $this->keys->put($key, [
            'root' => $root,
            'args' => $args,
            'relation' => $relation,
        ]);

        return new Deferred(function () use ($key, $root, $args) {
            if (! $this->hasLoaded) {
                $this->resolve();
                $this->hasLoaded = true;
            }

            return array_get($this->keys->toArray(), "$key.value");
        });
    }

    /**
     * Set key value.
     *
     * @param mixed $key
     * @param mixed $value
     */
    protected function set($key, $value)
    {
        if ($field = $this->keys->get($key)) {
            $this->keys->put($key, array_merge($field, ['value' => $value]));
        }
    }

    /**
     * Get stored keys.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getKeys()
    {
        return $this->keys->keys();
    }

    /**
     * Resolve keys.
     */
    abstract public function resolve();
}
