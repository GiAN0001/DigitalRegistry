<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangayRole;
use App\Models\AreaStreet;
use App\Models\DocumentPurpose;
use App\Models\DocumentType;
use App\Models\Equipment;
use App\Models\Facility;
use App\Models\houseHoldRole;
use App\Models\HouseStructure;
use App\Models\petType;
use App\Models\ResidencyType;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function lookup()
    {
        $barangayRoles = BarangayRole::all();
        $areaStreets = AreaStreet::all();
        $documentPurposes = DocumentPurpose::all();
        $documentTypes = DocumentType::all();
        $equipments = Equipment::all();
        $facilities = Facility::all();
        $householdRoles = houseHoldRole ::all();
        $houseStructures = HouseStructure::all();
        $petTypes = petType::all();
        $residencyTypes = ResidencyType::all();

        return view('admin.settings.lookup.index', compact('barangayRoles', 'areaStreets', 'documentPurposes', 'documentTypes', 'equipments', 'facilities', 'householdRoles', 'houseStructures', 'petTypes', 'residencyTypes'));
    } 
}