<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @apiDefine apartment 公寓
 */

class ApartmentController extends Controller
{
    /**
     * @api {get} /api/apartment/:id/canteen 供应该公寓的所有餐厅
     * @apiVersion 0.0.1
     * @apiGroup apartment
     * @apiParam {Number} id 公寓id
     *
     * @apiSuccess {Object[]} canteens 餐厅
     */
    public function beSuppliedCanteen(Request $request)
    {
        $apartmentId = $request->id;
        $apartment = Apartment::find($apartmentId);

        abort_if(is_null($apartment), 404, 'APARTMENT_NOT_EXISTS');

        return $apartment->beSupplied;
    }


    /**
     * @api {get} /api/apartment/:id/shop 供应该公寓的所有商家
     * @apiVersion 0.0.1
     * @apiGroup apartment
     * @apiParam {Number} id 公寓id
     *
     * @apiSuccess {Object[]} shops 商家
     */
    public function beSuppliedShop(Request $request)
    {
        $apartmentId = $request->id;
        $apartment = Apartment::find($apartmentId);
        abort_if(is_null($apartment), 404, 'APARTMENT_NOT_EXISTS');

        return $apartment->beSupplied->map(function ($canteen) {
            return $canteen->shop;
        })->flatten();
    }
}
