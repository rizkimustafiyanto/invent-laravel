<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE audit_logs ALTER COLUMN auditable_id TYPE varchar(255) USING auditable_id::text');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE audit_logs ALTER COLUMN auditable_id TYPE uuid USING auditable_id::uuid');
    }
};
