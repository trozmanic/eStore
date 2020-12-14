<?php

namespace App\Http\Controllers;

use App\Models\Picture;
use App\Models\StoreItem;
use Illuminate\Http\Request;

class StoreItemController extends Controller
{
    public function create (Request $request)
    {
        $storeItemData = $request->json('storeItemData');
        $pictureData = $request->json('pictureData');

        $picturesModel = [];
        foreach ($pictureData as $picture) {
            $pictureModel = new Picture();
            $pictureModel->fill($picture);
            array_push($picturesModel, $pictureModel);
        };

        $storeItem = new StoreItem();
        $storeItem->fill($storeItemData);
        $storeItem->save();
        $storeItem->pictures()->saveMany($picturesModel);

        return $storeItem;
    }

    public function getAll (Request $request)
    {
//        if (!$this->authUser()) {
//            return response()->json(["error" => 'unauthorized'], 401);
//        }

        $storeItems = StoreItem::all();
        $response = [];
        foreach ($storeItems as $storeItem) {
            $storeItemPictures = $storeItem -> pictures() -> get();
            array_push($response, [
                "storeItem"=>$storeItem,
                "pictures" => $storeItemPictures
            ]);
        }

        return $response;
    }

    public function get (Request$request)
    {
        $this->authUser();
        $storeItem = StoreItem::find($request->route('id'));

        $storeItemPictures = [];
        foreach ($storeItem->pictures()->get() as $picture) {
            array_push($storeItemPictures, $picture);
        }

        return [$storeItem, $storeItemPictures];
    }

    public function update (Request $request)
    {
        $storeItem = StoreItem::where('id', $request->json('id'))
            -> update($request->all());

        return $request->all();

    }

    public function delete (Request $request)
    {
        $storeItem = StoreItem::where('id', $request->route('id'));

        $storeItem->delete();

        return $storeItem->get();
    }
}