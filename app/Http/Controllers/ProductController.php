<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use Session;
use Illuminate\Support\Facades\Redirect;

session_start();
class ProductController extends Controller
{
    public function AuthLogin(){
        $admin_id= Session::get('admin_id');
        if($admin_id){
           return redirect::to('admin.dashboard');

        }
        else{
           return redirect::to('admin')->send();
        }
    }
    public function add_product(){
        $this->AuthLogin();
        $cate_product= DB::table('tbl_category_product')->orderBy('category_id','desc')->get();
        $brand_product= DB::table('tbl_brand')->orderBy('brand_id','desc')->get();
        return view('admin.add_product')-> with('cate_product',$cate_product)->with('brand_product',$brand_product);
            }
            
            public function all_product(){
                $this->AuthLogin();
             $all_product = DB::table('tbl_product')
             ->join('tbl_category_product','tbl_category_product.category_id','=','tbl_product.category_id')
             ->join('tbl_brand','tbl_brand.brand_id','=','tbl_product.brand_id')
             ->orderby('tbl_product.product_id','desc')->get();

             $manager_product= view('admin.all_product') -> with('all_product',$all_product);
        return view('admin_layout') -> with('admin.all_product',$manager_product);
                
            }
            public function save_product(Request $request){
                $this->AuthLogin();
                $data = array();
                $data['product_name']= $request->product_name;
                $data['product_price']= $request->product_price;
                $data['product_desc']= $request->product_desc;
                $data['product_content']= $request->product_content;    
                $data['product_status']= $request->product_status;    
                $data['category_id']= $request->cate_product;    
                $data['brand_id']= $request->brand_product;

                $get_image=$request->file('product_image');
                if($get_image){
                $get_name_image=$get_image->getClientOriginalName();
                $name_image=current(explode('.',$get_name_image));
                $new_image=$name_image.rand(0,99).'.'.$get_image->getClientOriginalExtension();
                $get_image->move('public/uploads/product',$new_image);
                
                $data['product_image']=$new_image;
                DB::table('tbl_product')->insert($data);
                 //not showing an alert box.
                session::put('message','Thêm sản phẩm thành công');
                return Redirect::to('add-product') ;
                }
                $data['product_image']='';
                DB::table('tbl_product')->insert($data);
                 //not showing an alert box.
                session::put('message','Thêm sản phẩm thành công');
                return Redirect::to('add-product') ;
        
            } 
            public function edit_product(Request $request,$product_id){
                $this->AuthLogin();
                $cate_product= DB::table('tbl_category_product')->orderBy('category_id','desc')->get();
                $brand_product= DB::table('tbl_brand')->orderBy('brand_id','desc')->get();
                $edit_product = DB::table('tbl_product')->where('product_id',$product_id)->get();
                $manager_product= view('admin.edit_product') -> with('edit_product',$edit_product)->with('cate_product',$cate_product)->with('brand_product',$brand_product);
           return view('admin_layout') -> with('admin.edit_product',$manager_product);

                   
               }
               public function update_product(Request $request,$product_id){
                $this->AuthLogin();
                $data = array();
                $data['product_name']= $request->product_name;
                $data['product_price']= $request->product_price;
                $data['product_desc']= $request->product_desc;
                $data['product_content']= $request->product_content;    
                $data['product_status']= $request->product_status;    
                $data['category_id']= $request->cate_product;    
                $data['brand_id']= $request->brand_product;

               
           $get_image=$request->file('product_image');
           if($get_image){
           $get_name_image=$get_image->getClientOriginalName();
           $name_image=current(explode('.',$get_name_image));
           $new_image=$name_image.rand(0,99).'.'.$get_image->getClientOriginalExtension();
           $get_image->move('public/uploads/product',$new_image);
           
           $data['product_image']=$new_image;
           DB::table('tbl_product')->where('product_id',$product_id)->update($data);
            //not showing an alert box.
           session::put('message','Cập nhật sản phẩm thành công');
           return Redirect::to('all-product') ;
           }
         
           DB::table('tbl_product')->where('product_id',$product_id)->update($data);
            //not showing an alert box.
           session::put('message','Cập nhật sản phẩm thành công');
           return Redirect::to('all-product') ;
               }
               public function delete_product($product_id){
                $this->AuthLogin();
                DB::table('tbl_product')->where('product_id',$product_id)->delete();
                session::put('message','Xóa sản phẩm thành công');
                return Redirect::to('all-product');
        
               }
               //END FUNCTION PRODUCT ADMIN
               public function show_details_product($product_id){
                $cate_product= DB::table('tbl_category_product')->orderBy('category_id','desc')->get();
                $brand_product= DB::table('tbl_brand')->orderBy('brand_id','desc')->get();
                $show_details_product = DB::table('tbl_product')
                ->join('tbl_category_product','tbl_category_product.category_id','=','tbl_product.category_id')
                ->join('tbl_brand','tbl_brand.brand_id','=','tbl_product.brand_id')
                ->orderby('tbl_product.product_id','desc')->where('product_id',$product_id)->get();
                foreach($show_details_product as $key =>$value){
                    $category_id=$value->category_id;
                }
                $show_related_product = DB::table('tbl_product')
                ->join('tbl_category_product','tbl_category_product.category_id','=','tbl_product.category_id')
                ->join('tbl_brand','tbl_brand.brand_id','=','tbl_product.brand_id')
                ->where('tbl_category_product.category_id',$category_id)->whereNotIn('product_id',[$product_id])->get();
                return view('pages.product.show_detail')->with('category',$cate_product)->with('brand',$brand_product)->with('show_details_product',$show_details_product)->with('show_related_product',$show_related_product);
            }
       

           

        
}
