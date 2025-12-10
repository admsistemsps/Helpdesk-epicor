<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\MasterDepartment;
use App\Models\MasterDivision;
use App\Models\MasterRole;
use App\Models\MasterPosition;
use App\Models\User;
use App\Models\MasterMenu;
use App\Models\MasterSubMenu;
use App\Models\MasterSite;
use App\Models\TicketPriority;
use App\Models\KbCategory;
use App\Models\KbArticle;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========================
        // Departments
        // ========================
        $departments = [
            ['code' => 'PTSI', 'name' => 'Pengembangan Teknologi dan Sistem Informasi', 'description' => 'Departemen yang mengelola teknologi dan sistem informasi.'],
            ['code' => 'SCM', 'name' => 'Supply Chain Management', 'description' => 'Departemen yang mengelola rantai pasok perusahaan.'],
            ['code' => 'FAC', 'name' => 'Finance and Accounting', 'description' => 'Departemen yang mengelola keuangan dan akuntansi.'],
            ['code' => 'PRC', 'name' => 'Procurement', 'description' => 'Departemen yang mengelola pengadaan barang dan jasa.'],
            ['code' => 'SNM', 'name' => 'Sales and Marketing', 'description' => 'Departemen yang menawarkan dan menjual produk.'],
        ];

        foreach ($departments as $dept) {
            MasterDepartment::updateOrCreate(
                ['code' => $dept['code']],
                $dept
            );
        }

        // ========================
        // Divisions
        // ========================
        $divisions = [
            ['code' => 'WRH', 'name' => 'Warehouse', 'master_department_id' => 2],
            ['code' => 'PRC', 'name' => 'Purchasing', 'master_department_id' => 4],
            ['code' => 'SNM', 'name' => 'Sales', 'master_department_id' => 5],
            ['code' => 'LOG', 'name' => 'Logistik', 'master_department_id' => 2],
        ];

        foreach ($divisions as $div) {
            MasterDivision::updateOrCreate(
                ['code' => $div['code']],
                $div
            );
        }

        // ========================
        // Roles
        // ========================
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Super Admin', 'level' => 99],
            ['name' => 'Admin Sistem', 'description' => 'Admin Sistem', 'level' => 88],
            ['name' => 'User', 'description' => 'User', 'level' => 1],
            ['name' => 'JM', 'description' => 'JM', 'level' => 2],
        ];

        foreach ($roles as $role) {
            MasterRole::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        // ========================
        // Positions
        // ========================
        $positions = [
            ['name' => 'Admin Gudang Sparepart', 'master_division_id' => 1, 'master_department_id' => 2, 'jabatan' => 'Staff/Admin', 'level' => 1],
            ['name' => 'SPV Gudang', 'master_division_id' => 1, 'master_department_id' => 2, 'jabatan' => 'Supervisor', 'level' => 2],
            ['name' => 'Manajer Gudang', 'master_division_id' => null, 'master_department_id' => 2, 'jabatan' => 'Manajer', 'level' => 3],
            ['name' => 'JM Finance', 'master_division_id' => null, 'master_department_id' => 3, 'jabatan' => 'Junior Manajer', 'level' => 9],
            ['name' => 'Admin Sistem', 'master_division_id' => null, 'master_department_id' => 1, 'jabatan' => 'Staff/Admin', 'level' => 1],
            ['name' => 'IT Dev', 'master_division_id' => null, 'master_department_id' => 1, 'jabatan' => 'Staff/Admin', 'level' => 1],
            ['name' => 'Manajer PTSI', 'master_division_id' => null, 'master_department_id' => 1, 'jabatan' => 'Manajer', 'level' => 3],
            ['name' => 'SPV Purchasing', 'master_division_id' => 2, 'master_department_id' => 4, 'jabatan' => 'Supervisor', 'level' => 2],
            ['name' => 'Staff Purchasing', 'master_division_id' => 2, 'master_department_id' => 4, 'jabatan' => 'Staff/Admin', 'level' => 1],
            ['name' => 'SPV Sales', 'master_division_id' => 3, 'master_department_id' => 5, 'jabatan' => 'Supervisor', 'level' => 2],
            ['name' => 'Staff Sales', 'master_division_id' => 3, 'master_department_id' => 5, 'jabatan' => 'Staff/Admin', 'level' => 1],
            ['name' => 'SPV Logistik', 'master_division_id' => 4, 'master_department_id' => 2, 'jabatan' => 'Supervisor', 'level' => 2],
            ['name' => 'Staff Logistik', 'master_division_id' => 4, 'master_department_id' => 2, 'jabatan' => 'Staff/Admin', 'level' => 1],
        ];

        foreach ($positions as $index => $pos) {
            MasterPosition::updateOrCreate(
                ['name' => $pos['name']],
                $pos
            );
        }

        // ========================
        // Users
        // ========================
        $site = [
            [
                'code' => 'KDR',
                'name' => 'KEDIRI',
                'address' => 'Jl. Dusun Bringin No.300, Bringin, Wonosari, Kec. Pagu, Kabupaten Kediri, Jawa Timur 64183',
            ],
            [
                'code' => 'NGW',
                'name' => 'NGAWI',
                'address' => 'Jl. Dusun Bringin No.300, Bringin, Wonosari, Kec. Pagu, Kabupaten Kediri, Jawa Timur 64183',
            ],
            [
                'code' => 'SNG',
                'name' => 'SUBANG',
                'address' => 'Jl. Dusun Bringin No.300, Bringin, Wonosari, Kec. Pagu, Kabupaten Kediri, Jawa Timur 64183',
            ]
        ];

        foreach ($site as $site) {
            MasterSite::updateOrCreate(
                ['code' => $site['code']],
                $site
            );
        }

        $users = [
            [
                'username' => 'spsadmin',
                'name' => 'JM PTSI SPS',
                'email' => 'spsadmin@gmail.com',
                'password' => Hash::make('SuperAdmin123!'),
                'status' => 'active',
                'master_role_id' => 1, // Super Admin
                'master_position_id' => 7, // Manajer PTSI
                'master_division_id' => null,
                'master_department_id' => 1,
                'master_site_id' => 1,
            ],
            [
                'username' => 'scm001',
                'name' => 'User SCM 001',
                'email' => 'scm001@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'master_role_id' => 3, // User
                'master_position_id' => 1, // Admin Gudang Sparepart
                'master_division_id' => 1,
                'master_department_id' => 2,
                'master_site_id' => 1,
            ],
            [
                'username' => 'scm002',
                'name' => 'User SCM 002',
                'email' => 'scm002@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'master_role_id' => 3,
                'master_position_id' => 2, // SPV Gudang
                'master_division_id' => 1,
                'master_department_id' => 2,
                'master_site_id' => 1,
            ],
            [
                'username' => 'scm003',
                'name' => 'User SCM 003',
                'email' => 'scm003@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'master_role_id' => 3,
                'master_position_id' => 3, // Manajer Gudang
                'master_division_id' => null,
                'master_department_id' => 2,
                'master_site_id' => 1,
            ],
            [
                'username' => 'scm004',
                'name' => 'Staff Log User SCM 004',
                'email' => 'scm004@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'master_role_id' => 3,
                'master_position_id' => 13,
                'master_division_id' => 4,
                'master_department_id' => 2,
                'master_site_id' => 1,
            ],
            [
                'username' => 'scm005',
                'name' => 'SPV Log User SCM 004',
                'email' => 'scm005@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'master_role_id' => 3,
                'master_position_id' => 12,
                'master_division_id' => 4,
                'master_department_id' => 2,
                'master_site_id' => 1,
            ],
            [
                'username' => 'snm001',
                'name' => 'SPV Sales User SNM 001',
                'email' => 'snm001@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'master_role_id' => 3,
                'master_position_id' => 10,
                'master_division_id' => 3,
                'master_department_id' => 5,
                'master_site_id' => 1,
            ],
            [
                'username' => 'fac001',
                'name' => 'JM FAC',
                'email' => 'fac001@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'master_role_id' => 4, // JM
                'master_position_id' => 4, // JM Finance
                'master_division_id' => null,
                'master_department_id' => 3,
                'master_site_id' => 1,
            ],
            [
                'username' => 'sis003',
                'name' => 'ADMIN SIS 003',
                'email' => 'sis003@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'master_role_id' => 2, // Admin Sistem
                'master_position_id' => 5, // Admin Sistem
                'master_division_id' => null,
                'master_department_id' => 1,
                'master_site_id' => 1,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $menus = [
            [
                'name' => 'Purchase Order Entry',
                'description' => 'Pesanan Pembelian',
            ],
            [
                'name' => 'Customer Shipment Entry',
                'description' => 'Pengiriman Customer',
            ],

        ];

        foreach ($menus as $menu) {
            MasterMenu::updateOrCreate(
                ['name' => $menu['name']],
                $menu
            );
        }

        $subMenus = [
            [
                'name' => 'Revisi PR',
                'description' => 'Revisi Permintaan',
                'master_menu_id' => 1,
            ],
            [
                'name' => 'Unshipped DO',
                'description' => 'Batalkan Kiriman',
                'master_menu_id' => 2,
            ],
        ];

        foreach ($subMenus as $subMenu) {
            MasterSubMenu::updateOrCreate(
                ['name' => $subMenu['name']],
                $subMenu
            );
        }

        $priority = [
            [
                'name' => 'Urgent',
                'sla_hours' => 4
            ],
            [
                'name' => 'High',
                'sla_hours' => 8
            ],
            [
                'name' => 'Medium',
                'sla_hours' => 24
            ],
            [
                'name' => 'Low',
                'sla_hours' => 72
            ],
        ];

        foreach ($priority as $priority) {
            TicketPriority::updateOrCreate(
                ['name' => $priority['name']],
                $priority
            );
        }
        $categories = [
            ['name' => 'Panduan Langkah-demi-Langkah', 'slug' => 'how-to-guides', 'description' => 'Tutorial step-by-step untuk pengguna', 'sort_order' => 1],
            ['name' => 'Pemecahan Masalah', 'slug' => 'troubleshooting', 'description' => 'Solusi masalah umum di aplikasi', 'sort_order' => 2],
            ['name' => 'Pertanyaan yang Sering Diajukan (FAQ)', 'slug' => 'faq', 'description' => 'Kumpulan pertanyaan & jawaban', 'sort_order' => 3],
            ['name' => 'Informasi & Kebijakan', 'slug' => 'informasi-kebijakan', 'description' => 'Standar operasional, kebijakan, dan info', 'sort_order' => 4],
        ];


        foreach ($categories as $c) {
            $cat = KbCategory::updateOrCreate(['slug' => $c['slug']], $c);


            // Contoh artikel awal per kategori (boleh dihapus/ubah)
            KbArticle::updateOrCreate(
                ['slug' => Str::slug($c['slug'] . '-contoh-artikel')],
                [
                    'category_id' => $cat->id,
                    'title' => 'Contoh Artikel: ' . $c['name'],
                    'summary' => 'Ringkasan singkat untuk ' . $c['name'],
                    'content' => '<p>Ini adalah <strong>contoh artikel</strong> untuk kategori <em>' . $c['name'] . '</em>. Silakan edit dari menu admin nanti.</p><ol><li>Langkah 1</li><li>Langkah 2</li><li>Langkah 3</li></ol>',
                    'status' => 'published',
                    'tags' => ['contoh', 'awal'],
                    'is_pinned' => false,
                ]
            );
        }
        $this->command->info('Seeder selesai: Departments, Divisions, Roles, Positions, Users, KB.');
    }
}
