<?php

use Pckg\Migration\Migration;

class Core extends Migration
{

    public function up()
    {
        $languages = $this->table('languages');
        $this->addSlug($languages);
        $languages->addColumn('flag', 'string');
        $languages->save();

        $languagesI18n = $this->translaTable('languages');
        $this->addTitle($languagesI18n);
        $languagesI18n->save();

        $translations = $this->table('translations');
        $this->addSlug($translations);
        $translations->save();

        $translationsI18n = $this->translaTable('translations');
        $translationsI18n->addColumn('content', 'text');
        $translations->save();

        $settings = $this->table('settings');
        $this->addParent($settings);
        $this->addSlug($settings);
        $settings->save();

        $settingsI18n = $this->translaTable('settings');
        $this->addTitle($settingsI18n);
        $settingsI18n->addColumn('value', 'string');
        $settingsI18n->save();
    }

    public function down()
    {

    }

}
