<?php

namespace App\Http\Controllers;

use App\Recipe;
use Illuminate\Http\Request;
use App\Http\Resources\Recipe as RecipeResource;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Recipes = Recipe::paginate(15);
        return RecipeResource::collection($Recipes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return ($this->addOrUpdate($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function show(Recipe $recipe)
    {
        return new RecipeResource($recipe);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Recipe $recipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        return ($this->addOrUpdate($request, $recipe));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return new RecipeResource($recipe);
    }

    private function addOrUpdate(Request $request, Recipe $recipe=null) {
        if (!$recipe) {
            $recipe = new Recipe;
        }
        $recipe->name = $request->name;
        $recipe->flour = $request->flour;
        $recipe->water = $request->water;
        $recipe->starter = $request->starter;
        $recipe->salt = $request->salt;
        $recipe->notes = $request->notes;
        $recipe->save();  
        return new RecipeResource($recipe); 
    }
}
