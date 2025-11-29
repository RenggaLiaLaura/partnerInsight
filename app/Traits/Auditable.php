<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->auditLog('created');
        });

        static::updated(function ($model) {
            $model->auditLog('updated');
        });

        static::deleted(function ($model) {
            $model->auditLog('deleted');
        });
    }

    protected function auditLog($action)
    {
        $oldValues = null;
        $newValues = $this->getAttributes();

        if ($action === 'updated') {
            $oldValues = $this->getOriginal();
            
            // Only log if there are actual changes
            if ($oldValues === $newValues) {
                return;
            }
        } elseif ($action === 'deleted') {
            $oldValues = $this->getOriginal();
            $newValues = null;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
}
