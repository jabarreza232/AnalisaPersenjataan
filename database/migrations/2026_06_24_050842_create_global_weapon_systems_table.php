<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_weapons_systems', function (Blueprint $table) {
            $table->id();
            $table->string('Weapon_Name')->nullable();
            $table->string('Country_of_Origin')->nullable();
            $table->string('Manufacturer')->nullable();
            $table->string('Category')->nullable();
            $table->string('Subcategory')->nullable();
            $table->string('Year_Introduced')->nullable();
            $table->string('Year_Retired')->nullable();
            $table->string('Service_Status')->nullable();
            $table->string('Caliber')->nullable();
            $table->string('Action_Type')->nullable();
            $table->string('Effective_Range_m')->nullable();
            $table->string('Max_Range_m')->nullable();
            $table->string('Weight_kg')->nullable();
            $table->string('Length_mm')->nullable();
            $table->string('Barrel_Length_mm')->nullable();
            $table->string('Muzzle_Velocity_mps')->nullable();
            $table->string('Rate_of_Fire_rpm')->nullable();
            $table->string('Magazine_Capacity')->nullable();
            $table->string('Warhead_Weight_kg')->nullable();
            $table->string('Max_Speed_kmh')->nullable();
            $table->string('Crew_Size')->nullable();
            $table->string('Unit_Cost_USD')->nullable();
            $table->string('Num_Operator_Nations')->nullable();
            $table->string('Primary_Users')->nullable();
            $table->string('NATO_Compatible')->nullable();
            $table->string('Generation')->nullable();
            $table->string('Theater_of_Operation')->nullable();
            $table->string('Export_Status')->nullable();
            $table->string('Combat_Proven')->nullable();
            $table->string('Propulsion_Type')->nullable();
            $table->string('Guidance_System')->nullable();
            $table->string('Communication_System')->nullable();
            $table->string('Operating_Environment')->nullable();
            $table->string('Protection_Level')->nullable();
            $table->text('Notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_weapons_systems');
    }
};
