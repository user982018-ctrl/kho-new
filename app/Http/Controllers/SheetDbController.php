<?php

namespace App\Http\Controllers;

use App\Models\ReportDetailDigital;
use App\Models\ReportDigital;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SheetDbController extends Controller
{
    public function get() 
    {
        $fileGGJson = 'app/cir-ads-77aef3b655c1.json';
        $dataGr = $this->prepareData();
        foreach ($dataGr as $group) {
            Log::channel('report_digital')->info('group name: ' . $group['team_name']);

            // dd($group['list_sheet']);
            foreach ($group['list_sheet'] as $mkt) {
                try {
                    $dataSheet = $this->getDataSheetByName($mkt['sheet_name'], $group['link_id'], $fileGGJson);
                    $this->saveReportSheet($mkt, $dataSheet);  
                } catch (\Exception $e) {
                    // dd($group);
                    dd($e);
                }
            }
        }
    }

    public function saveReportSheet($mkt, $dataSheet)
    {
        $id = $mkt['mkt_id'];
        foreach ($dataSheet as $value) {
            $dateStr = $value['date'];
            $date = date("Y-m-d", strtotime($dateStr));
            $dateInt = strtotime($date);
            $rp = $this->findReportDigital($id, $dateInt);
            
            if (!$rp) {
                $rp = new ReportDigital();
                $rp->date_str = $dateStr;
                $rp->date_int = $dateInt;
                $rp->created_at = $dateStr;
                $rp->save();
            }

            try {
                $rpDetail = new ReportDetailDigital();
                $rpDetail->value = $value['value'];
                $rpDetail->report_mkt_id = $rp->id;
                $rpDetail->digital_id = $id;
                $rpDetail->save();
            } catch (\Exception $e) {
                // dd($group);
                dd($rp);
            }
        }
    }

    public function findReportDigital($mktId, $dateInt)
    {  
        // $reportMkt = ReportDigital::leftJoin('report_mkt_detail', 'report_mkt.id', '=', 'report_mkt_detail.report_mkt_id')
        //     ->where('report_mkt.date_int', $dateInt)->where('report_mkt_detail.digital_id', $mktId)->first();
        
        $reportMkt = ReportDigital::where('report_mkt.date_int', $dateInt)
            ->first();

        return $reportMkt;
    }

    public function prepareData()
    {
        $result = [
            [
                'link_id' => '1iHKQKpBhv_dedskhwITSa7xMxO2ykAxlBcTe423la5E',
                'team_name' => 'tricho',
                'month' => '8',
                'list_sheet' => [
                    [
                        'sheet_name' => 'Tiến Anh',
                        'mkt_id' => 81
                    ],
                    [
                        'sheet_name' => 'Luyến',
                        'mkt_id' => 102
                    ],
                    [
                        'sheet_name' => 'Hiền',
                        'mkt_id' => 111
                    ],
                    [
                        'sheet_name' => 'Duyên',
                        'mkt_id' => 106
                    ],
                    [
                        'sheet_name' => 'Đức thắng',
                        'mkt_id' => 127
                    ],
                    [
                        'sheet_name' => 'Đạt',
                        'mkt_id' => 105
                    ]
                ]
            ],
            [
                'link_id' => '1aX12I00Kfj4i903VMzbkN-kwJ5UUN82byyn7v1AFdbA',
                'team_name' => 'npk-nhi',
                'month' => '8',
                'list_sheet' => [
                    [
                        'sheet_name' => 'Nhi',
                        'mkt_id' => 80
                    ],
                    
                ]
            ],
            [
                'link_id' => '1HdtkWwO0PZ63_FOyzsHQcvu6UbN9MKTgppKW5tqoAfQ',
                'team_name' => 'npk-no-nhi',
                'month' => '8',
                'list_sheet' => [
                    [
                        'sheet_name' => 'Ngân',
                        'mkt_id' => 138
                    ],
                    [
                        'sheet_name' => 'Huyền',
                        'mkt_id' => 68
                    ],
                    [
                        'sheet_name' => 'Chi',
                        'mkt_id' => 113
                    ],
                    [
                        'sheet_name' => 'A.Trung',
                        'mkt_id' => 120
                    ],
                    [
                        'sheet_name' => 'A.Sơn',
                        'mkt_id' => 122
                    ],
                    [
                        'sheet_name' => 'Trang',
                        'mkt_id' => 115
                    ],
                    [
                        'sheet_name' => 'Ngọc',
                        'mkt_id' => 121
                    ],
                    [
                        'sheet_name' => 'Thu',
                        'mkt_id' => 118
                    ],
                    [
                        'sheet_name' => 'Hiếu',
                        'mkt_id' => 117
                    ],
                    [
                        'sheet_name' => 'Đạt',
                        'mkt_id' => 126
                    ],
                    [
                        'sheet_name' => 'Sang',
                        'mkt_id' => 150
                    ],
                    [
                        'sheet_name' => 'Quân',
                        'mkt_id' => 157
                    ]
                ]
            ]
        ];

        return $result;
    }

    public function getDataSheetByName($sheetName, $sheetKey, $fileGGJson) 
    {
        $rs = [];
        $client = new Client();
        $client->setAuthConfig(storage_path($fileGGJson));
        $client->addScope(Sheets::SPREADSHEETS);

        $service = new Sheets($client);
        ////
        // $tmp = $this->t($service, $sheetKey);
        // dd($tmp);
        /////
        $rangeSheet = $sheetName . '!A6:C37';
        $response = $service->spreadsheets_values->get($sheetKey, $rangeSheet);

        $data = $response->getValues();
        Log::channel('report_digital')->info('$rangeSheet: ' . $rangeSheet);

        foreach ($data as $k => $value) {
            
            if ($value[1] == '') { //ko xác định đc thứ mấy trong tuần
                continue;
            }
            Log::channel('report_digital')->info('value: ' .json_encode($rangeSheet));
            preg_match('/(\d+)-thg (\d+)/', $value[0], $matches);
            $day   = $matches[1]; // 1
            $month = $matches[2]; // 8
            $year  = date('Y');   // Lấy năm hiện tại hoặc bạn set cố định
            $date = Carbon::createFromDate($year, $month, $day);

            $rs[] = [
                'date' => $date->format('Y-m-d'),
                'value' => $value[2]
            ];
        }

        return $rs;
    }

    public function t($service, $key)
    {
       $spreadsheet = $service->spreadsheets->get($key);
        $sheets = [];
        foreach ($spreadsheet->getSheets() as $sheet) {
            $properties = $sheet->getProperties();
            $sheets[] = [
                'title'   => $properties->getTitle(),   // sheet name (tab name)
                'sheetId' => $properties->getSheetId(), // numeric gid
                'index'   => $properties->getIndex(),   // position in workbook
            ];
        }

        dd($sheets);
    }
}
