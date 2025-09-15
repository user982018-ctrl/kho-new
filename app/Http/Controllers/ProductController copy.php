<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\ProductAttributes;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductCombo;
use App\Models\ProductComboItems;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function saveCombo(Request $r)
    {
        $data = $r->all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'price' => 'required|numeric',
            'products' => 'required',
        ],[
            'name.required' => 'Bạn chưa nhập tên sản phẩm',
            'price.required' => 'Bạn chưa nhập giá sản phẩm',
            'price.numeric' => 'Chỉ được nhập số',
            'products.required' => 'Bạn quên chọn sản phẩm',
        ]);

        if (!$validator->passes()) {
            $mess = '';
            foreach ($validator->errors()->messages() as $error) {
                $mess = $error[0];
                break;
            }
            notify()->error($mess, 'Thất baị!');
            return redirect()->route('add-combo');
        }

        $product = new Product();
        $product->name = $data['name'];
        $product->price = $data['price'];
        $product->save();

       
        $listItem = $data['products'];
        $quantities = $data['quantities'];
        foreach ($listItem as $k => $value) {
            $productItem = new ProductComboItems();
            $productItem->product_id = $value;
            $productItem->combo_id = $product;
            $productItem->qty = $quantities[$k];
            $productItem->save();
        }
        
        dd($data);
    }

    public function addCombo()
    {
        $products = Product::where('status', 1)->get()->toJson();
        return view('pages.product.addUpdateCombo')->with('products', $products)
            ->with('user', Auth::user());
    }
    public function getAttributesProduct()
    {
        $listAttribute = [];
        $listAttr = ProductAttributes::where('status', 1);

        foreach ($listAttr->get() as $attr) {
            if ($attr->values) {
                $attr = [
                    'id' => $attr->id,
                    'name' => $attr->name,
                    'values' => $attr->values->select('id', 'attribute_id', 'value')->toArray()
                ];
                $listAttribute[] = $attr;
            }
        }

        return $listAttribute;
    }


    public function getVariantsProductById(Request $req)
    {
        $result = $list_attribute = [];
        $id = $req->id;
        $variants = ProductVariant::where('product_id', $id);

        foreach ($variants->get() as $variant) {
            $valueAttr = $variant->attributeValues;
            $resultTmp =  $variant->getAttributes();
            $list_attribute = [];
            foreach ($valueAttr as $attr) {
                $attribute = $attr->attribute;
                // $resultTmp['list_attribute'][] = $attribute->getAttributes();
                $list_attribute[] = $attribute->id;
            }
            if (count($list_attribute) > 0) {
                $resultTmp['list_attribute'] = array_values($list_attribute);
                $result[] = $resultTmp;
            }
           
        }

        return response()->json($result);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $list = $this->getListProductByPermisson(Auth::user()->role)->paginate(15);
        return view('pages.product.index')->with('list', $list);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function addProduct()
    {
        $listCategory =  Category::all()->where('status', 1);
        return view('pages.product.addOrUpdate')->with('listCategory', $listCategory)
            ->with('user', Auth::user());
    }

    /**
     * Display a listing of the myformPost.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'qty' => 'required|numeric',
        ],[
            'name.required' => 'Nhập tên sản phẩm',
            'price.required' => 'Nhập giá sản phẩm',
            'price.numeric' => 'Chỉ được nhập số',
            'qty.required' => 'Nhập số lượng',
            'qty.numeric' => 'Chỉ được nhập số',
        ]);
        
        if ($validator->passes()) {
            if(isset($request->id)){
                $product        = Product::find($request->id);
                $product->status  = $request->status;
                $text = 'Cập nhật sản phẩm thành công.';
            } else {
                $product = new Product();
                $product->status = 1;
                $text = 'Tạo sản phẩm thành công.';
            }
           
            $product->name          = $request->name;
            $product->tax_name      = $request->nameTax;
            $product->qty           = $request->qty;
            $product->price         = $request->price;
            $product->weight        = $request->weight;
            $product->unit          = $request->unit;
            $product->orderBy       = $request->orderBy;
            $product->category_id   = $request->category_id;
            $product->roles         = $request->roles;
            $product->save();
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
        $product = Product::find($id);
        $listCategory =  Category::all();
        if($product){
            return view('pages.product.addOrUpdate')->with('product', $product)
                ->with('listCategory', $listCategory);
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
        $product = Product::find($id);
        if($product){
            $product->delete();
            return redirect('/danh-sach-san-pham')->with('success', 'Sản phẩm đã xoá thành công!');            
        } 

        return redirect('/danh-sach-san-pham') ->with('error', 'Đã xảy ra lỗi khi xoá sản phẩm!');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function search(Request $request)
    {
        $list       = $this->getListProductByPermisson(Auth::user()->role);
        $product    = $list->where('name', 'like', '%' . $request->search . '%')->orderBy('id', 'desc')->paginate(5);
        if($product){
            return view('pages.product.index')->with('list', $product);           
        } 

        return redirect('/');
    }

    public function setProducts(){
        $list = Product::orderBy('id', 'desc')->paginate(5);

        return view('pages.product.index')->with('list', $list);
    }

    public function setProductsByMonth(Request $request){
        $month  = $request->month;
        $list   = Product::orderBy('id', 'desc')
            ->whereMonth('created_at', '=', $month)
            ->paginate(5);

        return view('pages.product.index')->with('list', $list);
    }

    public function setProductsByYear(Request $request){
        $year  = $request->year;
        $list   = Product::orderBy('id', 'desc')
            ->whereYear('created_at', '=', $year)
            ->paginate(5);

        return view('pages.product.index')->with('list', $list);
    }

    public  function getListProductByPermisson($roles) {
        $list       = Product::orderBy('orderBy', 'desc');

        $checkAll   = false;
        $listRole   = [];
        $roles      = json_decode($roles);

        if ($roles) {
            foreach ($roles as $key => $value) {
                if ($value == 1) {
                    $checkAll = true;
                    break;
                } else {
                    $listRole[] = $value;
                }
            }
        }

        if (!$checkAll) {
            // $list = $list->where('roles', $listRole);
            $list = $list->whereIn('roles', $listRole);
        }
        // dd($list->get());
        return $list;
    }

    public function getProductsByCategoryId(Request $req) 
    {
        $id = $req->categoryId;
        $products = Product::where('category_id', $id)->where('status', 1);
        return $products->get();
    }
}
