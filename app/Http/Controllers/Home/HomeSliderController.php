<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeSlide;
use Ramsey\Uuid\Type\Hexadecimal;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Laravel\Facades\Image;

class HomeSliderController extends Controller
{
    public function homeSlider()
    {
        $homeslide = HomeSlide::find(1);
        return view('admin.home_slide.home_slide_all', compact('homeslide'));
    }// End Method

    public function updateSlider(Request $request)
    {
        //$imageManager = new ImageManager(new Driver());

        $slide_id = $request->id;

        if ($request->file('home_slide')) {
            $image = $request->file('home_slide');

            $name_gen = hexdec(uniqid()).'.'.$image->
                getClientOriginalExtension(); // 3434343434.jpg
            $img = Image::read($image);

            $img->resize(636, 852)->save('upload/home_slide/'.$name_gen);

            $save_url = 'upload/home_slide/'.$name_gen;

            //dd($slide_id);

            HomeSlide::findOrFail($slide_id)->update([
                'title' => $request->title,
                'short_title' => $request->short_title,
                'video_url' => $request->video_url,
                'home_slide' => $save_url,
            ]);
            //dd($request);

            $notification = array(
                'message' => 'Home Slide Updated with Image Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
        else {
            HomeSlide::findOrFail($slide_id)->update([
                'title' => $request->title,
                'short_title' => $request->short_title,
                'video_url' => $request->video_url,
            ]);

            $notification = array(
                'message' => 'Home Slide Updated without Image Successfully',
                'alert-type' => 'error'
            );
            return redirect() -> back() -> with($notification);
        }
    }// End Method
}
