<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use Ramsey\Uuid\Type\Hexadecimal;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\MultiImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function aboutPage()
    {
        $aboutpage = About::find(1);
        return view('admin.about_page.about_page_all', compact('aboutpage'));
    }// End Method

    public function updateAbout(Request $request)
    {
        //$imageManager = new ImageManager(new Driver());

        $about_id = $request->id;

        if ($request->file('about_image')) {
            $image = $request->file('about_image');

            $name_gen = hexdec(uniqid()).'.'.$image->
                getClientOriginalExtension(); // 3434343434.jpg
            $img = Image::read($image);

            $img->resize(523, 605)->save('upload/home_about/'.$name_gen);

            $save_url = 'upload/home_about/'.$name_gen;

            //dd($slide_id);

            About::findOrFail($about_id)->update([
                'title' => $request->title,
                'short_title' => $request->short_title,
                'short_description' => $request->short_description,
                'long_description' => $request->long_description,
                'about_image' => $save_url,
            ]);
            //dd($request);

            $notification = array(
                'message' => 'About Page Updated with Image Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
        else {
            About::findOrFail($about_id)->update([
                'title' => $request->title,
                'short_title' => $request->short_title,
                'short_description' => $request->short_description,
                'long_description' => $request->long_description,
            ]);

            $notification = array(
                'message' => 'About Page Updated without Image Successfully',
                'alert-type' => 'error'
            );
            return redirect() -> back() -> with($notification);
        }
    }// End Method

    public function homeAbout()
    {
        $aboutpage = About::find(1);
        return view('frontend.about_page', compact('aboutpage'));
    }// End Method

    public function aboutMultiImage()
    {
        return view('admin.about_page.multi_image');
    }

    public function storeMultiImage(Request $request)
    {
        $image = $request->file('multi_image');

        foreach ($image as $multi_image) {
            $name_gen = hexdec(uniqid()).'.'.$multi_image->
                getClientOriginalExtension(); // 3434343434.jpg
            $img = Image::read($multi_image);

            $img->resize(220, 220)->save('upload/multi/'.$name_gen);

            $save_url = 'upload/multi/'.$name_gen;

            //dd($slide_id);

            MultiImage::insert([
                'multi_image' => $save_url,
                'created_at' => Carbon::now(),
            ]);
            //dd($request);
            //dd($request->all());
        }

        $notification = array(
            'message' => 'Multi Image Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.multi.image')->with($notification);
    }// End Method

    public function allMultiImage()
    {
        $allMultiImages = MultiImage::all();
        return view('admin.about_page.all_multi_image', compact('allMultiImages'));
    }// End Method

    public function editMultiImage($id)
    {
        $multiImage = MultiImage::findOrFail($id);
        return view('admin.about_page.edit_multi_image', compact('multiImage'));
    }// End Method

    public function updateMultiImage(Request $request)
    {
        $multi_image_id = $request->id;

        if ($request->file('multi_image')) {
            $image = $request->file('multi_image');

            $name_gen = hexdec(uniqid()).'.'.$image->
                getClientOriginalExtension(); // 3434343434.jpg
            $img = Image::read($image);

            $img->resize(220, 220)->save('upload/multi/'.$name_gen);

            $save_url = 'upload/multi/'.$name_gen;

            MultiImage::findOrFail($multi_image_id)->update([
                'multi_image' => $save_url,
            ]);


            $notification = array(
                'message' => 'Multi Image Updated with Image Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('all.multi.image')->with($notification);
        }
        else {
            MultiImage::findOrFail($multi_image_id)->update([
                'multi_image' => $request->multi_image,
            ]);

            $notification = array(
                'message' => 'Multi Image Update Failed',
                'alert-type' => 'error'
            );
            return redirect()->route('all.multi.image')-> with($notification);
        }
    }// End Method

    public function deleteMultiImage($id)
    {
        $image = MultiImage::findOrFail($id);
        $img = $image->multi_image;
        unlink($img);

        MultiImage::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Multi Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }// End Method
}
