<?php

namespace App\Containers\Geral\Models;

use App\Ship\Parents\Models\Model;
use App\Containers\Geral\Models\Log;
use Watson\Validating\ValidatingTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Support\Arrayable;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\AttributeRedactor;
use OwenIt\Auditing\Auditable as TraitAuditable;
use OwenIt\Auditing\Exceptions\AuditableTransitionException;
use OwenIt\Auditing\Exceptions\AuditingException;
use Exception;

class MainModel extends Model implements Auditable
{
    use ValidatingTrait, SoftDeletes, TraitAuditable;

    /**
      * The name of the "created at" column.
      *
      * @var string
      */
    const CREATED_AT = 'dt_criacao';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'dt_alteracao';

    const DELETED_AT = 'dt_remocao';

    public $cacheFor = 60;

    public $cacheDriver = 'memcached';

    protected $keyType = 'integer';

    protected $primaryKey = 'id';

    public $incrementing = true;

    public $isApiResponse = false;

    public $rules = [];

    private $originalAntigo;

    public $dates = [
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao',
    ];

    public $auditTable = null;

    public $auditConnection = null;

    protected static $labels_trans_map = [
        'id' => 'ID'
    ];

    public static $label_cached = [];

    public static function label($field)
    {
        if (array_key_exists($field, static::$label_cached)) {
            return static::$label_cached[$field];
        }
        $label_i18n_path = self::getLabelI18nPath('field.' . $field);
        $trans_result = trans($label_i18n_path);
        if ($trans_result != $label_i18n_path) {
            $label = $trans_result;
        } else {
            if (property_exists(static::class, 'labels') && is_array(static::$labels) && array_key_exists($field, static::$labels)) {
                $label = static::$labels[$field];
                $label = is_callable($label) ? call_user_func($label, $field) : strval($label);
            } else {
                $field_lower = strtolower($field);
                if (array_key_exists($field_lower, static::$labels_trans_map)) {
                    $label = static::$labels_trans_map[$field_lower];
                } else {
                    $label = static::getAutoConvertLabel($field);
                }
            }
        }
        static::$label_cached[$field] = $label;

        return $label;
    }

    /**
     * @param string $field
     * @return string
     */
    protected static function getAutoConvertLabel($field)
    {
        return title_case(str_replace('_', ' ', strtolower($field)));
    }

    public static function labels()
    {
        $fields = func_num_args() ? array_flatten(func_get_args()) : true;
        $use_local = property_exists(static::class, 'labels') && is_array(static::$labels) && !empty(static::$labels);
        $result = static::$label_cached;
        if (is_array($fields) && !count(array_diff($fields, array_keys($result)))) {
            return array_only($result, $fields);
        }
        /**
         * Merge from i18n
         */
        $label_i18n_path = self::getLabelI18nPath('field');
        $i18n_fields = trans($label_i18n_path);
        if (is_array($i18n_fields) && $label_i18n_path !== $i18n_fields) {
            foreach ($i18n_fields as $i18n_key => $i18n_label) {
                $result[$i18n_key] = $i18n_label;
            }
        }
        /**
         * Merge from local
         */
        if ($use_local) {
            foreach (static::$labels as $local_field => $local_label) {
                if (!array_key_exists($local_field, $result)) {
                    $result[$local_field] = is_callable($local_label) ? call_user_func($local_label, $local_field) : strval($local_label);
                }
            }
        }
        /**
         * Make label for missing fields
         */
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (array_key_exists($field, $result)) {
                    $result[$field] = static::getAutoConvertLabel($field);
                }
            }
        }
        static::$label_cached = $result;
        if (is_array($fields)) {
            return array_only($result, $fields);
        }

        return $result;
    }

    protected static function getLabelI18nPath($sub_path = null)
    {
        if (property_exists(static::class, 'label_path')) {
            $path = static::$label_path;
        }
        if (empty($path)) {
            $path = 'model_' . snake_case(class_basename(static::class));
        }
        if ($sub_path) {
            $path .= '.' . $sub_path;
        }

        return $path;
    }

    public static function modelLabel()
    {
        $class_basename = class_basename(static::class);
        $trans = 'model_' . strtolower($class_basename) . '.model';
        $label = trans($trans);
        if (is_string($label) && $trans !== $label) {
            return $label;
        }

        return title_case($class_basename);
    }



    public function salvaOriginal()
    {
        $this->originalAntigo = $this->getOriginal();
    }

    public function limpaOriginal()
    {
        $this->originalAntigo = null;
    }

    public function temOriginal($key)
    {
        if (!$this->originalAntigo ||
            !Arr::exists($this->originalAntigo, $key)
        ) {
            return false;
        }
        return true;
    }

    public function valorOriginal($key = null, $default = null)
    {
        return Arr::get($this->originalAntigo, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function audits(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        $related = Config::get('audit.implementation', \OwenIt\Auditing\Models\Audit::class);
        $name = 'auditable';
        if ($this->auditTable ||
            $this->auditConnection
        ) {
            $type = null;
            $id = null;
            $localKey = null;

            $instance = $this->newRelatedInstance($related);
            [$type, $id] = $this->getMorphs($name, $type, $id);

            if ($this->auditTable) {
                $instance->setTable($this->auditTable);
            }
            if ($this->auditConnection) {
                $instance->setConnection($this->auditConnection);
            }
            $table = $instance->getTable();
            $localKey = $localKey ?: $this->getKeyName();
            $keys = $this->getKeyName();

            return $this->newMorphMany($instance->newQuery(), $this, $table . '.' . $type, $table . '.' . $id, $localKey);
        } else {
            return $this->morphMany(
                $related,
                $name
            );
        }
    }

    public function toAudit(): array
    {
        if (!$this->readyForAuditing()) {
            throw new AuditingException('A valid audit event has not been set');
        }

        $attributeGetter = $this->resolveAttributeGetter($this->auditEvent);

        if (!method_exists($this, $attributeGetter)) {
            throw new AuditingException(sprintf(
                'Unable to handle "%s" event, %s() method missing',
                $this->auditEvent,
                $attributeGetter
            ));
        }

        $this->resolveAuditExclusions();

        list($old, $new) = $this->$attributeGetter();

        if ($this->getAttributeModifiers()) {
            foreach ($old as $attribute => $value) {
                $old[$attribute] = $this->modifyAttributeValue($attribute, $value);
            }

            foreach ($new as $attribute => $value) {
                $new[$attribute] = $this->modifyAttributeValue($attribute, $value);
            }
        }

        $morphPrefix = Config::get('audit.user.morph_prefix', 'user');

        $tags = implode(',', $this->generateTags());

        $user = $this->resolveUser();

        return $this->transformAudit([
            'old_values' => $old,
            'new_values' => $new,
            'event' => $this->auditEvent,
            'auditable_id' => $this->id,
            'auditable_type' => $this->getMorphClass(),
            $morphPrefix . '_id' => $user ? $user->id : null,
            $morphPrefix . '_type' => $user ? $user->getMorphClass() : null,
            'url' => $this->resolveUrl(),
            'ip_address' => $this->resolveIpAddress(),
            'user_agent' => $this->resolveUserAgent(),
            'tags' => empty($tags) ? null : $tags,
        ]);
    }

    public function transitionTo(\OwenIt\Auditing\Contracts\Audit $audit, bool $old = false): \OwenIt\Auditing\Contracts\Auditable
    {
        // The Audit must be for an Auditable model of this type
        if ($this->getMorphClass() !== $audit->auditable_type) {
            throw new AuditableTransitionException(sprintf(
                'Expected Auditable type %s, got %s instead',
                $this->getMorphClass(),
                $audit->auditable_type
            ));
        }

        // The Audit must be for this specific Auditable model
        if ($this->getKeyForSaveQuery('id') !== $audit->auditable_id) {
            throw new AuditableTransitionException(sprintf(
                'Expected Auditable id %s, got %s instead',
                $this->getKeyForSaveQuery('id'),
                $audit->auditable_id
            ));
        }

        // Redacted data should not be used when transitioning states
        foreach ($this->getAttributeModifiers() as $attribute => $modifier) {
            if (is_subclass_of($modifier, AttributeRedactor::class)) {
                throw new AuditableTransitionException('Cannot transition states when an AttributeRedactor is set');
            }
        }

        // The attribute compatibility between the Audit and the Auditable model must be met
        $modified = $audit->getModified();

        if ($incompatibilities = array_diff_key($modified, $this->getAttributes())) {
            throw new AuditableTransitionException(sprintf(
                'Incompatibility between [%s:%s] and [%s:%s]',
                $this->getMorphClass(),
                $this->getKeyForSaveQuery('id'),
                get_class($audit),
                $audit->getKeyForSaveQuery('id')
            ), array_keys($incompatibilities));
        }

        $key = $old ? 'old' : 'new';

        foreach ($modified as $attribute => $value) {
            if (array_key_exists($key, $value)) {
                $this->setAttribute($attribute, $value[$key]);
            }
        }

        return $this;
    }
}
