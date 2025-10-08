<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>In đơn hàng</title>
  <style>
    body {
      zoom: 1.2; /* phóng to toàn bộ */
      font-family: Arial, sans-serif;
    }

    /* .styles_productItem__name__u0VlF:first-child, */
    .styles_productItem__u8zzn:first-child {
        font-size: 16px;
    }
    .styles_productItem__u8zzn {
      font-size: 30px;
      font-weight: bold;
      line-height: 30px;
      /* font-style: italic; */
    }
    /* .borderBottom .styles_leftContent__OgniN .borderBottom.flex.items-center.justify-between
    .borderBottom .styles_leftContent__OgniN .shrink-0.box-border
    {
        display: none !important;
        visibility: hidden !important;
    }  */

    .borderBottom .styles_leftContent__OgniN .shrink-0.box-border.borderRight{
        width: 100%;
    }

    @media print {
        /* body .borderBottom .styles_leftContent__OgniN .borderBottom.flex.items-center.justify-between
        .borderBottom .styles_leftContent__OgniN .shrink-0.box-border
        {
            display: none !important;
            visibility: hidden !important;
        } */
    }

    
    /* .styles_page__1R3D9 .flex.text-xs.overflow-hidden .borderRight
    {
        display: block !important;
    } */
    /* .styles_page__1R3D9 .flex.text-xs.overflow-hidden .shrink-0.box-border.w-1\/2 */
    /* .styles_page__1R3D9 .flex.text-xs.overflow-hidden :nth-of-type(2)
    {
        display: none !important;
    } */

    .styles_productItem__u8zzn {
        display: flex;
        justify-content: space-between;
    }
    .style_page__KlaoL{
        padding: .75rem;
    }

    .style_printItem__UXtq8 {
        margin-bottom: 2.25rem;
        border: 1px solid #000;
    }

    
    /* .style_a5Landscape__mBTBx, .style_a5Portrait__qUm6_, .style_a6Landscape__OogE2, .style_a6Portrait__sCzPH {
        display: none;
    }
    .style_a5Landscape__mBTBx {
        display: block;
    } */

  </style>
</head>
<body>
  {{-- render lại nội dung HTML gốc --}}
  {!! $html !!}

  <script>
    // Ví dụ: tự động thêm class vào tên sản phẩm
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".product-name-selector").forEach(el => {
            el.classList.add("product-name");
        });
    });
  </script>

<script>
//     setTimeout(() => {
//   window.print();
// }, 500); 
// document.querySelectorAll(".styles_page__1R3D9 .flex.text-xs.overflow-hidden .shrink-0.box-border")[0].style.width = "100%";
document.querySelectorAll(".styles_page__1R3D9 .flex.text-xs.overflow-hidden .shrink-0.box-border").forEach(el => {
    el.style.width = "100%";
    // el.style.display = "none";
});


// document.querySelectorAll(".styles_page__1R3D9 .flex.text-xs.overflow-hidden .shrink-0.box-border")[1].style.display = "none";
</script>
</body>
</html>
