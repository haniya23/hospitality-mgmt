<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
use Illuminate\Database\Seeder;

class SouthIndiaLocationSeeder extends Seeder
{
    public function run(): void
    {
        // Create India country
        $india = Country::firstOrCreate(['code' => 'IN'], ['name' => 'India']);

        // South India states data
        $southIndiaData = [
            [
                'state' => 'Kerala',
                'code' => 'KL',
                'districts' => [
                    [
                        'district' => 'Thiruvananthapuram',
                        'cities' => [
                            ['city' => 'Thiruvananthapuram', 'pincodes' => ['695001', '695003', '695004', '695010']],
                            ['city' => 'Technopark', 'pincodes' => ['695581']],
                            ['city' => 'Varkala', 'pincodes' => ['695141']],
                            ['city' => 'Neyyattinkara', 'pincodes' => ['695121']]
                        ]
                    ],
                    [
                        'district' => 'Kollam',
                        'cities' => [
                            ['city' => 'Kollam', 'pincodes' => ['691001', '691009']],
                            ['city' => 'Karunagappalli', 'pincodes' => ['690518']],
                            ['city' => 'Punalur', 'pincodes' => ['691305']]
                        ]
                    ],
                    [
                        'district' => 'Pathanamthitta',
                        'cities' => [
                            ['city' => 'Pathanamthitta', 'pincodes' => ['689645']],
                            ['city' => 'Thiruvalla', 'pincodes' => ['689101']],
                            ['city' => 'Adoor', 'pincodes' => ['691523']]
                        ]
                    ],
                    [
                        'district' => 'Alappuzha',
                        'cities' => [
                            ['city' => 'Alappuzha', 'pincodes' => ['688001', '688007']],
                            ['city' => 'Kayamkulam', 'pincodes' => ['690502']],
                            ['city' => 'Chengannur', 'pincodes' => ['689121']]
                        ]
                    ],
                    [
                        'district' => 'Kottayam',
                        'cities' => [
                            ['city' => 'Kottayam', 'pincodes' => ['686001', '686002']],
                            ['city' => 'Changanassery', 'pincodes' => ['686101']],
                            ['city' => 'Pala', 'pincodes' => ['686575']]
                        ]
                    ],
                    [
                        'district' => 'Idukki',
                        'cities' => [
                            ['city' => 'Munnar', 'pincodes' => ['685612']],
                            ['city' => 'Thodupuzha', 'pincodes' => ['685584']],
                            ['city' => 'Kattappana', 'pincodes' => ['685508']]
                        ]
                    ],
                    [
                        'district' => 'Ernakulam',
                        'cities' => [
                            ['city' => 'Kochi', 'pincodes' => ['682001', '682011', '682024']],
                            ['city' => 'Kakkanad', 'pincodes' => ['682030']],
                            ['city' => 'Aluva', 'pincodes' => ['683101']],
                            ['city' => 'Muvattupuzha', 'pincodes' => ['686661']]
                        ]
                    ],
                    [
                        'district' => 'Thrissur',
                        'cities' => [
                            ['city' => 'Thrissur', 'pincodes' => ['680001', '680005']],
                            ['city' => 'Guruvayur', 'pincodes' => ['680101']],
                            ['city' => 'Chalakudy', 'pincodes' => ['680307']]
                        ]
                    ],
                    [
                        'district' => 'Palakkad',
                        'cities' => [
                            ['city' => 'Palakkad', 'pincodes' => ['678001', '678007']],
                            ['city' => 'Ottapalam', 'pincodes' => ['679101']]
                        ]
                    ],
                    [
                        'district' => 'Malappuram',
                        'cities' => [
                            ['city' => 'Malappuram', 'pincodes' => ['676505']],
                            ['city' => 'Manjeri', 'pincodes' => ['676121']],
                            ['city' => 'Tirur', 'pincodes' => ['676101']]
                        ]
                    ],
                    [
                        'district' => 'Kozhikode',
                        'cities' => [
                            ['city' => 'Kozhikode', 'pincodes' => ['673001', '673004', '673032']],
                            ['city' => 'Koyilandy', 'pincodes' => ['673305']],
                            ['city' => 'Vatakara', 'pincodes' => ['673101']]
                        ]
                    ],
                    [
                        'district' => 'Wayanad',
                        'cities' => [
                            ['city' => 'Kalpetta', 'pincodes' => ['673121']],
                            ['city' => 'Sulthan Bathery', 'pincodes' => ['673592']],
                            ['city' => 'Mananthavady', 'pincodes' => ['670645']]
                        ]
                    ],
                    [
                        'district' => 'Kannur',
                        'cities' => [
                            ['city' => 'Kannur', 'pincodes' => ['670001', '670002']],
                            ['city' => 'Thalassery', 'pincodes' => ['670101']],
                            ['city' => 'Payyanur', 'pincodes' => ['670307']]
                        ]
                    ],
                    [
                        'district' => 'Kasaragod',
                        'cities' => [
                            ['city' => 'Kasaragod', 'pincodes' => ['671121']],
                            ['city' => 'Kanhangad', 'pincodes' => ['671315']]
                        ]
                    ]
                ]
            ],
            [
                'state' => 'Tamil Nadu',
                'code' => 'TN',
                'districts' => [
                    [
                        'district' => 'Chennai',
                        'cities' => [
                            ['city' => 'Chennai', 'pincodes' => ['600001', '600004', '600017', '600020']]
                        ]
                    ],
                    [
                        'district' => 'Coimbatore',
                        'cities' => [
                            ['city' => 'Coimbatore', 'pincodes' => ['641001', '641012', '641018']],
                            ['city' => 'Pollachi', 'pincodes' => ['642001']],
                            ['city' => 'Mettupalayam', 'pincodes' => ['641301']]
                        ]
                    ],
                    [
                        'district' => 'Madurai',
                        'cities' => [
                            ['city' => 'Madurai', 'pincodes' => ['625001', '625002', '625009', '625020']]
                        ]
                    ],
                    [
                        'district' => 'Tiruchirappalli',
                        'cities' => [
                            ['city' => 'Tiruchirappalli', 'pincodes' => ['620001', '620002']],
                            ['city' => 'Srirangam', 'pincodes' => ['620006']]
                        ]
                    ],
                    [
                        'district' => 'Salem',
                        'cities' => [
                            ['city' => 'Salem', 'pincodes' => ['636001', '636006', '636007']]
                        ]
                    ],
                    [
                        'district' => 'Kanyakumari',
                        'cities' => [
                            ['city' => 'Nagercoil', 'pincodes' => ['629001']],
                            ['city' => 'Kanyakumari', 'pincodes' => ['629702']]
                        ]
                    ],
                    [
                        'district' => 'Thanjavur',
                        'cities' => [
                            ['city' => 'Thanjavur', 'pincodes' => ['613001', '613005']],
                            ['city' => 'Kumbakonam', 'pincodes' => ['612001']]
                        ]
                    ],
                    [
                        'district' => 'Vellore',
                        'cities' => [
                            ['city' => 'Vellore', 'pincodes' => ['632001', '632006']],
                            ['city' => 'Katpadi', 'pincodes' => ['632007']]
                        ]
                    ],
                    [
                        'district' => 'Erode',
                        'cities' => [
                            ['city' => 'Erode', 'pincodes' => ['638001', '638003']]
                        ]
                    ],
                    [
                        'district' => 'Tirunelveli',
                        'cities' => [
                            ['city' => 'Tirunelveli', 'pincodes' => ['627001', '627006']],
                            ['city' => 'Palayamkottai', 'pincodes' => ['627002']]
                        ]
                    ],
                    [
                        'district' => 'Tiruppur',
                        'cities' => [
                            ['city' => 'Tiruppur', 'pincodes' => ['641601', '641602', '641604']]
                        ]
                    ],
                    [
                        'district' => 'Thoothukudi',
                        'cities' => [
                            ['city' => 'Thoothukudi', 'pincodes' => ['628001', '628002']]
                        ]
                    ],
                    [
                        'district' => 'Dindigul',
                        'cities' => [
                            ['city' => 'Dindigul', 'pincodes' => ['624001']],
                            ['city' => 'Kodaikanal', 'pincodes' => ['624101']]
                        ]
                    ],
                    [
                        'district' => 'Nilgiris',
                        'cities' => [
                            ['city' => 'Ooty (Udhagamandalam)', 'pincodes' => ['643001']],
                            ['city' => 'Coonoor', 'pincodes' => ['643101']]
                        ]
                    ],
                    [
                        'district' => 'Kanchipuram',
                        'cities' => [
                            ['city' => 'Kanchipuram', 'pincodes' => ['631501', '631502']]
                        ]
                    ],
                    [
                        'district' => 'Tiruvallur',
                        'cities' => [
                            ['city' => 'Tiruvallur', 'pincodes' => ['602001']],
                            ['city' => 'Avadi', 'pincodes' => ['600054']]
                        ]
                    ],
                    [
                        'district' => 'Chengalpattu',
                        'cities' => [
                            ['city' => 'Chengalpattu', 'pincodes' => ['603001']],
                            ['city' => 'Tambaram', 'pincodes' => ['600045']]
                        ]
                    ],
                    [
                        'district' => 'Krishnagiri',
                        'cities' => [
                            ['city' => 'Krishnagiri', 'pincodes' => ['635001']],
                            ['city' => 'Hosur', 'pincodes' => ['635109']]
                        ]
                    ],
                    [
                        'district' => 'Ariyalur',
                        'cities' => [
                            ['city' => 'Ariyalur', 'pincodes' => ['621704']]
                        ]
                    ],
                    [
                        'district' => 'Cuddalore',
                        'cities' => [
                            ['city' => 'Cuddalore', 'pincodes' => ['607001']]
                        ]
                    ],
                    [
                        'district' => 'Dharmapuri',
                        'cities' => [
                            ['city' => 'Dharmapuri', 'pincodes' => ['636701']]
                        ]
                    ],
                    [
                        'district' => 'Kallakurichi',
                        'cities' => [
                            ['city' => 'Kallakurichi', 'pincodes' => ['606202']]
                        ]
                    ],
                    [
                        'district' => 'Karur',
                        'cities' => [
                            ['city' => 'Karur', 'pincodes' => ['639001']]
                        ]
                    ],
                    [
                        'district' => 'Mayiladuthurai',
                        'cities' => [
                            ['city' => 'Mayiladuthurai', 'pincodes' => ['609001']]
                        ]
                    ],
                    [
                        'district' => 'Nagapattinam',
                        'cities' => [
                            ['city' => 'Nagapattinam', 'pincodes' => ['611001']]
                        ]
                    ],
                    [
                        'district' => 'Namakkal',
                        'cities' => [
                            ['city' => 'Namakkal', 'pincodes' => ['637001']]
                        ]
                    ],
                    [
                        'district' => 'Perambalur',
                        'cities' => [
                            ['city' => 'Perambalur', 'pincodes' => ['621212']]
                        ]
                    ],
                    [
                        'district' => 'Pudukkottai',
                        'cities' => [
                            ['city' => 'Pudukkottai', 'pincodes' => ['622001']]
                        ]
                    ],
                    [
                        'district' => 'Ramanathapuram',
                        'cities' => [
                            ['city' => 'Ramanathapuram', 'pincodes' => ['623501']]
                        ]
                    ],
                    [
                        'district' => 'Ranipet',
                        'cities' => [
                            ['city' => 'Ranipet', 'pincodes' => ['632401']]
                        ]
                    ],
                    [
                        'district' => 'Sivaganga',
                        'cities' => [
                            ['city' => 'Sivaganga', 'pincodes' => ['630561']]
                        ]
                    ],
                    [
                        'district' => 'Tenkasi',
                        'cities' => [
                            ['city' => 'Tenkasi', 'pincodes' => ['627811']]
                        ]
                    ],
                    [
                        'district' => 'Theni',
                        'cities' => [
                            ['city' => 'Theni', 'pincodes' => ['625531']]
                        ]
                    ],
                    [
                        'district' => 'Tirupathur',
                        'cities' => [
                            ['city' => 'Tirupathur', 'pincodes' => ['635601']]
                        ]
                    ],
                    [
                        'district' => 'Tiruvannamalai',
                        'cities' => [
                            ['city' => 'Tiruvannamalai', 'pincodes' => ['606601']]
                        ]
                    ],
                    [
                        'district' => 'Tiruvarur',
                        'cities' => [
                            ['city' => 'Tiruvarur', 'pincodes' => ['610001']]
                        ]
                    ],
                    [
                        'district' => 'Viluppuram',
                        'cities' => [
                            ['city' => 'Viluppuram', 'pincodes' => ['605602']]
                        ]
                    ],
                    [
                        'district' => 'Virudhunagar',
                        'cities' => [
                            ['city' => 'Virudhunagar', 'pincodes' => ['626001']]
                        ]
                    ]
                ]
            ],
            [
                'state' => 'Karnataka',
                'code' => 'KA',
                'districts' => [
                    [
                        'district' => 'Bengaluru Urban',
                        'cities' => [
                            ['city' => 'Bengaluru', 'pincodes' => ['560001', '560002', '560025', '560034', '560068']],
                            ['city' => 'Whitefield', 'pincodes' => ['560066']],
                            ['city' => 'Electronic City', 'pincodes' => ['560100']]
                        ]
                    ],
                    [
                        'district' => 'Mysuru',
                        'cities' => [
                            ['city' => 'Mysuru', 'pincodes' => ['570001', '570004', '570008']]
                        ]
                    ],
                    [
                        'district' => 'Dakshina Kannada',
                        'cities' => [
                            ['city' => 'Mangaluru', 'pincodes' => ['575001', '575002', '575003']],
                            ['city' => 'Surathkal', 'pincodes' => ['575014']]
                        ]
                    ],
                    [
                        'district' => 'Belagavi',
                        'cities' => [
                            ['city' => 'Belagavi', 'pincodes' => ['590001', '590002', '590006']]
                        ]
                    ],
                    [
                        'district' => 'Dharwad',
                        'cities' => [
                            ['city' => 'Hubballi', 'pincodes' => ['580020', '580023']],
                            ['city' => 'Dharwad', 'pincodes' => ['580001', '580007']]
                        ]
                    ],
                    [
                        'district' => 'Udupi',
                        'cities' => [
                            ['city' => 'Udupi', 'pincodes' => ['576101', '576102']],
                            ['city' => 'Manipal', 'pincodes' => ['576104']],
                            ['city' => 'Kundapura', 'pincodes' => ['576201']]
                        ]
                    ],
                    [
                        'district' => 'Ballari',
                        'cities' => [
                            ['city' => 'Ballari', 'pincodes' => ['583101', '583102']],
                            ['city' => 'Hosapete', 'pincodes' => ['583201']]
                        ]
                    ],
                    [
                        'district' => 'Shivamogga',
                        'cities' => [
                            ['city' => 'Shivamogga', 'pincodes' => ['577201', '577202']],
                            ['city' => 'Bhadravati', 'pincodes' => ['577301']]
                        ]
                    ],
                    [
                        'district' => 'Kalaburagi',
                        'cities' => [
                            ['city' => 'Kalaburagi', 'pincodes' => ['585101', '585102']]
                        ]
                    ],
                    [
                        'district' => 'Hassan',
                        'cities' => [
                            ['city' => 'Hassan', 'pincodes' => ['573201', '573202']],
                            ['city' => 'Belur', 'pincodes' => ['573115']]
                        ]
                    ],
                    [
                        'district' => 'Kodagu',
                        'cities' => [
                            ['city' => 'Madikeri', 'pincodes' => ['571201']],
                            ['city' => 'Kushalanagar', 'pincodes' => ['571234']]
                        ]
                    ],
                    [
                        'district' => 'Chikkamagaluru',
                        'cities' => [
                            ['city' => 'Chikkamagaluru', 'pincodes' => ['577101', '577102']]
                        ]
                    ],
                    [
                        'district' => 'Mandya',
                        'cities' => [
                            ['city' => 'Mandya', 'pincodes' => ['571401']]
                        ]
                    ],
                    [
                        'district' => 'Tumakuru',
                        'cities' => [
                            ['city' => 'Tumakuru', 'pincodes' => ['572101', '572102']]
                        ]
                    ],
                    [
                        'district' => 'Vijayapura',
                        'cities' => [
                            ['city' => 'Vijayapura', 'pincodes' => ['586101', '586102']]
                        ]
                    ],
                    [
                        'district' => 'Bagalkot',
                        'cities' => [
                            ['city' => 'Bagalkot', 'pincodes' => ['587101']]
                        ]
                    ],
                    [
                        'district' => 'Bengaluru Rural',
                        'cities' => [
                            ['city' => 'Doddaballapur', 'pincodes' => ['561203']],
                            ['city' => 'Nelamangala', 'pincodes' => ['562123']]
                        ]
                    ],
                    [
                        'district' => 'Bidar',
                        'cities' => [
                            ['city' => 'Bidar', 'pincodes' => ['585401']]
                        ]
                    ],
                    [
                        'district' => 'Chamarajanagar',
                        'cities' => [
                            ['city' => 'Chamarajanagar', 'pincodes' => ['571313']]
                        ]
                    ],
                    [
                        'district' => 'Chikkaballapur',
                        'cities' => [
                            ['city' => 'Chikkaballapur', 'pincodes' => ['562101']]
                        ]
                    ],
                    [
                        'district' => 'Chitradurga',
                        'cities' => [
                            ['city' => 'Chitradurga', 'pincodes' => ['577501']]
                        ]
                    ],
                    [
                        'district' => 'Davanagere',
                        'cities' => [
                            ['city' => 'Davanagere', 'pincodes' => ['577001', '577002']]
                        ]
                    ],
                    [
                        'district' => 'Gadag',
                        'cities' => [
                            ['city' => 'Gadag', 'pincodes' => ['582101']]
                        ]
                    ],
                    [
                        'district' => 'Haveri',
                        'cities' => [
                            ['city' => 'Haveri', 'pincodes' => ['581110']]
                        ]
                    ],
                    [
                        'district' => 'Kolar',
                        'cities' => [
                            ['city' => 'Kolar', 'pincodes' => ['563101']]
                        ]
                    ],
                    [
                        'district' => 'Koppal',
                        'cities' => [
                            ['city' => 'Koppal', 'pincodes' => ['583231']]
                        ]
                    ],
                    [
                        'district' => 'Raichur',
                        'cities' => [
                            ['city' => 'Raichur', 'pincodes' => ['584101']]
                        ]
                    ],
                    [
                        'district' => 'Ramanagara',
                        'cities' => [
                            ['city' => 'Ramanagara', 'pincodes' => ['562159']]
                        ]
                    ],
                    [
                        'district' => 'Uttara Kannada',
                        'cities' => [
                            ['city' => 'Karwar', 'pincodes' => ['581301']],
                            ['city' => 'Sirsi', 'pincodes' => ['581401']]
                        ]
                    ],
                    [
                        'district' => 'Vijayanagara',
                        'cities' => [
                            ['city' => 'Hospet', 'pincodes' => ['583201']],
                            ['city' => 'Hampi', 'pincodes' => ['583239']]
                        ]
                    ],
                    [
                        'district' => 'Yadgir',
                        'cities' => [
                            ['city' => 'Yadgir', 'pincodes' => ['585201']]
                        ]
                    ]
                ]
            ]
        ];

        // Process each state
        foreach ($southIndiaData as $stateData) {
            $state = State::firstOrCreate(
                ['country_id' => $india->id, 'name' => $stateData['state']],
                ['code' => $stateData['code']]
            );

            // Process each district
            foreach ($stateData['districts'] as $districtData) {
                $district = District::firstOrCreate([
                    'state_id' => $state->id,
                    'name' => $districtData['district']
                ]);

                // Process each city
                foreach ($districtData['cities'] as $cityData) {
                    $city = City::firstOrCreate([
                        'district_id' => $district->id,
                        'name' => $cityData['city']
                    ]);

                    // Process each pincode
                    foreach ($cityData['pincodes'] as $pincode) {
                        Pincode::firstOrCreate([
                            'city_id' => $city->id,
                            'code' => $pincode
                        ]);
                    }
                }
            }
        }

        $this->command->info('South India location data seeded successfully!');
    }
}
