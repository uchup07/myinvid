<?php

### IMAGES ###
if (! function_exists('getPathLogo')) {
    /**
     * @return path
     */
    function getPathLogo()
    {
        return "images/logo/";
    }
}

if (! function_exists('getPathGallery')) {
    /**
     * @return path
     */
    function getPathGallery()
    {
        return "media/gallery/";
    }
}

if (! function_exists('getPathStoryofLove')) {
    /**
     * @return path
     */
    function getPathStoryofLove()
    {
        return "media/storyoflove/";
    }
}

if (! function_exists('getPathWebsiteInformation')) {
    /**
     * @return path
     */
    function getPathWebsiteInformation()
    {
        return "media/web_information/";
    }
}

if (! function_exists('getPathBridesmaid')) {
    /**
     * @return path
     */
    function getPathBridesmaid()
    {
        return "media/bridesmaid/";
    }
}


if (! function_exists('getPathTemplate')) {
    /**
     * @return path
     */
    function getPathTemplate()
    {
        return "media/template/";
    }
}





### =================== GENERATE LINK ==================== ###
if (! function_exists('get_GenerateLinkImage')) {
    /**
     * @param path, image file
     * @return string
     */
    function get_GenerateLinkImage($Path, $File)
    {
        return $Path.$File;
    }
}

if (! function_exists('get_GenerateLinkImageUrl')) {
    /**
     * @param path, image file
     * @return string
     */
    function get_GenerateLinkImageUrl($Path, $File)
    {
        return url('/')."/".$Path.$File;
    }
}

### IMAGES ###

if (! function_exists('get_default_password')) {
    /**
     * @return string
     */
    function get_default_password()
    {
        return "Yourbagspa".date('d');
    }
}

if (! function_exists('get_UsersInformation')) {
    /**
     * @param string
     * @return string
     */
    function get_UsersInformation($field)
    {
        $arr_Users = Auth::user();
        if($arr_Users){
            return $arr_Users->$field;
        }
        return;
    }
}

if (! function_exists('bool_CheckUserRole')) {
    /**
     * @param string
     * @return boolean
     */
    function bool_CheckUserRole($string)
    {
        return Auth::user()->hasRole($string);
    }
}

if (! function_exists('bool_CheckAccessUser')) {
    /**
     * @param string
     * @return boolean
     */
    function bool_CheckAccessUser($string)
    {
        return Auth::user()->can($string);
    }
}


if (! function_exists('get_active_user')) {
    /**
     * @param integer
     * @return string
     */
    function get_active_user($integer)
    {
        if($integer == 1){
            return 'active';
        }else{
            return 'inactive';
        }
    }
}

if (! function_exists('getSettingInfo')) {
    /**
     * @param id,field
     * @return field
     */
    function getSettingInfo($id,$Field)
    {
        $Setting                = \App\Modules\Setting\Models\Setting::find($id);
        if($Setting){
            return $Setting->$Field;
        }
        return;
    }
}

if (! function_exists('createMenu')) {
    /**
     * @param integer
     * @return string
     */
    function createMenu()
    {
        $arr_Menu                   = App\Modules\Menu\Models\Menu::orderBy('name')->get();
        $arr_Send                   = array();
        if($arr_Menu){
            $i = 0;
            foreach($arr_Menu as $Menu){
                if(Illuminate\Support\Facades\Auth::user()->can($Menu->permission)){
                    $arr_Send[$i]['id']                 = $Menu->id;
                    $arr_Send[$i]['name']               = $Menu->name;
                    $arr_Send[$i]['url']                = $Menu->url;
                    $arr_Send[$i]['permission']         = $Menu->permission;
                    $arr_Send[$i]['icon']               = $Menu->icon;
                    $arr_Send[$i]['id_menu']            = str_replace(' ', '', $Menu->name );
                    if(App\Modules\Submenu\Models\Submenu::where('parent_id', $Menu->id)->count() > 0){
                        $activeMenu                     = $Menu->url."/*";
                    }else{
                        $activeMenu                     = $Menu->url;
                    }
                    $arr_Send[$i]['active_menu']        = $activeMenu;
                    $arr_Submenu                        = App\Modules\Submenu\Models\Submenu::where('parent_id', $Menu->id)->orderBy('name')->get();
                    if($arr_Submenu){
                        $y = 0;
                        foreach($arr_Submenu as $SubMenu){
                            if(Illuminate\Support\Facades\Auth::user()->can($SubMenu->permission)){
                                $arr_Send[$i]['arrSubMenu'][$y]['id_submenu']   = str_replace(' ', '', $Menu->name );
                                $arr_Send[$i]['arrSubMenu'][$y]['name']         = $SubMenu->name;
                                $arr_Send[$i]['arrSubMenu'][$y]['url']          = $SubMenu->url;
                                $arr_Send[$i]['arrSubMenu'][$y]['icon']         = $SubMenu->icon;
                                $arr_Send[$i]['arrSubMenu'][$y]['permission']   = $SubMenu->permission;
                                $arr_Send[$i]['arrSubMenu'][$y]['parent_id']    = $SubMenu->parent_id;
                                $y = $y+1;
                            }
                        }
                    }
                    $i = $i+1;
                }
            }
        }
        session()->put('Menu',$arr_Send);
    }
}



if (! function_exists('get_UserInformationByID')) {
    /**
     * @param integer
     * @return object
     */
    function get_UserInformationByID($id)
    {
        $obj_User = App\Modules\Users\Models\Users::find($id);
        if($obj_User){
            return $obj_User;
        }
        return;
    }
}

if (! function_exists('get_NameUser')) {
    /**
     * @param integer
     * @return object
     */
    function get_NameUser($id)
    {
        $obj_User = App\User::find($id);
        if($obj_User){
            return $obj_User->name;
        }
        return;
    }
}


if (! function_exists('get_DateNow')) {
    /**
     * @param string
     * @return string
     */
    function get_DateNow($Format)
    {
        return date($Format);
    }
}

if (! function_exists('set_json_encode')) {
    /**
     * @param string
     * @return string
     */
    function set_json_encode($Array)
    {
        return json_encode($Array);
    }
}

if (! function_exists('DateFormat')) {
    /**
     * @param string
     * @return string
     */
    function DateFormat($Date,$Format)
    {
        return date($Format,  strtotime($Date));
    }
}


if (! function_exists('bool_get_useragent_info')) {
    /**
     * @param String
     * @return boolean
     */
    function bool_get_useragent_info($String)
    {

        $Agent          = new \Jenssegers\Agent\Agent();
        return $Agent->$String();
    }
}

if (! function_exists('bool_checkAccessMember')) {
    /**
     * @param String
     * @return boolean
     */
    function bool_checkAccessMember($String)
    {
        $Permission         = \App\Modules\Permission\Models\Permission::Where('name','=',$String)->first();
        $PermissionID       = $Permission->id;

        $Where              = array(
            'user_id'       => \Illuminate\Support\Facades\Auth::id(),
            'permission_id' => $PermissionID
        );
        $PermissionMember   = \App\Modules\Permission\Models\PermissionMember::Where($Where)->count();
        if($PermissionMember > 0){
            return true;
        }
        return;
    }
}

if (! function_exists('get_ListPermissionNormal')) {
    /**
     * @return string
     */
    function get_ListPermissionNormal()
    {
        $Permission                                 = App\Modules\Permission\Models\Permission::all();

        $output = array();
        $output [0] = 'Choose Permission';
        foreach ($Permission as $item){
            $output[$item->id] = $item->display_name;
        }
        return $output;

    }
}

if (! function_exists('get_ListPermission')) {
    /**
     * @return string
     */
    function get_ListPermission()
    {
        $Permission                                 = App\Modules\Permission\Models\Permission::all();

        $output = array();
        $output [0] = 'Choose Permission';
        foreach ($Permission as $item){
            $output[$item->name] = $item->name;
        }
        return $output;

    }
}

if (! function_exists('get_active')) {
    /**
     * @param integer
     * @return string
     */
    function get_active($integer)
    {
        if($integer == 1){
            return 'active';
        }else{
            return 'inactive';
        }
    }
}

if (! function_exists('set_clearFormatRupiah')) {
    /**
     * @param $string
     * @return string
     */
    function set_clearFormatRupiah($string)
    {
        $Result = str_replace('.',"",$string);
        $Result = str_replace('_',"",$Result);
        $Result = str_replace('Rp ',"",$Result);

        return $Result;
    }
}

if (! function_exists('get_ListRole')) {
    /**
     * @return string
     */
    function get_ListRole()
    {
        $Role                                 = App\Modules\Role\Models\Role::all();

        $output = array();
        $output [0] = 'Choose Role';
        foreach ($Role as $item){
            if($item->name != 'super'){
                $output[$item->id] = $item->name;
            }
        }
        return $output;

    }
}

if (! function_exists('set_numberformat')) {
    /**
     * @return number
     */
    function set_numberformat($nominal)
    {
        return number_format($nominal,0,',',".");
    }
}


if (! function_exists('get_date_add')) {
    /**
     * @param integer
     * @return string
     */
    function get_date_add($Date,$interval)
    {
        $stop_date = new DateTime($Date);
        $stop_date->modify('+'.$interval.' day');
        return $stop_date->format('d F Y');
    }
}



if (! function_exists('get_ListMonth')) {
    /**
     * @return string
     */
    function get_ListMonth()
    {

        $output             = array();
        $output [0]         = 'Pilih Bulan';
        $output [1]         = 'Januari';
        $output [2]         = 'Februari';
        $output [3]         = 'Maret';
        $output [4]         = 'April';
        $output [5]         = 'Mei';
        $output [6]         = 'Juni';
        $output [7]         = 'Juli';
        $output [8]         = 'Agustus';
        $output [9]         = 'September';
        $output [10]        = 'Oktober';
        $output [11]        = 'November';
        $output [12]        = 'Desember';
        return $output;

    }
}

if (! function_exists('get_ListMenu')) {
    /**
     * @return string
     */
    function get_ListMenu()
    {
        $Menu                                       = App\Modules\Menu\Models\Menu::orderBy('name')->get();

        $output                                     = array();
        $output [0]                                 = 'Pilih Menu';
        foreach ($Menu as $item){
            $output[$item->id]                    = $item->name;
        }
        return $output;

    }
}

if (! function_exists('get_ListSubMenu')) {
    /**
     * @return string
     */
    function get_ListSubMenu($MenuID)
    {
        $Submenu                                    = App\Modules\Submenu\Models\Submenu::where('parent_id','=',$MenuID)->orderBy('name')->get();

        $output                                     = array();
        $output [0]                                 = 'Pilih Submenu';
        foreach ($Submenu as $item){
            $output[$item->id]                    = $item->name;
        }
        return $output;

    }
}

if (! function_exists('getnamePermission')) {
    /**
     * @Param ID
     * @return string
     */
    function getnamePermission($PermissionID)
    {
        $Permission                                 = App\Modules\Permission\Models\Permission::find($PermissionID);

        return $Permission->display_name;

    }
}

if (! function_exists('get_ListPermissionbyParentID')) {
    /**
     * @Param ID
     * @return string
     */
    function get_ListPermissionbyParentID($ParentID)
    {
        $Permission                                 = App\Modules\Permission\Models\Permission::where('parent_id','=',$ParentID)->get();
        return $Permission;

    }
}


if (! function_exists('SendSMS')) {
    /**
     * @Param Mobile, Message
     * @return boolean
     */
    function SendSMS($Mobile,$Message)
    {
        $Username                                   = "itmax";
        $Password                                   = "liverpool";
        $Param                                      = "Send.php?username=".$Username."&mobile=".$Mobile."&message=".$Message."&password=".$Password;
        $url                                        = "https://secure.gosmsgateway.com/api/".$Param;
        return $url;
    }
}

if (! function_exists('get_ListGender')) {
    /**
     * @return string
     */
    function get_ListGender()
    {
        $output                                     = array();
        $output [""]                                = 'Pilih Jenis Kelamin';
        $output ["P"]                               = 'Pria';
        $output ["W"]                               = 'Wanita';
        return $output;

    }
}

if (! function_exists('get_ListTemplate')) {
    /**
     * @return string
     */
    function get_ListTemplate()
    {
        $Template                                   = \App\Modules\Template\Models\Template::where('is_active','=',1)->get();

        $output                                     = array();
        $output [0]                                 = 'Pilih Template';
        foreach ($Template as $item){
            $output[$item->id]                    = $item->name;
        }
        return $output;

    }
}

if (! function_exists('CreateInvoice')) {
    /**
     * @return array
     */
    function CreateInvoice($ArrParam)
    {
        $UserID                     = $ArrParam['user_id'];
        $WebsiteID                  = $ArrParam['website_id'];
        $DomainInfo                 = $ArrParam['domain_info']; ### 1 = Subdomain | 2 = Domain ###
        $Domain                     = $ArrParam['domain'];
        $AdditionalPrice            = $ArrParam['additional_price'];
        $AdditionalNote             = $ArrParam['additional_note'];
        $Discount                   = $ArrParam['discount'];
        $StatusPaid                 = $ArrParam['paid'];
        $Detail                     = $ArrParam['detail'];



        if($DomainInfo == 1){
            $Domain                 = "http://".$Domain.".myinvitation.id";
        }else{
            $Domain                 = $Domain;
        }

        $DateTransaction            = date('Y-m-d H:i:s');

        $date                       = $DateTransaction;
        $date                       = strtotime($date);
        $date                       = strtotime("+7 day", $date);
        $DateExpirate               = date("Y-m-d H:i:s",$date);


        $Invoice                    = new \App\Modules\Invoice\Models\Invoice();
        $Invoice->user_id           = $UserID;
        $Invoice->website_id        = $WebsiteID;
        $Invoice->domain_info       = $DomainInfo;
        $Invoice->domain            = $Domain;
        $Invoice->additional_price  = $AdditionalPrice;
        $Invoice->discount          = $Discount;
        $Invoice->paid              = $StatusPaid;
        $Invoice->date_transaction  = $DateTransaction;
        $Invoice->date_expired      = $DateExpirate;
        $Invoice->created_by        = $UserID;

        try{
            $Invoice->save();
            $NoInvoice              = date('ymd').sprintf("%04s",$Invoice->id);
        }catch (\Exception $e){
            $ErrorData              = array(
                "user_id"           => $UserID,
                "website_id"        => $WebsiteID,
                "domain_info"       => $DomainInfo,
                "domain"            => $Domain,
                "additional_price"  => $AdditionalPrice,
                "discount"          => $Discount,
                "paid"              => $StatusPaid
            );

            \Regulus\ActivityLog\Models\Activity::log([
                'contentId'   => 0,
                'contentType' => 'SaveFeaturePremium',
                'action'      => 'CreateInvoice',
                'description' => "Ada Kesalahan pada Pembuatan Invoice Baru",
                'details'     => $e->getMessage(),
                'data'        => json_encode($ErrorData),
                'updated'     => $UserID
            ]);

            $Result                             = array(
                "status"                        => false,
                "message"                       => "Maaf, ada kesalahan teknis. Mohon hubungi web development",
                "reference"                     => "ID Website ".$WebsiteID
            );
            return $Result;
        }

        if($Detail){
            $arrTotal           = array();
            foreach($Detail as $item){
                $InvoiceDetail                  = new \App\Modules\Invoice\Models\InvoiceDetail();
                $InvoiceDetail->invoice_id      = $Invoice->id;
                $InvoiceDetail->note            = $item['note'];
                $InvoiceDetail->price           = $item['price'];
                $InvoiceDetail->discount        = $item['discount'];
                $InvoiceDetail->additional      = $item['additional'];
                $InvoiceDetail->additional_note = $item['additional_note'];
                $InvoiceDetail->total           = $item['total'];
                $InvoiceDetail->created_by      = $UserID;
                try{
                    array_push($arrTotal,$item['total']);
                    $InvoiceDetail->save();
                }catch (\Exception $e){
                    $ErrorData              = array(
                        "invoice_id"        => $Invoice->id,
                        "website_id"        => $WebsiteID,
                        "note"              => $item['note'],
                        "price"             => $item['price'],
                        "discount"          => $item['discount'],
                        "additional"        => $item['additional'],
                        "additional_note"   => $item['additional_note'],
                        "total"             => $item['total'],
                        "user_id"           => $UserID
                    );

                    \Regulus\ActivityLog\Models\Activity::log([
                        'contentId'   => $Invoice->id,
                        'contentType' => 'SaveFeaturePremium',
                        'action'      => 'CreateInvoice Detail',
                        'description' => "Ada Kesalahan pada Penginputan detail Invoice",
                        'details'     => $e->getMessage(),
                        'data'        => json_encode($ErrorData),
                        'updated'     => $UserID
                    ]);
                }

                $TotalDetail                            = array_sum($arrTotal);
                $InvoiceUpdate                          = \App\Modules\Invoice\Models\Invoice::find($Invoice->id);
                $InvoiceUpdate->invoice_number          = $NoInvoice;
                $NominalDiscount                        = $Discount * $TotalDetail / 100;
                $Total                                  = $TotalDetail + $AdditionalPrice - $NominalDiscount;

                $InvoiceUpdate->total                   = $Total;
                $InvoiceUpdate->updated_by              = $UserID;
                try{
                    $InvoiceUpdate->save();
                    $Result                             = array(
                        "status"                        => true,
                        "message"                       => "No. Pesanan kamu ".$NoInvoice,
                        "invoice_id"                    => $Invoice->id
                    );
                    return $Result;
                }catch (\Exception $e){
                    $ErrorData              = array(
                        "user_id"           => $UserID,
                        "website_id"        => $WebsiteID,
                        "domain_info"       => $DomainInfo,
                        "no_pesanan"        => $NoInvoice,
                        "additional_price"  => $AdditionalPrice,
                        "discount"          => $NominalDiscount,
                        "total"             => $Total
                    );

                    \Regulus\ActivityLog\Models\Activity::log([
                        'contentId'   => $Invoice->id,
                        'contentType' => 'SaveFeaturePremium',
                        'action'      => 'CreateInvoice',
                        'description' => "Ada Kesalahan pada Update Invoice Total",
                        'details'     => $e->getMessage(),
                        'data'      => json_encode($ErrorData),
                        'updated'     => $UserID
                    ]);

                    $Result                             = array(
                        "status"                        => false,
                        "message"                       => "Maaf, ada kesalahan teknis. Mohon hubungi web development",
                        "reference"                     => "No. Pesanan ".$NoInvoice,
                        "invoice_id"                    => $Invoice->id
                    );
                    return $Result;
                }
            }
        }else{
            $ErrorData              = array(
                "user_id"           => $UserID,
                "website_id"        => $WebsiteID,
                "domain_info"       => $DomainInfo,
                "domain"            => $Domain,
                "additional_price"  => $AdditionalPrice,
                "discount"          => $Discount,
                "paid"              => $StatusPaid
            );

            \Regulus\ActivityLog\Models\Activity::log([
                'contentId'   => $Invoice->id,
                'contentType' => 'SaveFeaturePremium',
                'action'      => 'CreateInvoice',
                'description' => "Ada Kesalahan pada Pembuatan Invoice Baru",
                'details'     =>  "Detail transaksi tidak ditemukan",
                'data'        => json_encode($ErrorData),
                'updated'     => $UserID
            ]);


            $Result                             = array(
                "status"                        => false,
                "message"                       => "Maaf, ada kesalahan teknis. Mohon hubungi web development",
                "reference"                     => "ID Website ".$WebsiteID.". Detail transaksi tidak ditemukan"
            );
            return $Result;
        }
    }
}



?>
