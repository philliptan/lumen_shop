<?php

namespace App\Http\Controllers;

use App\Http\Response\FractalResponse;
use Laravel\Lumen\Routing\Controller as BaseController;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Controller extends BaseController
{
    /*
     * @var FractalResponse
     */
    private $fractal;

    public function __construct(FractalResponse $fractal)
    {
        $this->fractal = $fractal;

        /*DB::listen(function($query) {
            $message = "\n SQL ::: %s \n Binding ::: %s \n Timing ::: %s";
            Log::debug(sprintf(
                $message,
                $query->sql,
                implode(', ', $query->bindings),
                $query->time
            ));
        });*/
    }

    /**
     *
     * @param $data
     * @param TransformerAbstract $transformer
     * @param null $resourceKey
     * @return array
     */
    public function item(
        $data, 
        TransformerAbstract $transformer, 
        $resourceKey = null
    ): array {

        return $this->fractal->item($data, $transformer, $resourceKey);
    }

    /**
     *
     * @param $data
     * @param TransformerAbstract $transformer
     * @param null $resourceKey
     * @return array
     */
    public function collection(
        $data, 
        TransformerAbstract $transformer, 
        $resourceKey = null
    ): array {

        return $this->fractal->collection($data, $transformer, $resourceKey);
    }

    //
}
