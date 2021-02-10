<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    const TYPE_DEBT = 1;
    const TYPE_RECEIVABLE = 0;

    protected $fillable = [
        'partner_id', 'type_id', 'amount', 'description', 'creator_id',
    ];

    public function getNameLinkAttribute()
    {
        return link_to_route('loans.show', $this->name, [$this], [
            'title' => __(
                'app.show_detail_title',
                ['name' => $this->name, 'type' => __('loan.loan')]
            ),
        ]);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}
