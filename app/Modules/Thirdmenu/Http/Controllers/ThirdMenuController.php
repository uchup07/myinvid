<?php

namespace App\Modules\Thirdmenu\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Validator;
use App\Modules\Menu\Models\Menu as MenuModel;
use App\Modules\Submenu\Models\Submenu as SubmenuModel;
use App\Modules\Thirdmenu\Models\Thirdmenu as ThirdmenuModel;
use App\Modules\Permission\Models\Permission as Permission;
use Auth;
use Theme;

class ThirdMenuController extends Controller
{
    protected $_data = array();
    public function __construct()
    {
        $this->middleware(['permission:thirdmenu-view']);
        $this->middleware('permission:thirdmenu-add')->only('add');
        $this->middleware('permission:thirdmenu-edit')->only('edit');
        $this->middleware('permission:thirdmenu-delete')->only('delete');

        $this->_data['string_menuname']             = 'Third Menu';
        $this->_data['IDMENU']                      = 'Thirdmenu';
    }

    public function index(){
        $this->_data['string_active_menu']          = 'List';
        $this->_data['datatables']                  = 'thirdmenu';
        $this->_data['IDSUBMENU']                   = 'ListThirdmenu';

        return Theme::view('modules.thirdmenu.show',$this->_data);
    }

    public function show(){
        $this->_data['string_active_menu']          = 'List';
        $this->_data['datatables']                  = 'thirdmenu';
        $this->_data['IDSUBMENU']                   = 'ListThirdmenu';

        return Theme::view('modules.thirdmenu.show',$this->_data);
    }

    public function datatables(){
        $Thirdmenu = ThirdmenuModel::join('submenus','thirdmenus.submenu_id', '=', 'submenus.id')
                            ->join('menus','submenus.parent_id','=','menus.id')
                            ->select(['thirdmenus.id', 'menus.name as menu', 'submenus.name as submenu', 'thirdmenus.name', 'thirdmenus.url', 'thirdmenus.permission', 'thirdmenus.icon']);

        return Datatables::of($Thirdmenu)
            ->addColumn('href', function ($Thirdmenu) {
                return '<a href="'.route('thirdmenu_edit',$Thirdmenu->id).'" class="btn btn-info"><i class="glyphicon glyphicon-pencil"></i></a>&nbsp;&nbsp;
                        <a href="javascript:void(0);" class="btn btn-danger" onclick="deleteList('.$Thirdmenu->id.')"><i class="glyphicon glyphicon-trash"></i></a>';
            })
            ->editColumn('icon', '<i class="{{$icon}}"></i>')
            ->rawColumns(['href','icon'])
            ->make(true);
    }

    public function add(){
        $this->_data['state']                   = 'add';
        $this->_data['string_active_menu']      = 'Add/Edit';
        $this->_data['IDSUBMENU']               = 'AddThirdmenu';

        $Permission                             = Permission::all();
        $Menu                                   = MenuModel::all();
        $this->_data['Permissions']             = $Permission;
        $this->_data['Menus']                   = $Menu;
        return Theme::view('modules.thirdmenu.form',$this->_data);
    }

    public function edit(Request $request){
        $this->_data['state']                   = 'edit';
        $this->_data['string_active_menu']      = 'Add/Edit';
        $this->_data['IDSUBMENU']               = 'AddThirdmenu';

        $this->_data['id']                      = $request->id;
        $Permission                             = Permission::all();
        $Thirdmenu                              = ThirdmenuModel::find($request->id);

        $arr_Menu                               = MenuModel::all();
        $this->_data['Parents']                 = $arr_Menu;
        $this->_data['Permissions']             = $Permission;
        $this->_data['Thirdmenu']               = $Thirdmenu;

        return Theme::view('modules.thirdmenu.form',$this->_data);
    }

    public function post(Request $request){
        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'menu'              => 'required',
            'submenu'           => 'required',
            'url'               => 'required',
            'permission'        => 'required'
        ]);

        $Permission                             = Permission::all();
        $this->_data['Permissions']             = $Permission;
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }

        $Thirdmenu                                      = new ThirdmenuModel();
        $Thirdmenu->name                                = $request->name;
        $Thirdmenu->menu_id                             = $request->menu;
        $Thirdmenu->submenu_id                          = $request->submenu;
        $Thirdmenu->url                                 = $request->url;
        $Thirdmenu->permission                          = $request->permission;
        $Thirdmenu->icon                                = $request->icon;

        if($Thirdmenu->save()){
            return redirect()
                ->route('thirdmenu')
                ->with('scsMsg',"Data successfuly saving");
        }
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name'              => 'required',
            'menu'              => 'required',
            'submenu'           => 'required',
            'url'               => 'required',
            'permission'        => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }

        $id                                             = $request->id;
        $Thirdmenu                                      = ThirdmenuModel::find($request->id);
        $Thirdmenu->name                                = $request->name;
        $Thirdmenu->menu_id                             = $request->menu;
        $Thirdmenu->submenu_id                          = $request->submenu;
        $Thirdmenu->url                                 = $request->url;
        $Thirdmenu->permission                          = $request->permission;
        $Thirdmenu->icon                                = $request->icon;

        if($Thirdmenu->save()){
            return redirect()
                ->route('thirdmenu')
                ->with('scsMsg',"Data succesfuly update");
        }
    }

    public function delete(Request $request){

        $ThidMenu                                       = ThirdmenuModel::find($request->id);
        if($ThidMenu){
            if($ThidMenu->delete()){
                return redirect()
                    ->route('thirdmenu')
                    ->with('scsMsg',"Data succesfuly deleted");

            }else{
                dd("Error deleted Data Role");
            }
        }
    }




}
