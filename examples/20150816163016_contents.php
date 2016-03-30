<?php

use Pckg\Migration\Migration;

class Contents extends Migration
{

    protected function upPlugins()
    {
        $plugins = $this->table('plugins');
        $this->addSlug($plugins);
        $plugins->save();

        $pluginsI18n = $this->translaTable('plugins');
        $this->addTitle($pluginsI18n);
        $pluginsI18n->addColumn('description', 'text');
        $pluginsI18n->save();

        $pluginsSettings = $this->mtmTable('plugins', 'settings', 'plugin_id', 'setting_id');
        $pluginsSettings->addColumn('value', 'string');
        $pluginsSettings->save();
    }

    protected function upActions()
    {
        $actionsRoutes = $this->mtmTable('actions', 'routes', 'action_id', 'route_id');
        $actionsRoutes->save();

        $actionsContents = $this->mtmTable('actions', 'contents', 'action_id', 'content_id');
        $actionsContents->save();
    }

    protected function upContents()
    {
        $contents = $this->table('contents');
        $this->addParent($contents);
        $this->addDatetimes($contents);
        $contents->save();

        $contentsSettings = $this->mtmTable('contents', 'settings', 'content_id', 'setting_id');
        $contentsSettings->addColumn('value', 'string');
        $contentsSettings->save();

        $contentsI18n = $this->translaTable('contents');
        $this->addTitle($contentsI18n);
        $contentsI18n->addColumn('subtitle', 'text');
        $contentsI18n->addColumn('description', 'text');
        $contentsI18n->addColumn('content', 'text');
        $contentsI18n->save();

        $contentTypes = $this->table('content_types');
        $this->addSlug($contentTypes);
        $contentTypes->save();

        $contentTypesI18n = $this->translaTable('content_types');
        $this->addSlug($contentTypesI18n);
        $contentTypesI18n->save();
    }

    public function up()
    {
        $this->upPlugins();
        $this->upContents();
        $this->upActions();
    }

    public function down()
    {

    }

}
