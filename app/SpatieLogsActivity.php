<?php

namespace App;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

trait SpatieLogsActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = new LogOptions;
        $logOptions->logAll();
        $logOptions->logOnlyDirty();

        return $logOptions;
    }
}
