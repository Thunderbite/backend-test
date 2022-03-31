<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PDO;

class GamesTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 2; $i++) {
            try {
                // Store and process the file
                $filePath = addslashes(public_path('data/games.csv'));
                $string = @file_get_contents($filePath);
                $string = preg_replace('~\r\n?~', "\n", $string);
                file_put_contents($filePath, $string);

                $pdo = DB::connection()->getPdo();

                $pdo->exec("LOAD DATA LOCAL INFILE '".$filePath."'
                INTO TABLE games
                FIELDS TERMINATED BY ','
                LINES TERMINATED BY '\\n'
                IGNORE 1 LINES
                (campaign_id, account, prizeId)
                SET revealed_at=FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) - FLOOR(0 + (RAND() * 1209600))),
                created_at=revealed_at, updated_at=revealed_at");
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
