<?php

namespace Energycalculator\Repository\Collection;

class ResolverCollection implements CollectionInterface
{
    /**
     * @var \Closure
     */
    private $resolver;

    /**
     * @var array
     */
    private $elements;

    /**
     * ResolverCollection constructor.
     * @param \Closure $resolver
     */
    public function __construct(\Closure $resolver)
    {
        $this->resolver = $resolver;
    }

    private function resolveElements()
    {
        if (null === $this->resolver) {
            return;
        }

        $resolver = $this->resolver;
        $this->resolver = null;
        $this->elements = $resolver();
    }

    /**
     * @return mixed
     */
    public function current()
    {
        $this->resolveElements();

        return current($this->elements);
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $this->resolveElements();

        return next($this->elements);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        $this->resolveElements();

        return key($this->elements);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $this->resolveElements();

        return (bool) current($this->elements);
    }

    public function rewind()
    {
        $this->resolveElements();

        reset($this->elements);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->resolveElements();

        return array_key_exists($offset, $this->elements);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $this->resolveElements();

        return $this->elements[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->resolveElements();

        $this->elements[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }
}
