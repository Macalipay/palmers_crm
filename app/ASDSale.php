<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ASDSale extends Model
{
    protected $fillable = [
        'rfq_no',
        'source_id',
        'category',
        'customer_type',
        'project_title',
        'company_name',
        'company_address',
        'contact_person',
        'designation',
        'telephone',
        'email',
        'date_received',
        'date_filed',
        'project_location',
        'tcp',
        'deadline',
        'comments',
        'sales_associate_id',
        'design_id',
        'supervisor',
        'date_submitted',
        'quoted_amount',
        'reference_no',
        'date_purchased',
        'po_no',
        'po_amount',
        'remarks',
        'active',
        'type',
        'created_by',
        'updated_by',
    ];

    protected $table = 'new_sales';
    
    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }
    
    public function sales()
    {
        return $this->belongsTo(Personnel::class, 'sales_associate_id');
    }
    
    public function design()
    {
        return $this->belongsTo(Personnel::class, 'design_id');
    }
    
    public function supervisor()
    {
        return $this->belongsTo(Personnel::class, 'supervisor');
    }
    
    public function setup()
    {
        return $this->hasMany(ProductSystemSetup::class, 'sales_id', 'id');
    }
}
