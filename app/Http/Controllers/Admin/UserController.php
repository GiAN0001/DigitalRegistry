<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role; // NEW IMPORT
use Illuminate\Support\Facades\Hash; // NEW IMPORT
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
   
    public function index(): View
    {

        $totalUsers = User::where('status', 1)->count();
        $totalInactiveUsers = User::where('status', 0)->count();
        $totalAdmins = User::role('Admin')->count();
        $totalStaff = User::role('Staff')->count();
        $totalHelpDesk = User::role('help desk')->count();
        
        // Fetch ALL users, eagerly loading roles and job titles.
        $users = User::with('barangayRole') 
            ->latest()
            ->paginate(10);
            
        // We handle the "cannot delete self" logic in the view (Step 3).
        
        return view('admin.users.index', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'totalInactiveUsers' => $totalInactiveUsers,
            'totalAdmins' => $totalAdmins,
            'totalStaff' => $totalStaff,
            'totalHelpDesk' => $totalHelpDesk,
        ]);
        
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request ->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'contact' => 'required|string|max:15|regex:/^(09)\d{9}$/',
            'role' => 'required|string|',
            'barangay_role_id' => ['required', 'integer', Rule::exists('barangay_roles', 'id')],
            'status' => ['required', 'in:0,1'],
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'username' => $request->username,
            'email' => $request->email,
            'contact' => $request->contact,
            'barangay_role_id' => $request->barangay_role_id,
            'status' => $request->status,
            'password' => Hash::make($request->password),
        ]);

            $roleId = $request->role; 
            
         
            $role = Role::findById($roleId); 
            
            // Assign the Role Model object
            $user->assignRole($role);

           
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }
}