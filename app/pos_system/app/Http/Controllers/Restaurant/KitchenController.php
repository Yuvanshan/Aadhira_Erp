<?php

namespace App\Http\Controllers\Restaurant;

use App\TransactionSellLine;
use App\Utils\RestaurantUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class KitchenController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    protected $restUtil;

    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @param  RestaurantUtil  $restUtil
     * @return void
     */
    public function __construct(Util $commonUtil, RestaurantUtil $restUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->restUtil = $restUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->authorizeKitchenAccess();

        $business_id = request()->session()->get('user.business_id');
        
        $orders = $this->restUtil->getAllOrders($business_id, ['line_order_status' => 'received', 'is_kitchen_order' => 1]);

        return view('restaurant.kitchen.index', compact('orders'));
    }

    /**
     * Marks an order as cooked
     *
     * @return json $output
     */
    public function markAsCooked($id)
    {
        $this->authorizeKitchenAccess();

        try {
            $business_id = request()->session()->get('user.business_id');
            $sl = TransactionSellLine::leftJoin('transactions as t', 't.id', '=', 'transaction_sell_lines.transaction_id')
                        ->where('t.business_id', $business_id)
                        ->where('transaction_id', $id)
                        ->where(function ($q) {
                            $q->whereNull('res_line_order_status')
                                ->orWhere('res_line_order_status', 'received');
                        })
                        ->update(['res_line_order_status' => 'cooked']);

            $output = ['success' => 1,
                'msg' => trans('restaurant.order_successfully_marked_cooked'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Retrives fresh orders
     *
     * @return Json $output
     */
    public function refreshOrdersList(Request $request)
    {
        $this->authorizeOrdersAccess($request->orders_for);
        $business_id = request()->session()->get('user.business_id');
        $orders_for = $request->orders_for;
        $filter = [];
        $service_staff_id = request()->session()->get('user.id');

        if (! $this->restUtil->is_service_staff($service_staff_id) && ! empty($request->input('service_staff_id'))) {
            $service_staff_id = $request->input('service_staff_id');
        }

        if ($orders_for == 'kitchen') {
            $filter['line_order_status'] = 'received';
            $filter['is_kitchen_order'] = 1;
        } elseif ($orders_for == 'waiter') {
            $filter['waiter_id'] = $service_staff_id;
        }

        $orders = $this->restUtil->getAllOrders($business_id, $filter);

        return view('restaurant.partials.show_orders', compact('orders', 'orders_for'));
    }

    /**
     * Retrives fresh orders
     *
     * @return Json $output
     */
    public function refreshLineOrdersList(Request $request)
    {
        $this->authorizeOrdersAccess($request->orders_for);
        $business_id = request()->session()->get('user.business_id');
        $orders_for = $request->orders_for;
        $filter = [];
        $service_staff_id = request()->session()->get('user.id');

        if (! $this->restUtil->is_service_staff($service_staff_id) && ! empty($request->input('service_staff_id'))) {
            $service_staff_id = $request->input('service_staff_id');
        }

        if ($orders_for == 'kitchen') {
            $filter['order_status'] = 'received';
        } elseif ($orders_for == 'waiter') {
            $filter['waiter_id'] = $service_staff_id;
        }

        $line_orders = $this->restUtil->getLineOrders($business_id, $filter);

        return view('restaurant.partials.line_orders', compact('line_orders', 'orders_for'));
    }

    /**
     * Ensure the current user can access kitchen actions.
     *
     * @return void
     */
    protected function authorizeKitchenAccess()
    {
        if (! auth()->user()->can('access_kitchen')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Allow kitchen refreshes only to kitchen users while preserving waiter refresh flow.
     *
     * @param  string|null  $orders_for
     * @return void
     */
    protected function authorizeOrdersAccess($orders_for)
    {
        if ($orders_for === 'kitchen') {
            $this->authorizeKitchenAccess();
        }
    }
}
