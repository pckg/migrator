<?php

namespace Pckg\Migration\Helper;

use Pckg\Generic\Record\ListItem as ListItemRecord;
use Pckg\Generic\Record\ListRecord;

/**
 * Class ListItem
 *
 * @package Pckg\Migration\Helper
 */
trait Lists
{
    /**
     * @param $id
     *
     * @return ListRecord
     */
    protected function list($id)
    {
        return ListRecord::getOrCreate(['id' => $id]);
    }

    /**
     * @param $list
     * @param $slug
     *
     * @return ListItemRecord
     */
    protected function listItem($list, $slug)
    {
        $list = $this->list($list);

        return ListItemRecord::getOrCreate(['list_id' => $list->id, 'slug' => $slug]);
    }
}
