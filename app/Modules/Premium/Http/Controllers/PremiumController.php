<?php

namespace App\Modules\Premium\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Modules\Website\Models\Website as WebsiteModel;
use App\Modules\Template\Models\Template as TemplateModel;

use Theme;
use Auth;
use Activity;
use Debugbar;
use File;


class PremiumController extends Controller
{
    protected $_data                                = array();

    public function __construct()
    {
        $this->_data['string_menuname']             = 'Premium Fitur';
    }

    public function FormFeaturePremium($WebsiteID){
        $Website                                    = WebsiteModel::find($WebsiteID);
        $Template                                   = TemplateModel::find($Website->template_id);

        $this->_data['Website']                     = $Website;
        $this->_data['WebsiteID']                   = $WebsiteID;
        $this->_data['Template']                    = $Template;

        return Theme::view('modules.premium.premium.form',$this->_data);
    }

    public function SaveFeaturePremium(Request $request){
        $validator = Validator::make($request->all(), [
            'domain'                                => 'required|min:5|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->input());
        }

        $WebsiteID                                  = $request->website_id;
        $AdditionalPrice                            = set_clearFormatRupiah($request->additional_price);
        $Total                                      = set_clearFormatRupiah($request->total);
        $Website                                    = WebsiteModel::find($WebsiteID);

        if($Website->domain != $request->domain){
            if(WebsiteModel::where('domain','=',$request->domain)->count() == 0){
                $Website->domain                    = $request->domain;
                $Website->save();
                $Data[]                                 = array(
                    "note"                              => "Template ". $request->template,
                    "additional"                        => $AdditionalPrice,
                    "additional_note"                   => "",
                    "price"                             => $Website->template->price,
                    "discount"                          => 0,
                    "total"                             => $Total
                );

                $ArrInvoice                         = array(
                    "user_id"                           => Auth::id(),
                    "website_id"                        => $WebsiteID,
                    "domain_info"                       => 1, // ### 1 = Subdomain, 2 = Domain //
                    "domain"                            => $request->domain,
                    "additional_price"                  => 0,
                    "additional_note"                   => "",
                    "paid"                              => "No", // No or Yes
                    "detail"                            => $Data
                );
                $CreateInvoice                      = CreateInvoice($ArrInvoice);
//                dd($CreateInvoice);
                if($CreateInvoice['status']){
                    $ScsMsg                         = $CreateInvoice['message'];
                    $InvoiceID                      = $CreateInvoice['invoice_id'];
                    return redirect()
                        ->route('invoice_show',$InvoiceID)
                        ->with('ScsMsg',$ScsMsg);
                }else{
                    $ScsMsg                         = $CreateInvoice['message'];
                    $InvoiceID                      = $CreateInvoice['invoice_id'];
                    return redirect()
                        ->route('invoice_show',$InvoiceID)
                        ->with('ScsMsg',$ScsMsg);
                }
            }else{
                $ErrMsg                             = 'Subdomain "'.$request->domain.'" sudah ada yang menggunakan.';
                return redirect()
                    ->route('premium_feature_form', $WebsiteID)
                    ->with('ErrMsg',$ErrMsg);
            }
        }else{
            ### TIDAK ADA PERUBAHAN DOMAIN ###
            $Data[]                                 = array(
                "note"                              => "Template ". $request->template,
                "additional"                        => $AdditionalPrice,
                "additional_note"                   => "",
                "price"                             => $Website->template->price,
                "discount"                          => 0,
                "total"                             => $Total
            );
            $ArrInvoice                             = array(
                "user_id"                           => Auth::id(),
                "website_id"                        => $WebsiteID,
                "domain_info"                       => 1, // ### 1 = Subdomain, 2 = Domain //
                "domain"                            => $request->domain,
                "additional_price"                  => 0,
                "additional_note"                   => "",
                "discount"                          => 0,
                "paid"                              => "No", // No or Yes
                "detail"                            => $Data
            );
            $CreateInvoice                          = CreateInvoice($ArrInvoice);
            if($CreateInvoice['status']){
                $ScsMsg                         = $CreateInvoice['message'];
                $InvoiceID                      = $CreateInvoice['invoice_id'];
                return redirect()
                    ->route('invoice_show',$InvoiceID)
                    ->with('ScsMsg',$ScsMsg);
            }else{
                $ScsMsg                         = $CreateInvoice['message'];
                $InvoiceID                      = $CreateInvoice['invoice_id'];
                return redirect()
                    ->route('invoice_show',$InvoiceID)
                    ->with('ScsMsg',$ScsMsg);
            }
            ### TIDAK ADA PERUBAHAN DOMAIN ###
        }
    }

}
