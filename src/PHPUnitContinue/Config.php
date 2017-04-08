<?php


namespace Wubs\PHPUnitContinue;


use ArrayAccess;

/**
 * Class Schema
 *
 * @package Wubs\PHPUnitContinue
 */
class Config implements \JsonSerializable, ArrayAccess
{

    /**
     * @var array
     */
    protected $schema = ["continue" => false, "class" => '', 'method' => '', 'has_error' => false];

    /**
     * @var
     */
    private $file;

    /**
     * @param $file
     */
    public function read(string $file)
    {
        $this->file = $file;
        $this->schema = json_decode(file_get_contents($file), true);
    }

    /**
     * @param $file
     */
    public function write($file = null)
    {
        if ($this->file || $file)
        {
            file_put_contents($file ?? $this->file, $this);
        }

    }


    /**
     * @return mixed
     */
    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->schema;
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->schema);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->schema[$offset];
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->schema[$offset] = $value;
        $this->write();
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->schema[$offset]);
    }
}