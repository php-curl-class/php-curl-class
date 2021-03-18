<?php

namespace Curl;

class CaseInsensitiveArray implements \ArrayAccess, \Countable, \Iterator
{

    /**
     * @var mixed[] Data storage with lowercase keys.
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
     * @param mixed[] $initial (optional) Existing array to convert.
     *
     * @return CaseInsensitiveArray
     *
     * @access public
     */
    public function __construct(array $initial = null)
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
     * @see https://secure.php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param string $offset The offset to store the data at (case-insensitive).
     * @param mixed $value The data to store at the specified offset.
     *
     * @return void
     *
     * @access public
     */
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
     * @see https://secure.php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param string $offset Offset to check
     *
     * @return bool If the offset exists.
     *
     * @access public
     */
    public function offsetExists($offset)
    {
        return (bool) array_key_exists(strtolower($offset), $this->data);
    }

    /**
     * Offset Unset
     *
     * Unsets the specified offset. Converts the provided offset to lowercase,
     * and unsets the case-sensitive key, as well as the stored data.
     *
     * @see https://secure.php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string $offset The offset to unset.
     *
     * @return void
     *
     * @access public
     */
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
     * @see https://secure.php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param string $offset Offset to lookup.
     *
     * @return mixed The data stored at the offset.
     *
     * @access public
     */
    public function offsetGet($offset)
    {
        $offsetlower = strtolower($offset);
        return isset($this->data[$offsetlower]) ? $this->data[$offsetlower] : null;
    }

    /**
     * Count
     *
     * @see https://secure.php.net/manual/en/countable.count.php
     *
     * @param void
     *
     * @return int The number of elements stored in the array.
     *
     * @access public
     */
    public function count()
    {
        return (int) count($this->data);
    }

    /**
     * Current
     *
     * @see https://secure.php.net/manual/en/iterator.current.php
     *
     * @param void
     *
     * @return mixed Data at the current position.
     *
     * @access public
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Next
     *
     * @see https://secure.php.net/manual/en/iterator.next.php
     *
     * @param void
     *
     * @return void
     *
     * @access public
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * Key
     *
     * @see https://secure.php.net/manual/en/iterator.key.php
     *
     * @param void
     *
     * @return mixed Case-sensitive key at current position.
     *
     * @access public
     */
    public function key()
    {
        $key = key($this->data);
        return isset($this->keys[$key]) ? $this->keys[$key] : $key;
    }

    /**
     * Valid
     *
     * @see https://secure.php.net/manual/en/iterator.valid.php
     *
     * @return bool If the current position is valid.
     *
     * @access public
     */
    public function valid()
    {
        return (bool) (key($this->data) !== null);
    }

    /**
     * Rewind
     *
     * @see https://secure.php.net/manual/en/iterator.rewind.php
     *
     * @param void
     *
     * @return void
     *
     * @access public
     */
    public function rewind()
    {
        reset($this->data);
    }
}
