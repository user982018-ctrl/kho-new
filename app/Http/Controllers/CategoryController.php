<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $list = Category::orderBy('id', 'desc')->paginate(5);

        return view('pages.category.index')->with('list', $list);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function add()
    {
        return view('pages.category.addOrUpdate');
    }

    /**
     * Display a listing of the myformPost.
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ],[
            'name.required' => 'Nhập tên sản phẩm',
        ]);
        
        if ($validator->passes()) {
            if(isset($request->id)){
                $category = Category::find($request->id);
                $text = 'Cập nhật danh mục thành công.';
                $category->status = $request->status;
            } else {
                $category = new Category();
                $text = 'Tạo danh mục thành công.';
            }
            
            $category->name = $request->name;
            $category->save();
            return response()->json(['success'=>$text]);
        }
        
        return response()->json(['errors'=>$validator->errors()]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function viewUpdate($id)
    {
        $category = Category::find($id);
        if($category){
            return view('pages.category.addOrUpdate')->with('category', $category);
        } 

        return redirect('/');
      
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function delete($id)
    {
        $category = Category::find($id);
        if($category){
            $category->delete();
            return redirect('/danh-muc-san-pham')->with('success', 'Danh mục đã xoá thành công!');            
        } 

        return redirect('/danh-muc-san-pham') ->with('error', 'Đã xảy ra lỗi khi xoá Danh mục!');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function search(Request $request)
    {
        $category = Category::where('name', 'like', '%' . $request->search . '%')->orderBy('id', 'desc')->paginate(5);
        if($category){
            return view('pages.category.index')->with('list', $category);           
        } 

        return redirect('/');
    }

}
