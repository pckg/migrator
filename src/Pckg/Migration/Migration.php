<?php

namespace Pckg\Migration;

class Migration
{

    public function table($table)
    {
        return new Table($table);
    }

}