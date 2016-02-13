<?php
namespace PhillipsData\Csv;

#use SeekableIterator;
use SplFileObject;

/**
 * CSV Reader
 */
class Reader extends AbstractModifier
{
    protected $file;

    protected function __construct(SplFileObject $file)
    {
        parent::__construct($file);
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        if ($this->acceptCallback) {
            return call_user_func($this->acceptCallback, $this->current(), $this->key(), $this->file);
        }

        return true;
    }

    /**
     * Set the input file
     *
     * @param \SplFileObject $file
     * @param bool $first_row_headers True if the CSV contains headers in the first row, false otherwise
     */
    public function input(SplFileObject $file, $first_row_headers = false)
    {
        return new Reader($file);
        #$this->file = $file;
    }

    /**
     * {@inheritdoc}
     *
    public function seek($position)
    {
        return $this->file->seek($position);
    }
     *
     */

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        #
        # TODO: Filter (call $this->next() until filter callback returns true)
        #
        /*
        $valid = false;
        if ($this->acceptCallback) {
            while ($this->valid()) {
                // Filter for a matching row
                $row = $this->file->fgetcsv();
                $match = call_user_func($this->acceptCallback, $row, $this->key(), $this->file);

                if ($match) {
                    $valid = true;
                    break;
                } else {
                    $this->next();
                }
            }
        } else {
            $row = $this->file->fgetcsv();
            $valid = true;
        }

        // No row matches, return empty array
        if (!$valid) {
            return [];
        }
         *
         */


        /*
        if ($this->acceptCallback) {
            echo "TRUE";
            while ($this->valid()) {
                #print_r($this->file->current());
                $row = $this->file->fgetcsv();
                if (call_user_func($this->acceptCallback, $row, $this->key(), $this->file)) {
                    break;
                } else {
                    $row = [];
                }
                //$this->next();
                #print_r($row);
                #$row = $this->file->fgetcsv();
                #print_r($row);
                #echo '123123';
            }
        } else {
            $row = $this->file->fgetcsv();
        }
         *
         */

        /*
        echo "KEY:" . $this->key();
        echo "DATA: ";

        $row = $this->file->fgetcsv();
        print_r($row);
        */

        /*
        echo "\n\nstart";
        $this->file->seek(0);
        print_r($this->file->fgetcsv());
        $this->file->seek(0);
        print_r($this->file->fgetcsv());
        $this->file->seek(1);
        print_r($this->file->fgetcsv());
        $this->file->seek(0);
        print_r($this->file->fgetcsv());
        echo "\n\nend";


        echo "\n--BEGIN--";
        for ($i=0; $i<4; $i++) {
            echo "\nSEEK TO " . $i . "\n";
            $this->seek($i);
            echo "KEY: " . $this->key() . "\n";
            echo "DATA:";
            print_r($this->file->fgetcsv());
            #$this->next();
        }
        #echo "SEEK TO: 0";
        #$this->seek(0);
        #echo "KEY: " . $this->key();
        #cho "DATA: ";
        #print_r($this->file->fgetcsv());
        echo "\n--END--";
         *
         */

        #
        # TODO: Format
        #
        #if ($this->formatCallback) {
        #    $row = call_user_func($this->formatCallback, $row, $this->key(), $this->file);
        #}


        $row = $this->file->fgetcsv();
        if ($this->formatCallback) {
            $row = call_user_func($this->formatCallback, $row, $this->key(), $this->file);
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     *
    public function key()
    {
        #echo "KEY: " . $this->file->key();
        return $this->file->key();
    }
     *
     */

    /**
     * {@inheritdoc}
     *
    public function next()
    {
        return $this->file->next();
    }
     *
     */

    /**
     * {@inheritdoc}
     *
    public function rewind()
    {
        return $this->file->rewind();
    }
     *
     */

    /**
     * {@inheritdoc}
     *
    public function valid()
    {
        return $this->file->valid();
     *
     */
        /*

        $valid = $this->file->valid();
        echo "\n\nchecking key: " . $this->key();

        if ($this->acceptCallback) {
            while ($this->file->valid()) {
                $key = $this->key();
                $row = $this->current();
                if (call_user_func($this->acceptCallback, $row, $key, $this->file)) {
                    //$this->next();
                    echo "THE KEY IS: " . $key;
                    $this->seek($key);
                    break;
                }
            }
        }

        echo "valid: " . $this->key();

        return $valid;
         *
         */
    #}
}
