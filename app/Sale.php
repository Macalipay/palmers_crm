<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'company_id',
        'customer_type',
        'source_id',
        'store_id',
        'po_no',
        'date_purchased',
        'amount',
        'user_id',
        'merchandiser_id',
        'sales_associate_id',
        'division_id',
        'branch_id',
        'date_posted',
        'agreed_delivery_date',
        'actual_delivery_date',
        'payment_term',
        'assist_by',
        'active',
        'created_by',
        'updated_by',
        'rfq_no',
        'project_title',
        'contact_person',
        'telephone_no',
        'email',
        'date_encode',
        'date_received',
        'date_filed',
        'fdas',
        'afss',
        'akfss',
        'fss',
        'supply',
        'pm',
        'cctv',
        'other',
        'other_details',
        'floor_plan',
        'site_inspection',
        'project_location',
        'tpc',
        'remarks',
        'deadline',
        'date_request',
        'date_submitted',
        'fsd_proposal_no',
        'de_supervisor',
        'de_engineer',
        'de_document_custodian',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function merchandiser()
    {
        return $this->belongsTo(Merchandiser::class, 'merchandiser_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'id', 'company_id');
    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sales_associate()
    {
        return $this->belongsTo(SalesAssociate::class, 'sales_associate_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id', 'id');
    }
}
