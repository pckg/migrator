<?php

namespace Pckg\Migration;

use Pckg\Migration\Constraint\Index;
use Pckg\Migration\Constraint\Primary;
use Pckg\Migration\Constraint\Unique;
use Pckg\Migration\Field\Boolean;
use Pckg\Migration\Field\Datetime;
use Pckg\Migration\Field\Decimal;
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
 * Class View
 *
 * @package Pckg\Migration
 */
class View
{

    /**
     * @var
     */
    protected $name;
/**
     * @var string
     */
    protected $select = null;
/**
     * @var bool
     */
    protected $check = false;
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

    /**
     * @param $select
     *
     * @return $this
     */
    public function as($select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @param bool $check
     *
     * @return $this
     */
    public function withCheck($check = true)
    {
        $this->check = $check;
        return $this;
    }

    /**
     * @return string
     */
    public function buildSql()
    {
        return $this->select . ($this->check ? ' WITH CHECK OPTION' : null);
    }
}
