<?php

namespace Database\Seeders;

use App\Models\Connection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 500; $i++) {
            $randomValue = rand(1, 100); 
            if ($randomValue <= 70) {
                $role = 1;
            } else {
                $role = rand(3, 5);
            }
            DB::table("connections")->insert([
                [
                    'id_client'     => rand(1, 38),
                    'id_group'      => rand(1, 22),
                    'id_role'       => $role,
                ],
            ]);
        }
        $duplicateRecords = Connection::select('id_client', 'id_group')
        ->groupBy('id_client', 'id_group')
        ->havingRaw('COUNT(*) > 1')
        ->get();

        // Xoá các bản ghi trùng nhau, chỉ giữ lại một bản ghi duy nhất
        foreach ($duplicateRecords as $record) {
            Connection::where('id_client', $record->id_client)
            ->where('id_group', $record->id_group)
            ->limit($record->count - 1)
            ->delete();
        }
    }
}
