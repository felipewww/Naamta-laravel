<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientFirstForm extends Model
{
    public $primaryKey = 'client_id';
    public $fillable = [
        'client_id',
        'client_id',
        'status',
        'services_accredited',
        'taxpayer_id',
        'address_street',
        'address_mailing',
        'phone_number',
        'business_type',
        'website',
        'ownerships',
        'contact_name',
        'contact_email',
        'contact_phone',
        'compliance_name',
        'compliance_email',
        'compliance_phone',
        'application_access',
        'since',
        'transports_per_year',
        'base_locations',
        'communications_center',
        'description',
        'patient_population',
        'medical_director_name',
        'medical_based',
        'medical_drug_license',
        'customer_reference_letter_1',
        'customer_reference_letter_2',
        'signed_acknowledgment_doc'
    ];
}
