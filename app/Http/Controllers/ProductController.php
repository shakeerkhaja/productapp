<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilters;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Image;



class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductFilters $filters)
    {
        return Inertia::render('Product/Product', [
            'products' =>function() use($filters) {
                return fractal(Product::filter($filters)
                    ->paginate(request()->perPage != null ? request()->perPage : 10),
                    new ProductTransformer())->toArray();
            }
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Product/ProductEntry');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $request->validate([
            'name' => 'required',
            'upc' => 'required',
            'price' => 'required|numeric',
            'image' => 'required',
            'status' => 'required',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->upc = $request->upc;
        $product->price = $request->price;
        $product->status = $request->status;

        if($request->get('image'))
        {
            $image = $request->get('image');
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('image'))->save(public_path('images/').$name);
            $product->image = $name;
        }

        $product->save();

        return redirect()->route('products.index')
            ->with('successMessage', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        return Inertia::render('Product/ProductEntry', [
            'product' => $product,
            'editFlag' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'upc' => 'required',
            'price' => 'required|numeric',
            'image' => 'required',
            'status' => 'required',
        ]);

        $product = Product::find($id);

//        if($request->get('image'))
//        {
            $image = $request->get('image');
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('image'))->save(public_path('images/').$name);
            $product->image = $name;
//        }

        $product->name = $request->name;
        $product->upc = $request->upc;
        $product->price = $request->price;
        $product->status = $request->status;

        $product->update();


        return redirect()->route('products.index')
            ->with('successMessage','Product updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success','Product deleted successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll(Request $request)
    {
        foreach($request->id as $delete) {
        $product = Product::find($delete['id']);
        $product->delete();
    }

        return redirect()->route('products.index')
            ->with('success','Product deleted successfully');
    }
}
