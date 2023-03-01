<?php
namespace VinicciusGuedes\LaravelCnab\Api\Exception;

use Exception;

class MissingDataException extends Exception
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
        parent::__construct('Os seguinte campos são obrigatórios: ' . implode(', ', $this->data));
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return MissingDataException
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}