<?php
namespace PhillipsData\Csv;

/**
 * Modifier allows modifying the result set
 */
abstract class AbstractModifier
{
    protected $acceptCallback;
    protected $formatCallback;

    /**
     * Set the accept callback. Executed for each iteration.
     *
     * @param callable $callback
     */
    public function accept(callable $callback)
    {
        $this->acceptCallback = $callback;
    }

    /**
     * Set the format callback. Executed for each iteration.
     *
     * @param callable $callback
     */
    public function format(callable $callback)
    {
        $this->formatCallback = $callback;
    }
}
