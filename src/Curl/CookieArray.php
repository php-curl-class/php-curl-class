<?php

declare(strict_types=1);

namespace Curl;

/**
 * @psalm-suppress MissingTemplateParam
 */
class CookieArray implements \ArrayAccess, \Countable, \Iterator
{
    /**
     * @var mixed[] Data storage with url-decoded keys.
     *
     * @see offsetSet()
     * @see offsetExists()
     * @see offsetUnset()
     * @see offsetGet()
     * @see count()
     * @see current()
     * @see next()
     * @see key()
     */
    private $data = [];

    /**
     * Offset Set
     *
     * Set data at a specified offset. Decodes the offset using rawurldecode
     * and stores the data at the decoded index.
     *
     * @param  string $offset The offset to store the data at (url-decoded).
     * @param  mixed  $value  The data to store at the specified offset.
     * @return void
     * @see https://secure.php.net/manual/en/arrayaccess.offsetset.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[rawurldecode($offset)] = $value;
        }
    }

    /**
     * Offset Exists
     *
     * Checks if the offset exists in data storage. The index is looked up with
     * the url-decoded version of the provided offset.
     *
     * @param  string $offset Offset to check
     * @return bool   If the offset exists.
     * @see https://secure.php.net/manual/en/arrayaccess.offsetexists.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return array_key_exists(rawurldecode($offset), $this->data);
    }

    /**
     * Offset Unset
     *
     * Unsets the specified offset. Decodes the provided offset using
     * rawurldecode and unsets the stored data.
     *
     * @param  string $offset The offset to unset.
     * @return void
     * @see https://secure.php.net/manual/en/arrayaccess.offsetunset.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->data[rawurldecode($offset)]);
    }

    /**
     * Offset Get
     *
     * Return the stored data at the provided offset. The offset is decoded
     * using rawurldecode and the lookup is done on the data store directly.
     *
     * @param  string $offset Offset to lookup.
     * @return mixed  The data stored at the offset.
     * @see https://secure.php.net/manual/en/arrayaccess.offsetget.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[rawurldecode($offset)] ?? null;
    }

    /**
     * Count
     *
     * @return int The number of elements stored in the array.
     * @see https://secure.php.net/manual/en/countable.count.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->data);
    }

    /**
     * Current
     *
     * @return mixed Data at the current position.
     * @see https://secure.php.net/manual/en/iterator.current.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->data);
    }

    /**
     * Next
     *
     * @return void
     * @see https://secure.php.net/manual/en/iterator.next.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function next()
    {
        next($this->data);
    }

    /**
     * Key
     *
     * @return mixed Decoded key at current position.
     * @see https://secure.php.net/manual/en/iterator.key.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->data);
    }

    /**
     * Valid
     *
     * @return bool If the current position is valid.
     * @see https://secure.php.net/manual/en/iterator.valid.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return (key($this->data) !== null);
    }

    /**
     * Rewind
     *
     * @return void
     * @see https://secure.php.net/manual/en/iterator.rewind.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->data);
    }
}
