<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('clientes');
    }

    public function down(): void
    {
        // Los clientes ahora son usuarios con rol cliente; no se recrea la tabla.
    }
};
