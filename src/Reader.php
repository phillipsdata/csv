<?php
namespace PhillipsData\Csv;

use SeekableIterator;
use SplFileObject;

/**
 * CSV Reader
 */
class Reader extends AbstractModifier implements SeekableIterator
{
    protected $file;

    /**
     * Set the input file
     *
     * @param \SplFileObject $file
     */
    public function input(SplFileObject $file)
    {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($position)
    {
        return $this->file->seek($position);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        #
        # TODO: Filter (call $this->next() until filter callback returns true)
        #

        $row = $this->file->fgetcsv();

        #
        # TODO: Format
        #

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->file->key();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        return $this->file->next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        return $this->file->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->file->valid();
    }
}
