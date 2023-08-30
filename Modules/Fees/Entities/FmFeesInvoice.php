<?php

namespace Modules\Fees\Entities;

use App\SmStudent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Fees\Entities\FmFeesInvoiceChield;

class FmFeesInvoice extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Fees\Database\factories\FmFeesInvoiceFactory::new ();
    }

    public function studentInfo()
    {
        return $this->belongsTo(SmStudent::class, 'student_id', 'id');
    }

    public function invoiceDetails()
    {
        return $this->hasMany(FmFeesInvoiceChield::class, 'fees_invoice_id');
    }

    public function invoiceDetail()
    {
        return $this->belongsTo(FmFeesInvoiceChield::class, 'fees_invoice_id');
    }

    public function getTidAttribute()
    {
        return $this->invoiceDetails()->first('fees_type');
    }

    public function getTamountAttribute()
    {
        return $this->invoiceDetails()->sum('amount');
    }

    public function getTweaverAttribute()
    {
        return $this->invoiceDetails()->sum('weaver');
    }

    public function getTfineAttribute()
    {
        return $this->invoiceDetails()->sum('fine');
    }

    public function getTpaidamountAttribute()
    {
        return $this->invoiceDetails()->sum('paid_amount');
    }

    public function getTsubtotalAttribute()
    {
        return $this->invoiceDetails()->sum('sub_total');
    }
    // public function feeType()
    // {
    //     return $this->invoiceDetails()->feeType->name ?? '';
    // }
}
