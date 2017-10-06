<?php

namespace App\Modules\Website\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Modules\Website\Models\Website as WebsiteModel;

use Theme;
use Auth;
use Activity;
use Debugbar;

class FinishWebsiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:website-menu');

        $this->_data['string_menuname']             = 'Website';
    }

    public function showFinish($WebsiteID){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Finish';
        $Website                                    = WebsiteModel::find($WebsiteID);
        $this->_data['Website']                     = $Website;
        $this->_data['WebsiteID']                   = $WebsiteID;

        return Theme::view('modules.website.finish',$this->_data);
    }

}
