<?php

namespace App\Models;

use App\Enums\ConfigStatus;
use Database\Factories\BankTransferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    /** @use HasFactory<BankTransferFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'bank_name',
        'account_number',
        'amount',
        'notes',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => ConfigStatus::class,
        ];
    }

    public function isComplete(): bool
    {
        return filled($this->bank_name)
            && filled($this->account_number)
            && $this->amount !== null;
    }

    public function formattedAmount(): ?string
    {
        return $this->amount !== null
            ? 'Rp '.number_format((float) $this->amount, 0, ',', '.')
            : null;
    }
}
