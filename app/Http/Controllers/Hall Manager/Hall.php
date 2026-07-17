/** 


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cinema extends Model
{
    protected $fillable = [
        'name', 'slug', 'address', 'location', 'latitude', 'longitude',
        'phone', 'email', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Cinema $cinema) {
            if (empty($cinema->slug)) {
                $base = Str::slug($cinema->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $cinema->slug = $slug;
            }
        });
    }

    public function halls()
    {
        return $this->hasMany(Hall::class);
    }

    public function shows()
    {
        return $this->hasMany(Show::class);
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
*/