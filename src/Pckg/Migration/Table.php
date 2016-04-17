<?php namespace Pckg\Migration;

use Pckg\Migration\Field\Boolean;
use Pckg\Migration\Field\Id;
use Pckg\Migration\Field\Integer;

class Table
{

    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function id($name = 'id')
    {
        $id = new Id($name);

        return $id;
    }

    public function varchar($name, $length = 128)
    {
        $varchar = new Field($name);

        $varchar->length($length);

        return $varchar;
    }

    public function boolean($name, $default = null)
    {
        $boolean = new Boolean($name);

        $boolean->setDefault($default);

        return $boolean;
    }

    public function integer($name, $length = 11)
    {
        $integer = new Integer($name);

        $integer->length($length);

        return $integer;
    }

}