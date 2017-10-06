<?php

namespace App\Modules\Template\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

use App\Modules\Template\Models\Template as TemplateModel;
use App\Modules\Website\Models\Website as WebsiteModel;

use Theme;
use Auth;
use Activity;
use Debugbar;
use File;


class TemplateWeddingController extends Controller
{
    protected $_data                                = array();

    public function __construct()
    {
        $this->_data['string_menuname']             = 'Template';
    }



    public function showTemplate($WebsiteID){
        $this->_data['state']                       = 'add';
        $this->_data['string_active_menu']          = 'Template';
        $Website                                    = WebsiteModel::find($WebsiteID);

        $this->_data['WebsiteID']                   = $WebsiteID;
        $this->_data['Website']                     = $Website;
        return Theme::view('modules.template.template.show',$this->_data);
    }

    public function loadTemplate($TemplateID)
    {
        $Users                                          = Auth::user();
        $LoadTemplate                                   = TemplateModel::find($TemplateID);

        $this->_data['ImageTemplate']                    = $LoadTemplate;

        return Theme::view('modules.template.template.load_template', $this->_data);
    }

    public function actionTemplate (Request $request){

        $validator = Validator::make($request->all(), [
            'template' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }

        $WebsiteID                                  = $request->website_id;
        $Website                                    = WebsiteModel::find($WebsiteID);
        $Website->template_id                       = $request->template;
        $Website->save();
            return redirect()
                ->route('wedding_finish',$WebsiteID);
    }
}
