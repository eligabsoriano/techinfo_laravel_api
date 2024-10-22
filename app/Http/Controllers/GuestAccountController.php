<?php

namespace App\Http\Controllers;

use App\Models\Gpus;
use App\Models\Hdds;
use App\Models\Rams;
use App\Models\Ssds;
use App\Models\CpuCoolers;
use App\Models\GuestBuild;
use App\Models\Processors;
use App\Models\GuestAccount;
use App\Models\Motherboards;
use Illuminate\Http\Request;
use App\Models\ComputerCases;
use App\Models\PowerSupplyUnits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class GuestAccountController extends Controller
{
    public function createGuestAccount()
    {
        // Automatically generate a random name for the guest account
        $guestName = 'Guest_' . uniqid();

        try {
            // Create the guest account in the database
            $guestAccount = GuestAccount::create([
                'name' => $guestName,
            ]);

            return response()->json([
                'message' => 'Guest account created successfully.',
                'guest_account' => $guestAccount
            ]);
        } catch (Exception $e) {
            // Handle any errors during account creation
            return response()->json(['message' => 'Failed to create guest account.', 'error' => $e->getMessage()], 500);
        }
    }

    public function createGuestBuild(Request $request)
    {
        try {
            // Validate guest name
            $guestName = $request->input('guest_name');
            if (!$guestName) {
                return response()->json(['message' => 'Guest name is required.'], 400);
            }

            // Find the guest account by name
            $guestAccount = GuestAccount::where('name', $guestName)->first();
            if (!$guestAccount) {
                return response()->json(['message' => 'Guest account not found.'], 404);
            }

            // Fetch component IDs based on provided names
            $processorId = Processors::where('processor_name', $request->input('processor_name'))->value('processor_id');
            $motherboardId = Motherboards::where('motherboard_name', $request->input('motherboard_name'))->value('motherboard_id');
            $gpuId = Gpus::where('gpu_name', $request->input('gpu_name'))->value('gpu_id');
            $ramId = Rams::where('ram_name', $request->input('ram_name'))->value('ram_id');
            $ssdId = Ssds::where('ssd_name', $request->input('ssd_name'))->value('ssd_id') ?? null;
            $hddId = Hdds::where('hdd_name', $request->input('hdd_name'))->value('hdd_id') ?? null;
            $cpuCoolerId = CpuCoolers::where('cooler_name', $request->input('cooler_name'))->value('cooler_id');
            $caseId = ComputerCases::where('case_name', $request->input('case_name'))->value('case_id');
            $psuId = PowerSupplyUnits::where('psu_name', $request->input('psu_name'))->value('psu_id');

            // Check if any required fields are missing
            if (!$processorId || !$motherboardId || !$gpuId || !$ramId || !$cpuCoolerId || !$caseId || !$psuId) {
                return response()->json(['message' => 'One or more required components are missing.'], 400);
            }

            // Create a new build for the guest account
            $guestBuild = GuestBuild::create([
                'guest_name' => $guestAccount->name,
                'build_name' => $request->input('build_name'),
                'processor_id' => $processorId,
                'motherboard_id' => $motherboardId,
                'gpu_id' => $gpuId,
                'ram_id' => $ramId,
                'ssd_id' => $ssdId,
                'hdd_id' => $hddId,
                'cooler_id' => $cpuCoolerId,
                'case_id' => $caseId,
                'psu_id' => $psuId,
            ]);

            // Fetch the component names for feedback
            $feedback = [
                'components' => [
                    'processor' => Processors::where('processor_id', $processorId)->value('processor_name'),
                    'motherboard' => Motherboards::where('motherboard_id', $motherboardId)->value('motherboard_name'),
                    'ram' => Rams::where('ram_id', $ramId)->value('ram_name'),
                    'gpu' => Gpus::where('gpu_id', $gpuId)->value('gpu_name'),
                    'psu' => PowerSupplyUnits::where('psu_id', $psuId)->value('psu_name'),
                    'case' => ComputerCases::where('case_id', $caseId)->value('case_name'),
                    'cooler' => CpuCoolers::where('cooler_id', $cpuCoolerId)->value('cooler_name'),
                    'hdd' => $hddId ? Hdds::where('hdd_id', $hddId)->value('hdd_name') : null,
                    'ssd' => $ssdId ? Ssds::where('ssd_id', $ssdId)->value('ssd_name') : null,
                ],
                'build_id' => $guestBuild->id,
            ];

            return response()->json([
                'message' => 'Build created successfully.',
                'guest_build' => $guestBuild,
                'feedback' => $feedback,
            ]);

        } catch (ModelNotFoundException $e) {
            // Handle case where a model is not found
            return response()->json(['message' => 'Component not found.', 'error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            // Handle any other exceptions
            return response()->json(['message' => 'Failed to create build.', 'error' => $e->getMessage()], 500);
        }
    }
}
