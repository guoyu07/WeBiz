<?php

namespace Apps\Models;

class WaiterModel extends CommonModel
{
    public function getNewWaiter($old_waiter_id = null)
    {
        $where = 'SELECT userid FROM ' . $this->table_name . ' WHERE status = 1';
        if ($old_waiter_id) {
            $where .= 'AND userid <> ' . $old_waiter_id;
            $this->set($old_waiter_id, array('status' => 0));
        }
        $waiter = $this->db->doSql('SELECT (' . $where . ') AS waiterid, count(*) AS count FROM ' . $this->getTableName('user') . " GROUP BY 'waiterid' ORDER BY count ASC LIMIT 1");
        return is_array($waiter) ? $waiter[0]['waiterid'] : $waiter;
    }
}