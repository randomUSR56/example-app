<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Laravel\Facades\Image;
use Carbon\Carbon;

class PortfolioController extends Controller
{
    public function allPortfolio()
    {
        $portfolio = Portfolio::latest()->get();
        return view('admin.portfolio.portfolio_all', compact('portfolio'));
    }// End Method

    public function addPortfolio()
    {
        //$portfolio = Portfolio::latest()->get();
        return view('admin.portfolio.portfolio_add');
    }// End Method

    public function storePortfolio(Request $request)
    {
        $request->validate([
            'portfolio_name' => 'required',
            'portfolio_title' => 'required',
            'portfolio_image' => 'required',

        ], [
            'portfolio_name.required' => 'Please input portfolio name',
            'portfolio_title.required' => 'Please input portfolio title',
        ]);

        if ($request->file('portfolio_image')) {
            $image = $request->file('portfolio_image');

            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = Image::read($image);

            $img->resize(1020, 519)->save('upload/portfolio/' . $name_gen);

            $save_url = 'upload/portfolio/' . $name_gen;

            Portfolio::insert([
                'portfolio_name' => $request->portfolio_name,
                'portfolio_title' => $request->portfolio_title,
                'portfolio_description' => $request->portfolio_description,
                'portfolio_image' => $save_url,
                'created_at' => Carbon::now(),
            ]);

            $notification = array(
                'message' => 'Portfolio inserted Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        } else {
            $notification = array(
                'message' => 'Portfolio Insert Failed',
                'alert-type' => 'error'
            );
            return redirect()->route('all.portfolio')->with($notification);
        }
    }

    public function updatePortfolio(Request $request){

        $portfolio_id = $request->id;

        if ($request->file('portfolio_image')) {
            $image = $request->file('portfolio_image');

            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = Image::read($image);

            $img->resize(1020, 519)->save('upload/portfolio/' . $name_gen);
            $save_url = 'upload/portfolio/'.$name_gen;

            Portfolio::findOrFail($portfolio_id)->update([
                'portfolio_name' => $request->portfolio_name,
                'portfolio_title' => $request->portfolio_title,
                'portfolio_description' => $request->portfolio_description,
                'portfolio_image' => $save_url,

            ]);
            $notification = array(
            'message' => 'Portfolio Updated with Image Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.portfolio')->with($notification);

        } else{

            Portfolio::findOrFail($portfolio_id)->update([
                'portfolio_name' => $request->portfolio_name,
                'portfolio_title' => $request->portfolio_title,
                'portfolio_description' => $request->portfolio_description,


            ]);
            $notification = array(
            'message' => 'Portfolio Updated without Image Successfully',
            'alert-type' => 'success'
            );

            return redirect()->route('all.portfolio')->with($notification);

        } // end Else

     } // End Method

    public function editPortfolio($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        return view('admin.portfolio.portfolio_edit', compact('portfolio'));
    }// End Method

    public function deletePortfolio($id)
    {
        $image = Portfolio::findOrFail($id);
        $img = $image->portfolio_image;
        unlink($img);

        Portfolio::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }// End Method

    public function portfolioDetails($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        return view('frontend.portfolio_details', compact('portfolio'));
    }// End Method
}
