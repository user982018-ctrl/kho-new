<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\SaleCare;
use App\Models\SaleCareDataCountAction;
use App\Models\SaleCareDataCountActionDetail;
use App\Models\GroupUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class SaleCareCountActionController  extends Controller
{
    public function apiSumTN(Request $r)
    {
        $daterange = $r->daterange;
        $user = Auth::user();
        $total = 0;
        if ($user) {
            $listDataCount = $this->getListDataCount( $r->daterange);
            $data = $this->getReportUserSaleEffect($listDataCount, $user->id);
            $total = $data['total'];
        }

        return response()->json(['count' => $total]);
    }

    public function ajaxViewReportEffect(Request $r)
    {
        $dataFilter['daterange'] = $r->date;
        $listSale = Helper::getListSaleV2(Auth::user());
        $show = $r->show;
        $list = [];

        // Xử lý filter groupUser
        if ($r->groupUser && $r->groupUser != 999) {
            $listUserInGroup = GroupUser::find($r->groupUser);
            if ($listUserInGroup) {
                $listUserGroupIds = $listUserInGroup->users->pluck('id')->toArray();
                // dd($listUserGroupIds);
                $listSale = $listSale->whereIn('id', $listUserGroupIds);
            }
        }

        $i = 1;
        $listDataCount = $this->getListDataCount( $dataFilter['daterange']);
        foreach ($listSale->get() as $sale) {
            if ($i > $show) {
                break;
            }
            $count = $this->getReportUserSaleEffect($listDataCount, $sale->id);
           
            if ($count['total'] > 0) {
                $list[] = [
                    'name' => ($sale->real_name) ?: '',
                    'count' => $count['total'],
                ];
                $i++;
            }
            
        }

        if ($list) {
            $this->sortByTotalCount($list);
        }

        // Xử lý số lượng hiển thị
        // if ($r->show && $r->show != 20) {
        //     $list = array_slice($list, 0, $r->show);
        // }

        return $list;
    }

    public function getReportUserSaleEffect($listDataCount, $saleID)
    {
        $result = [];
        $total = 0;
        if ($listDataCount->count() > 0) {
            // dd($listDataCount->get());
            foreach ($listDataCount->get() as $item) {
                $count = 0;
                $scCountAction = $item->scCountAction;
                // dd($scCountAction);
                if ($scCountAction) {
                    $count = $scCountAction->where('assign_user', $saleID)->count();
                }

                $total += $count;
                $result['data'] = [
                    'id' => $item->id,
                    'date' => $item->created_at,
                    'count' => $count,
                ];
            }
        }

        $result['total'] = $total;

        return $result;
    }

    public function sortByTotalCount(&$list)
    {
        array_multisort(array_column($list, 'count'), SORT_DESC, $list);
    }

    public function getReportSaleEffect($time, $checkAll = false, $show = 20)
    {
        $dataFilter['daterange'] = "$time - $time";
        $listSale = Helper::getListSaleV2(Auth::user());
        $result = [];

        if (!$checkAll) {
            $checkAll = isFullAccess(Auth::user()->role);
        }
        
        $isLeadSale = Helper::isLeadSale(Auth::user()->role);
        if ($checkAll || $isLeadSale) {
            $listDataCount = $this->getListDataCount( $dataFilter['daterange']);
            $i = 1;
            foreach ($listSale->get() as $sale) {
                if ($i > $show) {
                    break;
                }

                $count = $this->getReportUserSaleEffect($listDataCount, $sale->id);
                if ($count['total'] > 0) {
                    $result[] = [
                        'name' => ($sale->real_name) ?: '',
                        'count' => $count['total'],
                    ];
                    $i++;
                }
            }
        }

        if ($result) {
            $this->sortByTotalCount($result);
        }

        return $result;
    }

    public function viewReportEffectTN()
    {
        $toMonth      = date("d/m/Y", time());

        /**set tmp */
        // $toMonth = '20/10/2025';
        // $item = $this->filterByDate('day', $toMonth);
        $dataCountSale = $this->getReportSaleEffect($toMonth);
        $groupUser = GroupUser::orderBy('id', 'desc')->where('type', 'sale')->get();
        return view('pages.sale.report')->with('dataCountSale', $dataCountSale)->with('groupUser', $groupUser);
    }
    public function getListDataCount($daterange)
    {
        $time    = explode("-",$daterange); 
        $timeBegin  = str_replace('/', '-', $time[0]);
        $timeEnd    = str_replace('/', '-', $time[1]);
        $dateBegin  = date('Y-m-d 00:00:00',strtotime("$timeBegin"));
        $dateEnd    = date('Y-m-d 23:59:00',strtotime("$timeEnd"));

        // dd($dateEnd);
        $scCount = $this->getListSCCount($dateBegin, $dateEnd);
        return $scCount;
        // if ($scCount->count() > 0) {
        //     dd($scCount->get());
        //     foreach ($scCount->get() as $sc) {
        //         $data = $sc->scCountAction;
           
        //         $count = $data->where('assign_user', $idSale)->count();
        //     }
            
        //     // dd($count);
        // }

        // return $count;
    }

    public function setDataTNLog($idSaleCare, $action = null)
    {
        $today = date("d");

        $saleCare = SaleCare::find($idSaleCare);
        if (!$saleCare) {
            return false;
        }

        $check = $this->checkIssetDataTNLog( $idSaleCare);
        if (!$check) {
            $assignSaleID = $saleCare->assign_user;
            if (Auth::user()->id != $assignSaleID && Auth::user()->is_sale) {
                $assignSaleID = Auth::user()->id;
            }

            $this->saveDataTNLog($assignSaleID, $idSaleCare, $today, $action);
        }
    }

    public function getSCCountToDay($beginDay = null, $endDay = null)
    {
        return $this->getListSCCount($beginDay, $endDay)->first();

    }

    public function getListSCCount($beginDay = null, $endDay = null)
    {
        if (!$beginDay && !$endDay) {
            $beginDay = date("Y-m-d 00:00:00");
            $endDay = date("Y-m-d 23:59:00");
        }

        $data = SaleCareDataCountAction::whereDate('created_at', '>=',$beginDay)
            ->whereDate('created_at', '<',$endDay);
        return $data;

    }

    /**
     * true: đã tồn tại
     * false: không có
     * @param mixed $today
     * @return bool
     */
    public function checkIssetDataTNLog($idSale)
    {
        $scDay = $this->getSCCountToDay();
        if (!$scDay) {
            return false;
        }

        $data = $scDay->scCountAction;
        if ($data->count()) {
            foreach ($data as $item) {
                if ($item->id_sale_care == $idSale) {
                    return true;
                }
            }
        }

        return false;
    }

    public function saveDataTNLog($idSale, $idSaleCare, $today, $action)
    {
        $scDay = $this->getSCCountToDay();
        if (!$scDay) {
            $scDay = new SaleCareDataCountAction();
            $scDay->save();
        }

        $scDetail= new SaleCareDataCountActionDetail();
        $scDetail->assign_user = $idSale;
        $scDetail->id_sale_care = $idSaleCare;
        $scDetail->action = $action;
        $scDetail->id_sc_count = $scDay->id;
        $scDetail->save();
    }
}
