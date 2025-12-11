<?php

declare(strict_types=1);

namespace Curl;

/**
 * @psalm-suppress MissingTemplateParam
 */
class CaseInsensitiveArray implements \ArrayAccess, \Countable, \Iterator
{
    /**
     * @var mixed[] Data storage with lowercase keys.
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
     * @var string[] Case-sensitive keys.
     *
     * @see offsetSet()
     * @see offsetUnset()
     * @see key()
     */
    private $keys = [];

    /**
     * Construct
     *
     * Allow creating an empty array or converting an existing array to a
     * case-insensitive array. Caution: Data may be lost when converting
     * case-sensitive arrays to case-insensitive arrays.
     *
     * @param  mixed[]              $initial (optional) Existing array to convert.
     * @return CaseInsensitiveArray
     */
    public function __construct(?array $initial = null)
    {
        if ($initial !== null) {
            foreach ($initial as $key => $value) {
                $this->offsetSet($key, $value);
            }
        }
    }

    /**
     * Offset Set
     *
     * Set data at a specified offset. Converts the offset to lowercase, and
     * stores the case-sensitive offset and the data at the lowercase indexes in
     * $this->keys and @this->data.
     *
     * @param  string $offset The offset to store the data at (case-insensitive).
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
            $offsetlower = strtolower($offset);
            $this->data[$offsetlower] = $value;
            $this->keys[$offsetlower] = $offset;
        }
    }

    /**
     * Offset Exists
     *
     * Checks if the offset exists in data storage. The index is looked up with
     * the lowercase version of the provided offset.
     *
     * @param  string $offset Offset to check
     * @return bool   If the offset exists.
     * @see https://secure.php.net/manual/en/arrayaccess.offsetexists.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return array_key_exists(strtolower($offset), $this->data);
    }

    /**
     * Offset Unset
     *
     * Unsets the specified offset. Converts the provided offset to lowercase,
     * and unsets the case-sensitive key, as well as the stored data.
     *
     * @param  string $offset The offset to unset.
     * @return void
     * @see https://secure.php.net/manual/en/arrayaccess.offsetunset.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $offsetlower = strtolower($offset);
        unset($this->data[$offsetlower]);
        unset($this->keys[$offsetlower]);
    }

    /**
     * Offset Get
     *
     * Return the stored data at the provided offset. The offset is converted to
     * lowercase and the lookup is done on the data store directly.
     *
     * @param  string $offset Offset to lookup.
     * @return mixed  The data stored at the offset.
     * @see https://secure.php.net/manual/en/arrayaccess.offsetget.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        $offsetlower = strtolower($offset);
        return $this->data[$offsetlower] ?? null;
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
     * @return mixed Case-sensitive key at current position.
     * @see https://secure.php.net/manual/en/iterator.key.php
     */
    #[\Override]
    #[\ReturnTypeWillChange]
    public function key()
    {
        $key = key($this->data);
        if ($key === null) {
            return null;
        } else {
            return $this->keys[$key] ?? $key;
        }
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
