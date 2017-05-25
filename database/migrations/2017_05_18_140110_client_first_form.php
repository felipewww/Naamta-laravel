<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientFirstForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_first_forms', function (Blueprint $table) {

            //basic info
            $table->integer('client_id')->unsigned()->unique();
            $table->enum('status',[0,1]);
            $table->enum('services_accredited', ['medical_transport', 'medical_escort']);
            $table->text('taxpayer_id'); //file
            $table->char('address_street', 255);
            $table->text('address_mailing');
            $table->char('phone_number', 45);
            $table->enum('business_type', [
                'sole_proprietorship',
                'partnership',
                'non_profit',
                'corporation',
                'government_entity',
                'llc',
                'other'
            ]);

            $table->char('website', 255);
            $table->text('ownerships');

            //contact information
            $table->char('contact_name', 45);
            $table->char('contact_email', 255);
            $table->char('contact_phone',45);
            $table->char('compliance_name', 45);
            $table->char('compliance_email', 45);
            $table->char('compliance_phone', 45);
            $table->text('application_access');

            //business information
            $table->text('since'); //minimum six months
            $table->integer('transports_per_year');
            $table->text('base_locations');
            $table->text('communications_center'); 
            $table->text('description');

            //medical information
            $table->enum('patient_population', ['adult', 'pediatric', 'neonatal']);
            $table->char('medical_director_name', 45);
            $table->text('medical_based');
            $table->text('medical_drug_license'); //fileupload

            //Accreditation criteria
            $table->text('customer_reference_letter_1'); //fileupload
            $table->text('customer_reference_letter_2'); //fileupload
            $table->text('signed_acknowledgment_doc'); //fileupload

            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')->on('clients')
                ->onDelete('cascade');

            $table->primary('client_id');
        });

        DB::statement("ALTER TABLE client_first_forms ADD form_json MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('client_first_forms');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
