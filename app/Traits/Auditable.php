<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAction('created');
        });

        static::updated(function ($model) {
            $model->logAction('updated');
        });

        static::deleted(function ($model) {
            $model->logAction('deleted');
        });
    }

    protected function logAction(string $action)
    {
        $oldValues = null;
        $newValues = $this->getAttributes();

        if ($action === 'updated') {
            $newValues = $this->getChanges();
            $oldValues = array_intersect_key($this->getOriginal(), $newValues);
        }

        if ($action === 'deleted') {
            $oldValues = $this->getOriginal();
            $newValues = null;
        }

        // Mask sensitive fields (fraud-prevention)
        $sensitiveFields = ['password', 'remember_token', 'token', 'secret', 'key', 'api_key'];
        foreach ($sensitiveFields as $field) {
            if (isset($newValues[$field])) $newValues[$field] = '[MASKED]';
            if (isset($oldValues[$field])) $oldValues[$field] = '[MASKED]';
        }

        // Build a human-readable description for the audit log
        $modelName = class_basename($this);
        $description = match ($action) {
            'created' => "Membuat {$modelName} baru (ID: {$this->id})",
            'updated' => "Mengubah {$modelName} ID {$this->id}: " . implode(', ', array_keys($newValues ?? [])),
            'deleted' => "Menghapus {$modelName} ID {$this->id}",
            default   => "{$action} on {$modelName}",
        };

        AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'model_type'  => get_class($this),
            'model_id'    => $this->id,
            'description' => $description,
            'old_values'  => $oldValues ? json_encode($oldValues) : null,
            'new_values'  => $newValues ? json_encode($newValues) : null,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'url'         => Request::fullUrl(),
        ]);
    }
}
