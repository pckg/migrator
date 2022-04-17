<?php

namespace Pckg\Migration\Helper;

use Pckg\Dynamic\Record\Field;
use Pckg\Dynamic\Record\FieldType;
use Pckg\Dynamic\Record\Relation;
use Pckg\Dynamic\Record\RelationType;
use Pckg\Dynamic\Record\Table;

/**
 * Trait Dynamic
 *
 * @package Pckg\Migration\Helper
 */
trait Dynamic
{
    /**
     * @param      $table
     * @param null $repository
     *
     * @return mixed
     */
    protected function dynamicTable($table, $repository = null)
    {
        return Table::getOrCreate(['table' => $table, 'repository' => $repository]);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    protected function dynamicFieldType($slug)
    {
        return FieldType::getOrFail(['slug' => $slug]);
    }

    /**
     * @param Table $table
     * @param       $field
     * @param       $fieldType
     *
     * @return mixed
     */
    protected function dynamicField(Table $table, $field, $fieldType)
    {
        $fieldType = $this->dynamicFieldType($fieldType);

        return Field::getOrCreate(['table_id' => $table->id, 'field' => $field, 'field_type_id' => $fieldType->id]);
    }

    /**
     * @param $slug
     *
     * @return mixed
     */
    protected function dynamicRelationType($slug)
    {
        return RelationType::getOrFail(['slug' => $slug]);
    }

    /**
     * @param Table $onTable
     * @param Field $onField
     * @param       $relationType
     * @param Table $showTable
     * @param Field $showField
     *
     * @return mixed
     */
    protected function dynamicRelation(
        Table $onTable,
        Field $onField,
        $relationType,
        Table $showTable,
        Field $showField
    ) {
        $relationType = $this->dynamicRelationType($relationType);

        return Relation::getOrCreate([
                                         'on_table_id'      => $onTable->id,
                                         'on_field_id'      => $onField->id,
                                         'show_table_id'    => $showTable->id,
                                         'show_field_id'    => $showField->id,
                                         'relation_type_id' => $relationType->id,
                                     ]);
    }
}
