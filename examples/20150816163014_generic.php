<?php

use Pckg\Migration\Migration;

class Generic extends Migration
{

    public function up()
    {
        $userGroups = $this->table('user_groups');
        $this->addSlug($userGroups);
        $userGroups->save();

        $userGroupsI18n = $this->translaTable('user_groups');
        $this->addTitle($userGroupsI18n);
        $userGroupsI18n->save();

        $users = $this->table('users');
        $users->addColumn('user_group_id', 'integer');
        $users->addColumn('language_id', 'integer');
        $users->addColumn('email', 'string');
        $users->addIndex('email', ['unique' => true]);
        $users->addColumn('password', 'string');
        $users->save();

        $routes = $this->table('routes');
        $this->addDatetimes($routes);
        $this->addSlug($routes);
        $routes->save();

        $routesI18n = $this->translaTable('routes');
        $this->addTitle($routesI18n);
        $routesI18n->addColumn('route', 'string');
        $routes->addIndex('route', ['unique' => true]);
        $routesI18n->save();

        $layouts = $this->table('layouts');
        $this->addSlug($layouts);
        $layouts->save();

        $layoutsI18n = $this->translaTable('layouts');
        $this->addTitle($layoutsI18n);
        $layoutsI18n->addColumn('template', 'string');
        $layoutsI18n->save();

        $actions = $this->table('actions');
        $actions->save();

        $routesActions = $this->mtmTable('routes', 'actions', 'route_id', 'action_id');
        $routesActions->save();
    }

    public function down()
    {

    }

}
