<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Plans
        \App\Models\Plan::updateOrCreate([
            'slug' => 'premium',
        ], [
            'name' => 'Premium',
            'price_1' => 7.99,
            'price_12' => 6.39,
            'price_24' => 5.59,
            'features' => [
                'Up to 3 active bots',
                'High speed continuous hosting',
                'Remove all HublyBot brand signatures',
            ],
        ]);

        \App\Models\Plan::updateOrCreate([
            'slug' => 'pro',
        ], [
            'name' => 'Pro',
            'price_1' => 19.99,
            'price_12' => 15.99,
            'price_24' => 13.99,
            'features' => [
                'Up to 10 active bots',
                'Ultra speed continuous hosting',
                'Remove all HublyBot brand signatures',
                'Priority developer support',
            ],
        ]);

        // Seed Coupons
        \App\Models\Coupon::updateOrCreate([
            'code' => 'HUBLY20',
        ], [
            'discount_percent' => 20,
            'is_active' => true,
        ]);

        \App\Models\Coupon::updateOrCreate([
            'code' => 'WELCOME10',
        ], [
            'discount_percent' => 10,
            'is_active' => true,
        ]);

        \App\Models\Coupon::updateOrCreate([
            'code' => 'BRAINROT',
        ], [
            'discount_percent' => 67,
            'is_active' => true,
        ]);

        \App\Models\Coupon::updateOrCreate([
            'code' => 'AURADEV',
        ], [
            'discount_percent' => 5,
            'is_active' => true,
        ]);

        // Seed Countries & Tax Rates
        $taxRatesConfig = [
            'FR' => 0.20, 'DE' => 0.19, 'ES' => 0.21, 'IT' => 0.22, 'NL' => 0.21,
            'GB' => 0.20, 'UK' => 0.20, 'US' => 0.08, 'CA' => 0.12, 'AU' => 0.10,
            'HK' => 0.00, 'CH' => 0.081, 'SG' => 0.09, 'JP' => 0.10
        ];

        $allCountries = [
            'AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AD' => 'Andorra', 'AO' => 'Angola', 
            'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AU' => 'Australia', 'AT' => 'Austria', 
            'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 
            'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhutan', 
            'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BR' => 'Brazil', 'BN' => 'Brunei', 
            'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 
            'CA' => 'Canada', 'CV' => 'Cape Verde', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 
            'CN' => 'China', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CR' => 'Costa Rica', 
            'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 
            'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 
            'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 
            'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GA' => 'Gabon', 'GM' => 'Gambia', 
            'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GR' => 'Greece', 'GD' => 'Grenada', 
            'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 
            'HN' => 'Honduras', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 
            'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IL' => 'Israel', 'IT' => 'Italy', 
            'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 
            'KI' => 'Kiribati', 'KP' => 'North Korea', 'KR' => 'South Korea', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 
            'LA' => 'Laos', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 
            'LY' => 'Libya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MK' => 'North Macedonia', 
            'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 
            'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'MX' => 'Mexico', 
            'FM' => 'Micronesia', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 
            'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 
            'NP' => 'Nepal', 'NL' => 'Netherlands', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 
            'NG' => 'Nigeria', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 
            'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 
            'PL' => 'Poland', 'PT' => 'Portugal', 'QA' => 'Qatar', 'RO' => 'Romania', 'RU' => 'Russia', 
            'RW' => 'Rwanda', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent', 
            'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome and Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 
            'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 
            'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'ES' => 'Spain', 
            'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SE' => 'Sweden', 'CH' => 'Switzerland', 
            'SY' => 'Syria', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 
            'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 
            'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 
            'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UY' => 'Uruguay', 
            'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'YE' => 'Yemen', 
            'ZM' => 'Zambia', 'ZW' => 'Zimbabwe', 'HK' => 'Hong Kong'
        ];

        foreach ($allCountries as $code => $name) {
            \App\Models\Country::updateOrCreate([
                'code' => $code,
            ], [
                'name' => $name,
                'tax_rate' => $taxRatesConfig[$code] ?? 0.10,
            ]);
        }

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
