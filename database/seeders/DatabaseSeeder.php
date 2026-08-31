<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\mUser;
use App\Models\mDriver;
use App\Models\mVehicle;
use App\Models\mDepartment;
use App\Models\trDutyTrip;
use App\Models\trDutyTrip_Details;
use App\Models\trDutyTrip_Documentations;
use App\Models\dtLocationTracking;
use App\Models\logTripStatus;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin Users (HC Team)
        mUser::create([
            'txtUserName' => 'Admin HC Kalbe',
            'txtEmail' => 'admin@kmi.kalbe.co.id',
            'txtPassword' => Hash::make('admin123'),
            'txtRole' => 'ADMIN_HC',
            'txtPhoneNumber' => '08119876543',
            'txtInsertedBy' => 'SYSTEM_INIT',
            'dtmInserted' => now(),
            'bitActive' => 1,
        ]);

        mUser::create([
            'txtUserName' => 'Human Capital Officer',
            'txtEmail' => 'hc.officer@kmi.kalbe.co.id',
            'txtPassword' => Hash::make('admin123'),
            'txtRole' => 'ADMIN_HC',
            'txtPhoneNumber' => '08128765432',
            'txtInsertedBy' => 'SYSTEM_INIT',
            'dtmInserted' => now(),
            'bitActive' => 1,
        ]);

        // 2. Seed Master Departments
        $departments = [
            ['txtDepartmentName' => 'Human Capital (HC)', 'txtDepartmentCode' => 'HC'],
            ['txtDepartmentName' => 'Production & Manufacturing', 'txtDepartmentCode' => 'PROD'],
            ['txtDepartmentName' => 'Quality Assurance & Control (QA/QC)', 'txtDepartmentCode' => 'QAQC'],
            ['txtDepartmentName' => 'Engineering & Maintenance', 'txtDepartmentCode' => 'ENG'],
            ['txtDepartmentName' => 'Supply Chain & Logistics', 'txtDepartmentCode' => 'SCM'],
            ['txtDepartmentName' => 'Research & Development (R&D)', 'txtDepartmentCode' => 'RND'],
            ['txtDepartmentName' => 'Finance & Accounting', 'txtDepartmentCode' => 'FA'],
            ['txtDepartmentName' => 'Sales & Marketing', 'txtDepartmentCode' => 'MKT'],
            ['txtDepartmentName' => 'Production Planning & Inventory Control (PPIC)', 'txtDepartmentCode' => 'PPIC'],
        ];

        foreach ($departments as $dept) {
            mDepartment::create([
                'txtDepartmentName' => $dept['txtDepartmentName'],
                'txtDepartmentCode' => $dept['txtDepartmentCode'],
                'txtInsertedBy' => 'SYSTEM_INIT',
                'dtmInserted' => now(),
                'bitActive' => 1,
            ]);
        }

        // 3. Seed Master Vehicles
        $vehicles = [
            [
                'txtVehicleName' => 'Innova Zenix V Silver',
                'txtPlateNumber' => 'B 1024 KMI',
                'txtBrandModel' => 'Toyota Innova Zenix 2.0 V CVT',
                'txtVehicleType' => 'MPV',
                'intMaxSeat' => 7,
                'intCurrentOdometer' => 24850,
                'txtFuelType' => 'Pertamax',
                'txtStatus' => 'IN_USE',
            ],
            [
                'txtVehicleName' => 'Avanza G Hitam',
                'txtPlateNumber' => 'B 1588 KMI',
                'txtBrandModel' => 'Toyota Avanza 1.5 G CVT',
                'txtVehicleType' => 'MPV',
                'intMaxSeat' => 7,
                'intCurrentOdometer' => 45200,
                'txtFuelType' => 'Pertalite',
                'txtStatus' => 'AVAILABLE',
            ],
            [
                'txtVehicleName' => 'HiAce Commuter Putih',
                'txtPlateNumber' => 'B 7099 KMI',
                'txtBrandModel' => 'Toyota HiAce Commuter Manual',
                'txtVehicleType' => 'Minibus',
                'intMaxSeat' => 14,
                'intCurrentOdometer' => 78400,
                'txtFuelType' => 'Dexlite',
                'txtStatus' => 'AVAILABLE',
            ],
            [
                'txtVehicleName' => 'Pajero Sport Dakar Hitam',
                'txtPlateNumber' => 'B 1999 KMI',
                'txtBrandModel' => 'Mitsubishi Pajero Sport Dakar 4x2',
                'txtVehicleType' => 'SUV',
                'intMaxSeat' => 7,
                'intCurrentOdometer' => 31600,
                'txtFuelType' => 'Dexlite',
                'txtStatus' => 'AVAILABLE',
            ],
            [
                'txtVehicleName' => 'Gran Max Blind Van',
                'txtPlateNumber' => 'B 9122 KMI',
                'txtBrandModel' => 'Daihatsu Gran Max Blind Van 1.3',
                'txtVehicleType' => 'Van Logistik',
                'intMaxSeat' => 2,
                'intCurrentOdometer' => 92100,
                'txtFuelType' => 'Pertalite',
                'txtStatus' => 'AVAILABLE',
            ],
        ];

        $vehicleMap = [];
        foreach ($vehicles as $v) {
            $created = mVehicle::create([
                'txtVehicleName' => $v['txtVehicleName'],
                'txtPlateNumber' => $v['txtPlateNumber'],
                'txtBrandModel' => $v['txtBrandModel'],
                'txtVehicleType' => $v['txtVehicleType'],
                'intMaxSeat' => $v['intMaxSeat'],
                'intCurrentOdometer' => $v['intCurrentOdometer'],
                'txtFuelType' => $v['txtFuelType'],
                'txtStatus' => $v['txtStatus'],
                'txtInsertedBy' => 'SYSTEM_INIT',
                'dtmInserted' => now(),
                'bitActive' => 1,
            ]);
            $vehicleMap[$v['txtPlateNumber']] = $created;
        }

        // 4. Seed Master Drivers
        $drivers = [
            [
                'txtDriverName' => 'Pak Joko Santoso',
                'txtPhoneNumber' => '081234567890',
                'txtLicenseNumber' => 'SIM-A-928172910',
                'txtEmail' => 'joko.santoso@kmi.kalbe.co.id',
                'txtPassword' => Hash::make('driver123'),
                'txtStatus' => 'ON_DUTY',
            ],
            [
                'txtDriverName' => 'Pak Budi Prasetyo',
                'txtPhoneNumber' => '081398765432',
                'txtLicenseNumber' => 'SIM-B1-182736450',
                'txtEmail' => 'budi.prasetyo@kmi.kalbe.co.id',
                'txtPassword' => Hash::make('driver123'),
                'txtStatus' => 'AVAILABLE',
            ],
            [
                'txtDriverName' => 'Pak Agus Setiawan',
                'txtPhoneNumber' => '081567890123',
                'txtLicenseNumber' => 'SIM-A-564738291',
                'txtEmail' => 'agus.setiawan@kmi.kalbe.co.id',
                'txtPassword' => Hash::make('driver123'),
                'txtStatus' => 'AVAILABLE',
            ],
            [
                'txtDriverName' => 'Pak Hendra Kurniawan',
                'txtPhoneNumber' => '081723456789',
                'txtLicenseNumber' => 'SIM-A-738291029',
                'txtEmail' => 'hendra.kurniawan@kmi.kalbe.co.id',
                'txtPassword' => Hash::make('driver123'),
                'txtStatus' => 'AVAILABLE',
            ],
        ];

        $driverMap = [];
        foreach ($drivers as $d) {
            $created = mDriver::create([
                'txtDriverName' => $d['txtDriverName'],
                'txtPhoneNumber' => $d['txtPhoneNumber'],
                'txtLicenseNumber' => $d['txtLicenseNumber'],
                'txtEmail' => $d['txtEmail'],
                'txtPassword' => $d['txtPassword'],
                'txtStatus' => $d['txtStatus'],
                'txtInsertedBy' => 'SYSTEM_INIT',
                'dtmInserted' => now(),
                'bitActive' => 1,
            ]);
            $driverMap[$d['txtEmail']] = $created;
        }

        // 5. Seed an Active Trip (In Progress with Live Telemetry Tracking)
        $innova = $vehicleMap['B 1024 KMI'];
        $joko = $driverMap['joko.santoso@kmi.kalbe.co.id'];

        $activeTrip = trDutyTrip::create([
            'txtTripCode' => 'TRIP-' . date('Ymd') . '-001',
            'intVehicle_ID' => $innova->intVehicle_ID,
            'intDriver_ID' => $joko->intDriver_ID,
            'dtmTripDate' => now()->toDateString(),
            'dtmDepartureTime' => now()->subHours(2),
            'txtDestination' => 'PT Kalbe Farma Tbk (Cikarang Plant)',
            'txtPurpose' => 'Audit Mutu ISO & Kunjungan Lapangan QA/QC',
            'txtTripStatus' => 'IN_PROGRESS',
            'intStartOdometer' => 24800,
            'floatTotalFuelCost' => 250000,
            'floatTotalFuelLiters' => 19.23,
            'txtNotes' => 'Berangkat dari Kantor Pusat KMI Jakarta menuju Pabrik Cikarang via Tol Jakarta-Cikampek.',
            'txtInsertedBy' => 'ADMIN_HC',
            'dtmInserted' => now()->subHours(3),
        ]);

        // Passengers in active trip
        $passengers = [
            ['txtEmployeeName' => 'Andi Pratama', 'txtEmployeeNIK' => 'KMI-2021-042', 'txtDepartment' => 'Quality Assurance & Control (QA/QC)', 'txtPhoneNumber' => '081299887766', 'txtPurpose' => 'Lead Auditor Verifikasi Bahan Baku'],
            ['txtEmployeeName' => 'Dewi Sartika', 'txtEmployeeNIK' => 'KMI-2022-108', 'txtDepartment' => 'Quality Assurance & Control (QA/QC)', 'txtPhoneNumber' => '081388776655', 'txtPurpose' => 'Sampling Lab Mikrobiologi'],
            ['txtEmployeeName' => 'Rian Hidayat', 'txtEmployeeNIK' => 'KMI-2023-019', 'txtDepartment' => 'Production & Manufacturing', 'txtPhoneNumber' => '081577665544', 'txtPurpose' => 'Sinkronisasi Line Packaging 4'],
            ['txtEmployeeName' => 'Siti Nurhaliza', 'txtEmployeeNIK' => 'KMI-2024-055', 'txtDepartment' => 'Engineering & Maintenance', 'txtPhoneNumber' => '081766554433', 'txtPurpose' => 'Inspeksi Mesin Filling Aseptic'],
        ];

        foreach ($passengers as $p) {
            trDutyTrip_Details::create([
                'intDutyTrip_ID' => $activeTrip->intDutyTrip_ID,
                'txtEmployeeName' => $p['txtEmployeeName'],
                'txtEmployeeNIK' => $p['txtEmployeeNIK'],
                'txtDepartment' => $p['txtDepartment'],
                'txtPhoneNumber' => $p['txtPhoneNumber'],
                'dtmTripDate' => now()->toDateString(),
                'intRequestedVehicle_ID' => $innova->intVehicle_ID,
                'txtDestination' => 'PT Kalbe Farma Tbk (Cikarang Plant)',
                'txtPurpose' => $p['txtPurpose'],
                'txtBookingStatus' => 'ASSIGNED',
                'txtInsertedBy' => 'EMPLOYEE',
                'dtmInserted' => now()->subHours(4),
            ]);
        }

        // Checkpoint Documentations
        trDutyTrip_Documentations::create([
            'intDutyTrip_ID' => $activeTrip->intDutyTrip_ID,
            'intDriver_ID' => $joko->intDriver_ID,
            'txtCategory' => 'SEBELUM_BERANGKAT',
            'txtPhotoPath' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=800&auto=format&fit=crop&q=80',
            'intOdometer' => 24800,
            'floatLatitude' => -6.155823,
            'floatLongitude' => 106.883712,
            'txtLocationName' => 'Head Office Kalbe Nutritionals Jakarta',
            'txtNotes' => 'Pengecekan fisik kendaraan, kondisi ban, rem, dan interior bersih. Odometer awal 24.800 KM.',
            'txtInsertedBy' => 'DRIVER',
            'dtmInserted' => now()->subHours(2),
        ]);

        trDutyTrip_Documentations::create([
            'intDutyTrip_ID' => $activeTrip->intDutyTrip_ID,
            'intDriver_ID' => $joko->intDriver_ID,
            'txtCategory' => 'ISI_BBM',
            'txtPhotoPath' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=80',
            'intOdometer' => 24825,
            'floatFuelLiters' => 19.23,
            'floatFuelCost' => 250000,
            'floatLatitude' => -6.241512,
            'floatLongitude' => 106.992145,
            'txtLocationName' => 'SPBU Pertamina Rest Area KM 19 Tol Jakarta-Cikampek',
            'txtNotes' => 'Pengisian Pertamax Rp 250.000 (19.23 Liter). Struk terlampir.',
            'txtInsertedBy' => 'DRIVER',
            'dtmInserted' => now()->subHour(),
        ]);

        // Seed Realistic GPS Telemetry Trail (Live Location breadcrumbs from Jakarta towards Cikarang)
        $telemetryPoints = [
            ['-6.155823', '106.883712', 0, 110, 120],  // HO Kalbe
            ['-6.172109', '106.901540', 35, 125, 100], // Kelapa Gading toll
            ['-6.208450', '106.938720', 65, 115, 80],  // Tol JORR Cakung
            ['-6.234120', '106.975410', 78, 105, 60],  // Bekasi Barat
            ['-6.241512', '106.992145', 0, 95, 45],    // Rest Area KM 19 (Isi BBM)
            ['-6.275890', '107.085120', 82, 100, 25],  // Cibitung
            ['-6.312450', '107.143210', 74, 98, 10],   // Cikarang Barat Tol Exit
            ['-6.331200', '107.158900', 42, 140, 2],   // Menuju Kawasan Industri Cikarang (Current Live Position)
        ];

        foreach ($telemetryPoints as $index => $pt) {
            dtLocationTracking::create([
                'intDutyTrip_ID' => $activeTrip->intDutyTrip_ID,
                'intDriver_ID' => $joko->intDriver_ID,
                'floatLatitude' => (float)$pt[0],
                'floatLongitude' => (float)$pt[1],
                'floatSpeed' => (float)$pt[2],
                'floatHeading' => (float)$pt[3],
                'floatAccuracy' => 5.2,
                'dtmTracked' => now()->subMinutes($pt[4]),
            ]);
        }

        // Status Logs
        logTripStatus::create([
            'intDutyTrip_ID' => $activeTrip->intDutyTrip_ID,
            'txtPreviousStatus' => null,
            'txtNewStatus' => 'SCHEDULED',
            'txtActionNotes' => 'Jadwal dinas dibuat oleh Admin HC, menugaskan Pak Joko Santoso & Innova Zenix B 1024 KMI.',
            'txtInsertedBy' => 'ADMIN_HC',
            'dtmInserted' => now()->subHours(3),
        ]);

        logTripStatus::create([
            'intDutyTrip_ID' => $activeTrip->intDutyTrip_ID,
            'txtPreviousStatus' => 'SCHEDULED',
            'txtNewStatus' => 'IN_PROGRESS',
            'txtActionNotes' => 'Driver memulai perjalanan dari HO Jakarta dan upload foto cek kendaraan.',
            'txtInsertedBy' => 'DRIVER',
            'dtmInserted' => now()->subHours(2),
        ]);

        // 6. Seed an Upcoming Scheduled Trip
        $avanza = $vehicleMap['B 1588 KMI'];
        $budi = $driverMap['budi.prasetyo@kmi.kalbe.co.id'];

        $scheduledTrip = trDutyTrip::create([
            'txtTripCode' => 'TRIP-' . date('Ymd', strtotime('+1 day')) . '-002',
            'intVehicle_ID' => $avanza->intVehicle_ID,
            'intDriver_ID' => $budi->intDriver_ID,
            'dtmTripDate' => now()->addDay()->toDateString(),
            'dtmDepartureTime' => now()->addDay()->setHour(8)->setMinute(0),
            'txtDestination' => 'BPOM RI Percetakan Negara Jakarta Pusat',
            'txtPurpose' => 'Penyerahan Berkas Registrasi Produk Baru Nutrisi',
            'txtTripStatus' => 'SCHEDULED',
            'intStartOdometer' => 45200,
            'txtNotes' => 'Dinas tim Regulatory Affairs & HC.',
            'txtInsertedBy' => 'ADMIN_HC',
            'dtmInserted' => now()->subHours(1),
        ]);

        trDutyTrip_Details::create([
            'intDutyTrip_ID' => $scheduledTrip->intDutyTrip_ID,
            'txtEmployeeName' => 'Bambang Irawan',
            'txtEmployeeNIK' => 'KMI-2020-011',
            'txtDepartment' => 'Research & Development (R&D)',
            'txtPhoneNumber' => '081211223344',
            'dtmTripDate' => now()->addDay()->toDateString(),
            'intRequestedVehicle_ID' => $avanza->intVehicle_ID,
            'txtDestination' => 'BPOM RI Percetakan Negara Jakarta Pusat',
            'txtPurpose' => 'Pengurusan Izin Edar Produk Baru Nutrisi Anak',
            'txtBookingStatus' => 'ASSIGNED',
            'txtInsertedBy' => 'EMPLOYEE',
            'dtmInserted' => now()->subHours(2),
        ]);

        trDutyTrip_Details::create([
            'intDutyTrip_ID' => $scheduledTrip->intDutyTrip_ID,
            'txtEmployeeName' => 'Fitri Anggraini',
            'txtEmployeeNIK' => 'KMI-2023-089',
            'txtDepartment' => 'Human Capital (HC)',
            'txtPhoneNumber' => '081322334455',
            'dtmTripDate' => now()->addDay()->toDateString(),
            'intRequestedVehicle_ID' => $avanza->intVehicle_ID,
            'txtDestination' => 'BPOM RI Percetakan Negara Jakarta Pusat',
            'txtPurpose' => 'Pendampingan Konsultasi Regulasi',
            'txtBookingStatus' => 'ASSIGNED',
            'txtInsertedBy' => 'EMPLOYEE',
            'dtmInserted' => now()->subHours(1),
        ]);

        // 7. Seed Pending Employee Booking Submissions (Waiting for Driver & Schedule assignment)
        trDutyTrip_Details::create([
            'intDutyTrip_ID' => null,
            'txtEmployeeName' => 'Gunawan Wibisono',
            'txtEmployeeNIK' => 'KMI-2022-077',
            'txtDepartment' => 'Supply Chain & Logistics',
            'txtPhoneNumber' => '081987654321',
            'dtmTripDate' => now()->addDays(2)->toDateString(),
            'intRequestedVehicle_ID' => $vehicleMap['B 7099 KMI']->intVehicle_ID, // HiAce
            'txtDestination' => 'Warehouse Kalbe Cikande Serang',
            'txtPurpose' => 'Stock Opname Semesteran & Kunjungan Gudang Bahan Baku',
            'txtNotes' => 'Rombongan tim logistik 6 orang.',
            'txtBookingStatus' => 'PENDING',
            'txtInsertedBy' => 'EMPLOYEE',
            'dtmInserted' => now()->subMinutes(30),
        ]);
    }
}
