<?php namespace Pckg\Migration;

use Pckg\Migration\Command\ExecuteMigration;
use Pckg\Migration\Field\Boolean;
use Pckg\Migration\Field\Datetime;
use Pckg\Migration\Field\Group\Timeable;
use Pckg\Migration\Field\Id;
use Pckg\Migration\Field\Integer;
use Pckg\Migration\Key\Index;
use Pckg\Migration\Key\Primary;
use Pckg\Migration\Key\Unique;

class Table
{

    protected $name;

    protected $fields = [];

    protected $constraints = [];

    protected $relations = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    // adders

    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;

        return $this;
    }

    public function addConstraint(Key $constraint)
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;

        return $this;
    }

    // fields

    public function id($name = 'id', $primary = true)
    {
        $id = new Id($this, $name);

        if ($primary) {
            $id->primary();
        }

        $this->fields[] = $id;

        return $id;
    }

    public function varchar($name, $length = 128)
    {
        $varchar = new Field($this, $name);

        $this->fields[] = $varchar;

        $varchar->length($length);

        return $varchar;
    }

    public function boolean($name, $default = null)
    {
        $boolean = new Boolean($this, $name);

        $this->fields[] = $boolean;

        $boolean->setDefault($default);

        return $boolean;
    }

    public function integer($name, $length = 11)
    {
        $integer = new Integer($this, $name);

        $this->fields[] = $integer;

        $integer->length($length);

        return $integer;
    }

    public function datetime($name)
    {
        $datetime = new Datetime($this, $name);

        $this->fields[] = $datetime;

        return $datetime;
    }

    // groups

    public function timeable()
    {
        $timeable = new Timeable($this);

        return $timeable;
    }

    // index, primary, unique

    public function index(...$fields)
    {
        $index = new Index($this, ...$fields);

        $this->constraints[] = $index;

        return $index;
    }

    public function primary(...$fields)
    {
        $primary = new Primary($this, $fields);

        $this->constraints[] = $primary;

        return $primary;
    }

    public function unique(...$fields)
    {
        $unique = new Unique($this, $fields);

        $this->constraints[] = $unique;

        return $unique;
    }

}