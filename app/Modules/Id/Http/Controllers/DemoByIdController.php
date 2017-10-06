<?php

namespace App\Modules\Id\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Modules\Website\Models\Website as WebsiteModel;
use App\Modules\Template\Models\Template as TemplateModel;
use App\Modules\Gallery\Models\Gallery as GalleryModel;
use App\Modules\Storyoflove\Models\StoryOfLove as StoryOfLoveModel;
use App\Modules\Bridesmaid\Models\Bridesmaid as BridesmaidModel;

use Theme;

class DemoByIdController extends Controller
{


    public function showDemoPreview($WebsiteID,$TemplateID){
        $Template                                       = TemplateModel::find($TemplateID);
        $Website                                        = WebsiteModel::find($WebsiteID);
        $Gallery                                        = GalleryModel::where('website_id','=',$WebsiteID)->get();
        $StoryOfLove                                    = StoryOfLoveModel::where('website_id','=',$WebsiteID)->get();
        $Bridesmaid                                     = BridesmaidModel::where('website_id','=',$WebsiteID)->get();

        Theme::setActive($Template->slug);

        $this->_data['WebsiteInfo']                     = $Website;
        $this->_data['GalleryInfo']                     = $Gallery;
        $this->_data['StoryOfLove']                     = $StoryOfLove;
        $this->_data['Bridesmaid']                      = $Bridesmaid;

        ### PATH IMAGE ###
        $this->_data['UrlWebInformation']               = url('/')."/".getPathWebsiteInformation(); // Image Website //
        $this->_data['UrlGallery']                      = url('/')."/".getPathGallery(); // Image Gallery //
        $this->_data['UrlStoryOfLove']                  = url('/')."/".getPathStoryofLove(); // Image Story of Love //
        $this->_data['UrlBridesmaid']                   = url('/')."/".getPathBridesmaid(); // Image Bridesmaid //
        ### PATH IMAGE ###

    }

    public function showDemo($WebsiteID){
        $Website                                        = WebsiteModel::find($WebsiteID);
        $Gallery                                        = GalleryModel::where('website_id','=',$WebsiteID)->get();
        $StoryOfLove                                    = StoryOfLoveModel::where('website_id','=',$WebsiteID)->get();
        $Bridesmaid                                     = BridesmaidModel::where('website_id','=',$WebsiteID)->get();

        Theme::setActive($Website->template->slug);

        $this->_data['WebsiteInfo']                     = $Website;
        $this->_data['GalleryInfo']                     = $Gallery;
        $this->_data['StoryOfLove']                     = $StoryOfLove;
        $this->_data['Bridesmaid']                      = $Bridesmaid;

        ### PATH IMAGE ###
        $this->_data['UrlWebInformation']               = url('/')."/".getPathWebsiteInformation(); // Image Website //
        $this->_data['UrlGallery']                      = url('/')."/".getPathGallery(); // Image Gallery //
        $this->_data['UrlStoryOfLove']                  = url('/')."/".getPathStoryofLove(); // Image Story of Love //
        $this->_data['UrlBridesmaid']                   = url('/')."/".getPathBridesmaid(); // Image Bridesmaid //
        ### PATH IMAGE ###

    }


}
