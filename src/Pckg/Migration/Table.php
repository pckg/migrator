<?php

namespace Pckg\Migration;

use Pckg\Migration\Constraint\Index;
use Pckg\Migration\Constraint\Primary;
use Pckg\Migration\Constraint\Unique;
use Pckg\Migration\Constraint\Constraint;
use Pckg\Migration\Field\Boolean;
use Pckg\Migration\Field\Datetime;
use Pckg\Migration\Field\Decimal;
use Pckg\Migration\Field\Generated;
use Pckg\Migration\Field\Group\Deletable;
use Pckg\Migration\Field\Group\Hidable;
use Pckg\Migration\Field\Group\Orderable;
use Pckg\Migration\Field\Group\Timeable;
use Pckg\Migration\Field\Id;
use Pckg\Migration\Field\IdString;
use Pckg\Migration\Field\Integer;
use Pckg\Migration\Field\Json;
use Pckg\Migration\Field\LongText;
use Pckg\Migration\Field\Point;
use Pckg\Migration\Field\Text;
use Pckg\Migration\Field\Varchar;

/**
 * Class Table
 *
 * @package Pckg\Migration
 */
class Table
{

    /**
     * @var
     */
    protected $name;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $constraints = [];

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * Table constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    // getters

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function getField($name)
    {
        return new Field($this, $name);
    }

    /**
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    // adders

    /**
     * @param Relation $relation
     *
     * @return $this
     */
    public function addRelation(Relation $relation)
    {
        $this->relations[] = $relation;

        return $this;
    }

    /**
     * @param Constraint $constraint
     *
     * @return $this
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    /**
     * @param Field $field
     *
     * @return $this
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;

        return $this;
    }

    // fields

    /**
     * @param string $name
     * @param bool   $primary
     *
     * @return Id
     */
    public function id($name = 'id', $primary = true)
    {
        $id = new Id($this, $name);

        if ($primary) {
            $id->primary();
        } else {
            $id->autoincrement(false);
        }

        $this->fields[] = $id;

        return $id;
    }

    /**
     * @param string $name
     * @param bool   $primary
     *
     * @return IdString
     */
    public function idString($name = 'id', $primary = true)
    {
        $id = new IdString($this, $name);

        if ($primary) {
            $id->primary();
        }

        $this->fields[] = $id;

        return $id;
    }

    /**
     * @param     $name
     * @param int $length
     *
     * @return Varchar
     */
    public function varchar($name, $length = 255)
    {
        $varchar = new Varchar($this, $name);

        $this->fields[] = $varchar;

        $varchar->length($length);

        return $varchar;
    }

    /**
     * @param     $name
     *
     * @return Json
     */
    public function json($name)
    {
        $json = new Json($this, $name);

        $this->fields[] = $json;

        return $json;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $as
     */
    public function generated(string $name, string $type, string $as)
    {
        $generated = new Generated($this, $name, $type, $as);

        $this->fields[] = $generated;

        return $generated;
    }

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $unique
     *
     * @return Varchar
     */
    public function slug($name = 'slug', $length = 128, $unique = true)
    {
        $field = $this->varchar($name, $length);

        if ($unique) {
            $this->unique($name);
        }

        return $field;
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return Varchar
     */
    public function title($name = 'title', $length = 128)
    {
        return $this->varchar($name, $length);
    }

    /**
     * @param        $name
     * @param string $type
     *
     * @return $this
     */
    public function range($name, $type = 'datetime')
    {
        $this->{$type}($name . '_from')->nullable();
        $this->{$type}($name . '_to')->nullable();

        return $this;
    }

    /**
     * @param $name
     *
     * @return Point
     */
    public function point($name)
    {
        $point = new Point($this, $name);

        $this->fields[] = $point;

        return $point;
    }

    /**
     * @param string $name
     *
     * @return Text
     */
    public function subtitle($name = 'subtitle')
    {
        return $this->text($name);
    }

    /**
     * @param string $name
     *
     * @return Text
     */
    public function lead($name = 'lead')
    {
        return $this->text($name);
    }

    /**
     * @param string $name
     *
     * @return Text
     */
    public function content($name = 'content')
    {
        return $this->text($name);
    }

    /**
     * @param      $name
     * @param null $default
     *
     * @return Boolean
     */
    public function boolean($name, $default = null)
    {
        $boolean = new Boolean($this, $name);

        $this->fields[] = $boolean;

        $boolean->setDefault($default);

        return $boolean;
    }

    /**
     * @param $name
     *
     * @return Text
     */
    public function text($name)
    {
        $text = new Text($this, $name);

        $this->fields[] = $text;

        return $text;
    }

    /**
     * @param $name
     *
     * @return Text|Varchar
     */
    public function textByLength($name, $length = 255)
    {
        if ($length <= 255) {
            return $this->tinytext($name, $length);
        }

        if ($length <= 2048) {
            return $this->varchar($name, $length);
        }

        return $this->text($name);
    }

    /**
     * @param $name
     *
     * @return LongText
     */
    public function longtext($name)
    {
        $text = new LongText($this, $name);

        $this->fields[] = $text;

        return $text;
    }

    /**
     * @param string $name
     *
     * @return Text
     */
    public function description($name = 'description')
    {
        return $this->text($name);
    }

    /**
     * @param string $name
     *
     * @return Varchar
     */
    public function email($name = 'email')
    {
        return $this->varchar($name);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return Varchar
     */
    public function password($name = 'password', $length = 255)
    {
        return $this->varchar($name, $length);
    }

    /**
     * @param     $name
     * @param int $length
     *
     * @return Integer
     */
    public function integer($name, $length = 11)
    {
        $integer = new Integer($this, $name);

        $this->fields[] = $integer;

        $integer->length($length);

        return $integer;
    }

    /**
     * @param       $name
     * @param array $length
     *
     * @return Decimal
     */
    public function decimal($name, $length = [8, 2])
    {
        $decimal = new Decimal($this, $name);

        $this->fields[] = $decimal;

        $decimal->length($length);

        return $decimal;
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function parent($name = 'parent_id')
    {
        $parent = $this->integer($name);

        $parent->references($this->name)->nullable()->index();

        return $parent;
    }

    /**
     * @param $name
     *
     * @return Datetime
     */
    public function datetime($name)
    {
        $datetime = new Datetime($this, $name);

        $this->fields[] = $datetime;

        return $datetime;
    }

    /**
     * @param $name
     *
     * @return Datetime
     */
    public function date($name)
    {
        $datetime = new Datetime($this, $name);

        $this->fields[] = $datetime;

        return $datetime;
    }

    /**
     * @param $name
     *
     * @return Datetime
     */
    public function time($name)
    {
        $datetime = new Datetime($this, $name);

        $this->fields[] = $datetime;

        return $datetime;
    }

    /**
     * @return Varchar
     */
    public function language()
    {
        $language = $this->varchar('language_id', 2)->references('languages', 'slug')->index();

        return $language;
    }

    // groups

    /**
     * @return Timeable
     */
    public function timeable()
    {
        $timeable = new Timeable($this);

        return $timeable;
    }

    /**
     * @return Orderable
     */
    public function orderable()
    {
        $orderable = new Orderable($this);

        return $orderable;
    }

    /**
     * @return Hidable
     */
    public function hideable()
    {
        $hidable = new Hidable($this);

        return $hidable;
    }

    /**
     * @return Deletable
     */
    public function deletable()
    {
        $deletable = new Deletable($this);

        return $deletable;
    }

    // index, primary, unique

    /**
     * @param array ...$fields
     *
     * @return Primary
     */
    public function primary(...$fields)
    {
        $primary = new Primary($this, ...$fields);

        $this->constraints[] = $primary;

        return $primary;
    }

    /**
     * @param array ...$fields
     *
     * @return Index
     */
    public function index(...$fields)
    {
        $index = new Index($this, ...$fields);

        $this->constraints[] = $index;

        return $index;
    }

    /**
     * @param mixed ...$fields
     *
     * @return Unique
     */
    public function unique(...$fields)
    {
        $unique = new Unique($this, ...$fields);

        $this->constraints[] = $unique;

        return $unique;
    }
}