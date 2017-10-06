<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Theme;

use Auth;
use App\User;
use App\Modules\Cashbook\Models\CashBookModel;
use App\Modules\Branch\Models\BranchModel;
use App\Modules\Customer\Models\CustomerModel;


class HomeController extends Controller
{
    protected $_data = array();

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Users                                          = Auth::user();
        
        return Theme::view('home',$this->_data);
    }
}
