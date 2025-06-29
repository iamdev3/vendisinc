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
            if (Schema::hasTable('products')) {
                return;
            }else{
                Schema::create('products', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('slug')->unique();
                    $table->foreignId('brand_id')->constrained("brands")->cascadeOnDelete();
                    $table->foreignId('category_id')->nullable()->constrained("categories")->nullOnDelete();
                    $table->string('description')->nullable();
                    $table->string('image')->nullable();
                    $table->integer('base_price')->nullable();
                    $table->integer('sell_price')->nullable();
                    $table->integer('quantity')->nullable();
                    $table->text('additional_info')->nullable();
                    $table->tinyInteger('is_featured')->default(0);
                    $table->tinyInteger('is_popular')->default(0);
                    $table->tinyInteger('is_new')->default(0);
                    $table->tinyInteger('is_active')->default(1);
                    $table->timestamps();
                });
            }
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('products');
        }
    };
