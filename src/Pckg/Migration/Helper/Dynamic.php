<?php namespace Pckg\Migration\Helper;

use Pckg\Dynamic\Record\Field;
use Pckg\Dynamic\Record\FieldType;
use Pckg\Dynamic\Record\Relation;
use Pckg\Dynamic\Record\RelationType;
use Pckg\Dynamic\Record\Table;

trait Dynamic
{

    protected function dynamicTable($table, $repository = null)
    {
        return Table::getOrCreate(['table' => $table, 'repository' => $repository]);
    }

    protected function dynamicFieldType($slug)
    {
        return FieldType::getOrFail(['slug' => $slug]);
    }

    protected function dynamicField(Table $table, $field, $fieldType)
    {
        $fieldType = $this->dynamicFieldType($fieldType);

        return Field::getOrCreate(['table_id' => $table->id, 'field' => $field, 'field_type_id' => $fieldType->id]);
    }

    protected function dynamicRelationType($slug)
    {
        return RelationType::getOrFail(['slug' => $slug]);
    }

    protected function dynamicRelation(
        Table $onTable,
        Field $onField,
        $relationType,
        Table $showTable,
        Field $showField
    )
    {
        $relationType = $this->dynamicRelationType($relationType);

        return Relation::getOrCreate(
            [
                'on_table_id'      => $onTable->id,
                'on_field_id'      => $onField->id,
                'show_table_id'    => $showTable->id,
                'show_field_id'    => $showField->id,
                'relation_type_id' => $relationType->id,
            ]
        );
    }

}
