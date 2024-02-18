<?php

namespace Database\Seeders;

use App\Models\Insentif;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsentifsSeeder extends Seeder
{
    protected $table;
    public function __construct()
    {
        $this->table = 'insentifs';
    }

    public function run(): void
    {
        // Note: 5. Team Leader, 6. SPG Freelancer, 7. SPG Trainer
        $insentifs = [
            [
                'access_id' => 5,
                'sales_qty' => 160,
                'insentive' => 50000,
                'period' => 'daily',
            ],
            [
                'access_id' => 5,
                'sales_qty' => 200,
                'insentive' => 100000,
                'period' => 'daily',
            ],
            [
                'access_id' => 5,
                'sales_qty' => 250,
                'insentive' => 150000,
                'period' => 'daily',
            ],
            [
                'access_id' => 6,
                'sales_qty' => 43,
                'insentive' => 50000,
                'period' => 'daily',
            ],
            [
                'access_id' => 6,
                'sales_qty' => 186,
                'insentive' => 100000,
            ],
            [
                'access_id' => 6,
                'sales_qty' => 246,
                'insentive' => 250000,
            ],
            [
                'access_id' => 6,
                'sales_qty' => 306,
                'insentive' => 325000,
            ],
            [
                'access_id' => 6,
                'sales_qty' => 366,
                'insentive' => 400000,
            ],
            [
                'access_id' => 6,
                'sales_qty' => 426,
                'insentive' => 550000,
            ],
            [
                'access_id' => 6,
                'sales_qty' => 486,
                'insentive' => 675000,
            ],
            [
                'access_id' => 6,
                'sales_qty' => 1170,
                'insentive' => 'MAGICOM',
                'type' => 'thing',
                'period' => 'monthly',
            ],
            [
                'access_id' => 6,
                'sales_qty' => 1430,
                'insentive' => '2 GRAM EMAS',
                'type' => 'thing',
                'period' => 'monthly',
            ],
            [
                'access_id' => 6,
                'sales_qty' => 1690,
                'insentive' => 'MESIN CUCI 2 T',
                'type' => 'thing',
                'period' => 'monthly',
            ],
            [
                'access_id' => 6,
                'sales_qty' => 1950,
                'insentive' => '1 UNIT HP',
                'type' => 'thing',
                'period' => 'monthly',
            ],
            [
                'access_id' => 6,
                'sales_qty' => 2210,
                'insentive' => 'TV 40 INC',
                'type' => 'thing',
                'period' => 'monthly',
            ],
            [
                'access_id' => 7,
                'sales_qty' => 15,
                'insentive' => 65000,
                'period' => 'daily',
            ],
            [
                'access_id' => 7,
                'sales_qty' => 25,
                'insentive' => 100000,
                'period' => 'daily',
            ],
            [
                'access_id' => 7,
                'sales_qty' => 35,
                'insentive' => 150000,
                'period' => 'daily',
            ],
            [
                'access_id' => 7,
                'sales_qty' => 45,
                'insentive' => 200000,
                'period' => 'daily',
            ],
        ];

        foreach ($insentifs as $insentif) {
            Insentif::create($insentif);
        }
    }
}
