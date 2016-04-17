<?php

namespace Pckg\Migration;

class Migration
{

    public function table($table)
    {
        return new Table($table);
    }

    public function translatable($table, $suffix = '_i18n')
    {
        $translatable = new Table($table . $suffix);

        $translatable->id('id', false);
        $translatable->varchar('lang_id', 2)->references('languages', 'slug');

        $translatable->primary('id', 'lang_id');

        return $translatable;
    }

}