<?php declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Domains\CoreApp\Migration\MigrationAbstract;

return new class() extends MigrationAbstract {
    /**
     * @return void
     */
    public function up(): void
    {
        if ($this->upMigrated()) {
            return;
        }

        $this->tables();
        $this->keys();
    }

    /**
     * @return bool
     */
    protected function upMigrated(): bool
    {
        return Schema::hasColumn('position', 'latitude');
    }

    /**
     * @return void
     */
    protected function tables(): void
    {
        Schema::table('alarm_notification', function (Blueprint $table) {
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
        });

        Schema::table('city', function (Blueprint $table) {
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
        });

        Schema::table('position', function (Blueprint $table) {
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
        });

        // Populate latitude and longitude from existing point data
        $this->db()->unprepared('
            UPDATE `alarm_notification`
            SET `latitude` = ROUND(ST_LATITUDE(`point`), 5),
                `longitude` = ROUND(ST_LONGITUDE(`point`), 5)
            WHERE `point` IS NOT NULL;
        ');

        $this->db()->unprepared('
            UPDATE `city`
            SET `latitude` = ROUND(ST_LATITUDE(`point`), 5),
                `longitude` = ROUND(ST_LONGITUDE(`point`), 5)
            WHERE `point` IS NOT NULL;
        ');

        $this->db()->unprepared('
            UPDATE `position`
            SET `latitude` = ROUND(ST_LATITUDE(`point`), 5),
                `longitude` = ROUND(ST_LONGITUDE(`point`), 5)
            WHERE `point` IS NOT NULL;
        ');
    }

    /**
     * @return void
     */
    protected function keys(): void
    {
        Schema::table('alarm_notification', function (Blueprint $table) {
            $this->tableAddIndex($table, 'latitude');
            $this->tableAddIndex($table, 'longitude');
        });

        Schema::table('city', function (Blueprint $table) {
            $this->tableAddIndex($table, 'latitude');
            $this->tableAddIndex($table, 'longitude');
        });

        Schema::table('position', function (Blueprint $table) {
            $this->tableAddIndex($table, 'latitude');
            $this->tableAddIndex($table, 'longitude');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::table('alarm_notification', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });

        Schema::table('city', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });

        Schema::table('position', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
        });
    }
};
