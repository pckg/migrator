<?php

namespace Pckg\Migration\Constraint;

use Pckg\Migration\Constraint;

/**
 * Class Index
 *
 * @package Pckg\Migration\Constraint
 */
class Index extends Constraint
{

    /**
     * @var string
     */
    protected $type = 'INDEX';

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->type . '(`' . implode('`,`', $this->fields) . '`)';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'I_' . $this->getFields('_');
    }

}