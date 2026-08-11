<?php

namespace App\Models;

use App\Enums\ConfigStatus;
use Database\Factories\SocialMediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    /** @use HasFactory<SocialMediaFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'platform',
        'username',
        'profile_url',
        'caption',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ConfigStatus::class,
        ];
    }

    public function isComplete(): bool
    {
        return filled($this->platform)
            && filled($this->username)
            && filled($this->profile_url);
    }
}
