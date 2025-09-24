<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Phiếu In 80x80</title>

    <style type="text/css">
        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        body {
            font-family: "Arial";
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }

        @media print {
            @page {
                size: 80mm 80mm;
                margin-top: 0;
                margin-right: 1rem;
                margin-bottom: 0;
                margin-left: 0;
                min-height: auto;
            }

            html,
            .content {
                width: 80mm;
                height: 85mm;
                content: ".";
            }
        }

        .content.page-break {
            display: block;
            page-break-after: always;
        }

        .content {
            width: 80mm;
            height: 85mm;
            background-color: white;
            position: relative;
            margin: 4px;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .line-clamp-4 {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
        }

        .line-clamp-5 {
            display: -webkit-box;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
        }

        .line-clamp-6 {
            display: -webkit-box;
            -webkit-line-clamp: 6;
            -webkit-box-orient: vertical;
        }

        .module {
            overflow: hidden;
        }

        .line {
            line-height: 12px;
        }

        .items :last-child {
            border-bottom: 1px dotted;
        }

        #process-bar {
            margin: auto;
            position: fixed;
            z-index: 1000;
            width: 98%;
            height: 8px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .w3-light-grey,
        .w3-hover-light-grey:hover,
        .w3-light-gray,
        .w3-hover-light-gray:hover {
            color: #000 !important;
            background-color: #f1f1f1 !important;
        }

        .w3-round,
        .w3-round-medium {
            border-radius: 4px;
        }

        .w3-round-large {
            border-radius: 8px;
        }

        .w3-green,
        .w3-hover-green:hover {
            color: #ff8328 !important;
            background-color: #ff8328 !important;
            height: 8px;
            font-size: 1px;
        }
    </style>
</head>

<div id="process-bar" class="w3-light-grey w3-round">
    <div id="myBar" class="w3-container w3-green w3-round-large" style="width: 0%">background</div>
    <div id="value-percent"
        style="text-align: center; font-size: 12px; margin-top: 2px; color: #ff8328; font-weight: bold">
        Đang tải 0%
    </div>
</div>

<script>
    function processBar(stt, total) {
        elem = document.getElementById("myBar")
        value = document.getElementById("value-percent")
        if (elem) {
            if (stt == total) {
                elem.style.width = 100 + "%"
                value.innerHTML = "Đã tải " + 100 + "%"
                document.getElementById("process-bar").remove()

                window.print()
            } else {
                width = ((stt / total) * 100).toFixed(0)
                elem.style.width = width + "%"
                value.innerHTML = "Đang tải " + width * 1 + "%"
            }
        }
    }
</script>
<?php 

// dd($list);

// 
?>
@foreach ($list as $orderTracking)
<?php
$i = 1;

$items = $orderTracking['items'];
$count = count($items);
$data = $orderTracking;
// dd($items);
?>
    @if ($count > 0)
    @foreach ($items as $item)
    <div class="content page-break line" style="font-weight: bold; position: relative">
        <div style="padding: 2px; display: flex; align-items: center; font-size: 10px">

            <div style="width: 35%; border-right: 1px dotted; padding-right: 4px">
                <div class="module line-clamp-2">Phân Bón Miền Nam</div>
                <div style="display: flex; align-items: center;">

                    <div style="width: 30%; height: 20px; margin-right: 4px;">
                        <img style="width: 100%; height: 100%; background: black;padding: 4px; border-radius: 1px;" src="https://dev-online-gateway.ghn.vn/file/public-api/files/get?file_id=649149d53783f66d4f38d034" />
                    </div>
                    <div style="font-size: 14px;">CK</div>

                </div>
            </div>

            <div style="padding-left: 4px; width: 65%">
                <div style="display: flex; align-items: center; justify-content: space-between">
                    <div class="module line-clamp-2">{{$data['to_name']}}</div>
                    <div></div>
                </div>
                <div class="module line-clamp-2">{{$data['to_address']}}</div>
            </div>
        </div>
        <div style="padding: 2px; border-bottom: 1px dotted">
            <img
                width="100%"
                height="auto"
                src="https://online-gateway.ghn.vn/barcode/api/v1/barcode/generate?code={{$item['item_order_code']}}&type=128A&width=820&height=150"
                atl="barcode"
                onload="processBar(parseInt('1'),'1')"
            />
            <div style="display: flex; justify-content:  space-between ; align-items: center; padding-top: 2px;">
                <div>

                    <div style="font-size: 16px"> @if ($data['service_type_id'] == 5)
                        <div style="font-size: 16px; font-weight: bold">
                            CPTT
                        </div>
                        @endif</div>

                    <div style="font-size: 8px; font-weight: 700;   ;">{{\Carbon\Carbon::parse($data['created_date'])->format('H:i d-m-Y')}}</div>
                </div>

                <div>
                    <div style="
                            font-size: 12px;
                            font-weight: bold;
                            display: flex;
                            justify-content: space-around;
                            padding-top: 2px;
                        ">
                        <div>{{$item['item_order_code']}}</div>
                    </div>

                </div>

                <div style="font-size: 12px">
                    <div style="font-size: 14px; border: 1px solid; padding: 1px;">{{$i}}/{{$count}}</div>

                </div>

            </div>
        </div>
        
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px dotted">

            <b style="height: 34px; text-align: center; font-size: 28px; width: 80%; display: flex; align-items: center; justify-content: center; ">

                <span>{{$data["sort_code"]}}</span>
            </b>

            <div style="height: 50px; width: 50px">
                <img
                    width="100%"
                    height="100%"
                    height="auto"
                    src="https://online-gateway.ghn.vn/barcode/api/v1/barcode/generate?code={{$item['item_order_code']}}&type=QR&width=200&height=200"
                    atl="barcode"
                />
            </div>
        </div>

        <span style="font-size: 12px; padding: 2px 0; border-bottom: 1px dotted" class="module">
            <span style="font-size: 10px">Thu:</span>
            <b class="mask">{{$data['cod_amount']}}</b>
            <span> Cho xem hàng, không cho thử</span>
        </span>

        <div class="items">
            <div style="
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 20px 0;
                font-weight: 500;
                font-size: 20px;
                ">
                <div style="line-height:20px; width: 90%; word-break: break-all; font-weight: bold" class="module">
                    {{$item['name']}}
                </div>
                <div style="border: none">{{$item['quantity']}}</div>
            </div>

        </div>
    </div>
    <?php $i++; ?>
    @endforeach
    @endif
@endforeach


<script>
    function maskNumber(num) {
      let str = num.toString();

      // lấy ký tự đầu tiên và ký tự thứ hai
      let first = str.charAt(0);
      let second = str.charAt(1);

      return first + "." + second + "***";
    }

    document.querySelectorAll(".mask").forEach(el => {
      let raw = el.textContent.trim();
      el.textContent = maskNumber(raw);
    });
  </script>