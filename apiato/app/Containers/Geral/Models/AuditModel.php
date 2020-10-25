<?php

namespace App\Containers\Geral\Models;

use App\Ship\Parents\Models\Model;
use OwenIt\Auditing\Contracts\Audit;
use OwenIt\Auditing\Audit as TraitAudit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

/**
 * @property integer $id
 * @property string $user_type
 * @property integer $user_id
 * @property string $event
 * @property string $auditable_type
 * @property integer $auditable_id
 * @property string $old_values
 * @property string $new_values
 * @property string $url
 * @property string $ip_address
 * @property string $user_agent
 * @property string $tags
 * @property string $created_at
 * @property string $updated_at
 */
class AuditModel extends Model implements Audit
{
    use TraitAudit;

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        // Note: Please do not add 'auditable_id' in here, as it will break non-integer PK models
    ];

    protected $attributes = [
    ];

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    public $incrementing = true;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->old_values) && empty($model->new_values)) {
                return false;
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->morphTo()->withTrashed();
    }

    public function getConnectionName()
    {
        if ($this->connection) {
            return $this->connection;
        }
        return Config::get('audit.drivers.database.connection', parent::getConnectionName());
    }

    /**
     * {@inheritdoc}
     */
    public function getTable(): string
    {
        if ($this->table) {
            return $this->table;
        }
        return Config::get('audit.drivers.database.table', parent::getTable());
    }



    public function getEventoAttribute()
    {
        if ($this->event == 'updated') {
            return 'Alteração';
        } elseif ($this->event == 'created') {
            return 'Criação';
        } elseif ($this->event == 'deleted') {
            return 'Remoção';
        }
        return 'Indefinido';
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
