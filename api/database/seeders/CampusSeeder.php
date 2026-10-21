<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('password123');

        // Roles
        $roles = ['admin', 'secretary', 'professor', 'student'];
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['name' => $role], ['updated_at' => $now]);
        }
        $roleIds = DB::table('roles')->pluck('id', 'name');

        // Departments
        $departments = [
            ['code' => 'DIT', 'name' => 'Τμήμα Πληροφορικής και Τηλεπικοινωνιών'],
            ['code' => 'ECON', 'name' => 'Τμήμα Οικονομικών Επιστημών'],
            ['code' => 'MST', 'name' => 'Τμήμα Διοικητικής Επιστήμης και Τεχνολογίας'],
            ['code' => 'NURS', 'name' => 'Τμήμα Νοσηλευτικής'],
        ];
        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(['code' => $dept['code']], ['name' => $dept['name'], 'updated_at' => $now]);
        }
        $deptIds = DB::table('departments')->pluck('id', 'code');

        // Buildings
        DB::table('buildings')->updateOrInsert(['name' => 'Κάτω Κτίριο (Κεντρικό Κτίριο / Πληροφορικής)'], ['updated_at' => $now]);
        DB::table('buildings')->updateOrInsert(['name' => 'Πάνω Κτίριο (Σχολή Οικονομίας & Τεχνολογίας)'], ['updated_at' => $now]);

        $katoKtirioId = DB::table('buildings')->where('name', 'like', 'Κάτω Κτίριο%')->value('id');
        $panoKtirioId = DB::table('buildings')->where('name', 'like', 'Πάνω Κτίριο%')->value('id');

        // Rooms & Labs
        $rooms = [
            // Κάτω Κτίριο (DIT Dedicated) - Ground Floor
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'Ι4', 'type' => 'lecture_hall', 'floor' => 0, 'capacity' => 90, 'exam_capacity' => 45, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => true, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'Ι5', 'type' => 'lecture_hall', 'floor' => 0, 'capacity' => 34, 'exam_capacity' => 17, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'Ι6', 'type' => 'computer_lab', 'floor' => 0, 'capacity' => 25, 'exam_capacity' => 25, 'has_projector' => true, 'computer_count' => 25, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'Ι8', 'type' => 'lecture_hall', 'floor' => 0, 'capacity' => 46, 'exam_capacity' => 23, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'Ι9', 'type' => 'computer_lab', 'floor' => 0, 'capacity' => 24, 'exam_capacity' => 24, 'has_projector' => true, 'computer_count' => 24, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'Ι11', 'type' => 'lecture_hall', 'floor' => 0, 'capacity' => 43, 'exam_capacity' => 22, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'Ι13', 'type' => 'lecture_hall', 'floor' => 0, 'capacity' => 70, 'exam_capacity' => 35, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => true, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'Ι15', 'type' => 'lecture_hall', 'floor' => 0, 'capacity' => 40, 'exam_capacity' => 20, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'I10A', 'type' => 'research_lab', 'floor' => 0, 'capacity' => 15, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'I10B', 'type' => 'research_lab', 'floor' => 0, 'capacity' => 15, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            
            // Κάτω Κτίριο (DIT Dedicated) - 1st Floor
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'A9', 'type' => 'research_lab', 'floor' => 1, 'capacity' => 15, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'A10', 'type' => 'research_lab', 'floor' => 1, 'capacity' => 20, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'A11', 'type' => 'research_lab', 'floor' => 1, 'capacity' => 15, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'A12', 'type' => 'research_lab', 'floor' => 1, 'capacity' => 15, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'A13', 'type' => 'research_lab', 'floor' => 1, 'capacity' => 15, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'A14', 'type' => 'research_lab', 'floor' => 1, 'capacity' => 20, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'A8', 'type' => 'conference_room', 'floor' => 1, 'capacity' => 12, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $katoKtirioId, 'department_id' => $deptIds['DIT'], 'name' => 'A15', 'type' => 'conference_room', 'floor' => 1, 'capacity' => 12, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],

            // Πάνω Κτίριο (Shared) - Ground Floor
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'Αμφιθέατρο "Αντώνιος Μπουκουβάλας"', 'type' => 'amphitheater', 'floor' => 0, 'capacity' => 250, 'exam_capacity' => 125, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => true, 'has_wheelchair_desks' => true],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'B0.17', 'type' => 'conference_room', 'floor' => 0, 'capacity' => 30, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'B0.18', 'type' => 'computer_lab', 'floor' => 0, 'capacity' => 12, 'exam_capacity' => 12, 'has_projector' => false, 'computer_count' => 12, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            
            // Πάνω Κτίριο (Shared) - 1st Floor
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A1.1', 'type' => 'lecture_hall', 'floor' => 1, 'capacity' => 51, 'exam_capacity' => 25, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A1.2', 'type' => 'computer_lab', 'floor' => 1, 'capacity' => 35, 'exam_capacity' => 35, 'has_projector' => true, 'computer_count' => 35, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A1.3', 'type' => 'lecture_hall', 'floor' => 1, 'capacity' => 132, 'exam_capacity' => 66, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => true, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A1.4', 'type' => 'lecture_hall', 'floor' => 1, 'capacity' => 72, 'exam_capacity' => 36, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            
            // Πάνω Κτίριο (Shared) - 2nd Floor
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A2.1', 'type' => 'computer_lab', 'floor' => 2, 'capacity' => 18, 'exam_capacity' => 18, 'has_projector' => false, 'computer_count' => 18, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A2.3', 'type' => 'research_lab', 'floor' => 2, 'capacity' => 20, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A2.4', 'type' => 'lecture_hall', 'floor' => 2, 'capacity' => 138, 'exam_capacity' => 69, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => true, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A2.5', 'type' => 'lecture_hall', 'floor' => 2, 'capacity' => 126, 'exam_capacity' => 63, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            
            // Πάνω Κτίριο (Shared) - 3rd Floor
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A3.1', 'type' => 'computer_lab', 'floor' => 3, 'capacity' => 48, 'exam_capacity' => 24, 'has_projector' => true, 'computer_count' => 15, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A3.2', 'type' => 'lecture_hall', 'floor' => 3, 'capacity' => 48, 'exam_capacity' => 24, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A3.3', 'type' => 'lecture_hall', 'floor' => 3, 'capacity' => 114, 'exam_capacity' => 57, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A3.4', 'type' => 'research_lab', 'floor' => 3, 'capacity' => 15, 'exam_capacity' => 0, 'has_projector' => false, 'computer_count' => 0, 'hvac_type' => 'none', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
            ['building_id' => $panoKtirioId, 'department_id' => null, 'name' => 'A3.5', 'type' => 'lecture_hall', 'floor' => 3, 'capacity' => 87, 'exam_capacity' => 43, 'has_projector' => true, 'computer_count' => 0, 'hvac_type' => 'ac', 'is_wheelchair_accessible' => false, 'has_wheelchair_desks' => false],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->updateOrInsert(
                ['building_id' => $room['building_id'], 'name' => $room['name']],
                [
                    'department_id' => $room['department_id'],
                    'type' => $room['type'],
                    'floor' => $room['floor'],
                    'capacity' => $room['capacity'],
                    'exam_capacity' => $room['exam_capacity'],
                    'has_projector' => $room['has_projector'],
                    'computer_count' => $room['computer_count'],
                    'hvac_type' => $room['hvac_type'],
                    'is_wheelchair_accessible' => $room['is_wheelchair_accessible'],
                    'has_wheelchair_desks' => $room['has_wheelchair_desks'],
                    'updated_at' => $now
                ]
            );
        }

        // Users
        $users = [
            ['email' => 'admin@uop.gr', 'role_id' => $roleIds['admin'], 'department_id' => null, 'first_name' => 'System', 'last_name' => 'Administrator'],
            ['email' => 'sec.dit@uop.gr', 'role_id' => $roleIds['secretary'], 'department_id' => $deptIds['DIT'], 'first_name' => 'Γραμματεία', 'last_name' => 'Πληροφορικής'],
            ['email' => 'sec.econ@uop.gr', 'role_id' => $roleIds['secretary'], 'department_id' => $deptIds['ECON'], 'first_name' => 'Γραμματεία', 'last_name' => 'Οικονομικών'],
            ['email' => 'tryfonopoulos@uop.gr', 'role_id' => $roleIds['professor'], 'department_id' => $deptIds['DIT'], 'first_name' => 'Χρήστος', 'last_name' => 'Τρυφωνόπουλος'],
            ['email' => 'skiadopoulos@uop.gr', 'role_id' => $roleIds['professor'], 'department_id' => $deptIds['DIT'], 'first_name' => 'Σπύρος', 'last_name' => 'Σκιαδόπουλος'],
            ['email' => 'vassilakis@uop.gr', 'role_id' => $roleIds['professor'], 'department_id' => $deptIds['DIT'], 'first_name' => 'Κώστας', 'last_name' => 'Βασιλάκης'],
            ['email' => 'lepouras@uop.gr', 'role_id' => $roleIds['professor'], 'department_id' => $deptIds['DIT'], 'first_name' => 'Γιώργος', 'last_name' => 'Λέπουρας'],
            ['email' => 'student@go.uop.gr', 'role_id' => $roleIds['student'], 'department_id' => $deptIds['DIT'], 'first_name' => 'Φοιτητής', 'last_name' => 'Δοκιμών'],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                [
                    'role_id' => $user['role_id'],
                    'department_id' => $user['department_id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'password' => $password,
                    'updated_at' => $now
                ]
            );
        }
    }
}