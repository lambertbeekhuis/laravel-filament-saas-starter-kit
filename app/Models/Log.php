<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory;

    public const TYPE_INFO = 'info';

    public const TYPE_WARNING = 'warning';

    public const TYPE_ERROR = 'error';

    protected $fillable = [
        'tenant_id',
        'action',
        'type',
        'data_json',
        'model_type',
        'model_id',
    ];

    /** @var array<string, mixed>|null */
    protected ?array $data = null;

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            self::TYPE_INFO => 'Info',
            self::TYPE_WARNING => 'Warning',
            self::TYPE_ERROR => 'Error',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function setData(array $data): static
    {
        $this->data_json = json_encode($data);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        if (! is_array($this->data)) {
            $this->data = $this->data_json
                ? json_decode($this->data_json, true)
                : [];
        }

        return $this->data;
    }

    public function getDataJson(): ?string
    {
        return $this->data_json;
    }

    public static function createNewLog(string $action, string $type, array $data, ?int $tenant_id = null, ?Model $model = null): self
    {
        return self::create([
            'action' => $action,
            'type' => $type,
            'data_json' => json_encode($data),
            'tenant_id' => $tenant_id,
            'model_type' => $model ? $model::class : null,
            'model_id' => $model?->id,
        ]);
    }
}
