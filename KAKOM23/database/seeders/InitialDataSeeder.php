<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\SportEvent;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $collegeNames = [
            ['KOLEJ MATRIKULASI SELANGOR (KMS)', 'KMS', 'K01'],
            ['KOLEJ MATRIKULASI MELAKA (KMM)', 'KMM', 'K02'],
            ['KOLEJ MATRIKULASI NEGERI SEMBILAN (KMNS)', 'KMNS', 'K03'],
            ['KOLEJ MATRIKULASI JOHOR (KMJ)', 'KMJ', 'K04'],
            ['KOLEJ MATRIKULASI KEJUTERAAN JOHOR (KMKJ)', 'KMKJ', 'K05'],
            ['KOLEJ MATRIKULASI PERAK (KMPk)', 'KMPk', 'K06'],
            ['KOLEJ MATRIKULASI PULAU PINANG (KMPP)', 'KMPP', 'K07'],
            ['KOLEJ MATRIKULASI KEDAH (KMK)', 'KMK', 'K08'],
            ['KOLEJ MATRIKULASI PERLIS (KMP)', 'KMP', 'K09'],
            ['KOLEJ MATRIKULASI PAHANG (KMPh)', 'KMPh', 'K10'],
            ['KOLEJ MATRIKULASI KEJUTERAAN PAHANG (KMPKh)', 'KMPKh', 'K11'],
            ['KOLEJ MATRIKULASI KEJUTERAAN KEDAH (KMKK)', 'KMKK', 'K12'],
            ['KOLEJ MARA KULIM', 'KMKULIM', 'K13'],
            ['KOLEJ MARA KUALA NERAM', 'KMKN', 'K14'],
        ];

        $colleges = array_map(function ($college) {
            return [
                'name' => $college[0],
                'code' => $college[1],
                'legacy_code' => $college[2],
                'access_code' => $college[1] . '-KAKOM23',
                'role' => 'college',
            ];
        }, $collegeNames);

        $colleges[] = [
            'name' => 'Urusetia KAKOM 23',
            'code' => 'URUSETIA',
            'access_code' => 'URUSETIA-KAKOM23',
            'role' => 'secretariat',
        ];

        foreach ($colleges as $college) {
            $legacyCode = $college['legacy_code'] ?? null;
            unset($college['legacy_code']);

            $collegeModel = College::whereIn('code', array_filter([$college['code'], $legacyCode]))->first();

            if ($collegeModel) {
                $collegeModel->update($college);
            } else {
                College::create($college);
            }
        }

        College::whereIn('code', ['K15', 'K16', 'K17'])
            ->whereDoesntHave('registrations')
            ->delete();

        $events = [
            ['Bola Sepak', 'bola-sepak', true, 18],
            ['Bola Jaring Perempuan', 'bola-jaring', false, 10],
            ['Bola Tampar Lelaki', 'bola-tampar-lelaki', true, 12],
            ['Bola Tampar Perempuan', 'bola-tampar-perempuan', true, 12],
            ['Sepak Takraw', 'sepak-takraw', true, 5],
            ['Petanque Lelaki', 'petanque-lelaki', false, 3],
            ['Petanque Perempuan', 'petanque-perempuan', false, 3],
            ['Tenis Lelaki', 'tenis-lelaki', false, 3],
            ['Tenis Perempuan', 'tenis-perempuan', false, 3],
            ['Skuasy Lelaki', 'skuasy-lelaki', false, 25],
            ['Skuasy Perempuan', 'skuasy-perempuan', false, 25],
            ['Bola Keranjang Lelaki', 'bola-keranjang-lelaki', true, 5],
            ['Bola Keranjang Perempuan', 'bola-keranjang-perempuan', true, 5],
            ['Badminton Lelaki', 'badminton-lelaki', false, 3],
            ['Badminton Perempuan', 'badminton-perempuan', false, 3],
        ];

        foreach ($events as $index => $event) {
            SportEvent::updateOrCreate(
                ['slug' => $event[1]],
                [
                    'name' => $event[0],
                    'requires_jersey_no' => $event[2],
                    'max_students' => $event[3],
                    'sort_order' => $index + 1,
                ]
            );
        }

        SportEvent::whereIn('slug', [
            'bola-sepak-perempuan',
            'sepak-takraw-perempuan',
            'bola-jaring-lelaki',
        ])->delete();
    }
}
