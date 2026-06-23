<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->unsignedBigInteger('assigned_employee_id')->nullable();
            $table->unsignedBigInteger('created_by_employee_id');
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index('assigned_employee_id');
            $table->index('created_by_employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
