<?php

namespace App\Traits;

trait UsesPivotConnection
{
    public function usingPivotConnection($connection)
    {
        $this->pivotConnection = $connection;
        return $this;
    }

    public function newPivotQuery()
    {
        $query = parent::newPivotQuery();

        if (isset($this->pivotConnection)) {
            $query->setConnection($this->pivotConnection);
        }

        return $query;
    }
}
