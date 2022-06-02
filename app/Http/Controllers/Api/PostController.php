<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PostResource;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PostResource::collection(Post::paginate(5));    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'title'=>['required'],
            'description'=>['required'],
            'source'=>['required'],
            'featured'=>['required','boolean']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors() , 422);
        }
        $id=Auth::id();
        $detail['title']=$request->title;
        $detail['description']=$request->description;
        $detail['source']=$request->source;
        $detail['featured']=$request->featured;
        $detail['user_id']=$id;
        $detail['meta_title']=$request->title;
        $detail['meta_description']=$request->description;
        $detail['position']=Post::count()+1;
        
        $post=Post::create($detail);
        
        return  new PostResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\rc  $rc
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post=Post::findOrFail($id);
        $post->increment('viewcount',1);
        $post->save();
        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\rc  $rc
     * @return \Illuminate\Http\Response
     */
    public function edit(rc $rc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\rc  $rc
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator=Validator::make($request->all(),[
            'title'=>['required'],
            'description'=>['required'],
            'source'=>['required'],
            'featured'=>['required','boolean']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors() , 422);
        }
        $id=Auth::id();
        $post=Post::findOrFail($id);
        
        $detail['title']=$request->title;
        $detail['description']=$request->description;
        $detail['source']=$request->source;
        $detail['featured']=$request->featured;
        $detail['user_id']=$id;
        $detail['meta_title']=$request->title;
        $detail['meta_description']=$request->description;
        $detail['position']=Post::count()+1;
        if($post['user_id']==$id){
            $post->fill($detail);
             $post->save(); 
             return  new PostResource($post);
        }
        return response()->json('you are unauthorized',401);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\rc  $rc
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Post::whereId($id)->delete();;
        return response()->json('the post is deleted successfully');
    }
}
