<?php 
  $note = $id = $imgs = '';
  if ($history) {
    $note = $history->note;
    $id = $history->id;
    $imgs = $history->img;
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head> @include('includes.head')  </head>
  <body>
    @include('notify::components.notify')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> --}}
    <script type="text/javascript" src="{{asset('public/js/moment.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('public/css/daterangepicker.css')}}" /> 

    <style>
      .notify {
        z-index: 99 !important;
      }
      .comment {
        padding: 10px;
        background-color: #ddd;
        margin: 10px 0;
      }

      .bottom-tab {
        padding: 10px 0;
        background: #fff;
        width: 90%;
        bottom: 0;
        position: fixed;
      }
      .bottom-content {
        display: flex;
        width: 100%;
        align-items: baseline;
        justify-content: space-between;
        color: #fff;
       
      }
      .bottom-tab-right {
        right: 10px;
      }
      .bottom-tab-left {
        left: 10px;
      }

      .back {
        background-color: yellow;
        color:#000;
        border: none;
      }
      .refresh, .back {
        margin-left: 10px;
      }
      .list-images {
        width: 50%;
        margin-top: 20px;
        display: inline-block;
    }
    .hidden { display: none !important; }
    .box-image {
        width: 100px;
        height: 108px;
        position: relative;
        float: left;
        margin-left: 5px;
    }
    .box-image img {
        width: 100px;
        height: 100px;
    }
    .wrap-btn-delete {
        position: absolute;
        top: -8px;
        right: 0;
        height: 2px;
        font-size: 20px;
        font-weight: bold;
        color: red;
    }
    .btn-delete-image {
        cursor: pointer;
    }
    .table {
        width: 15%;
    }
    </style>
    <h6 style="padding: 10px; text-align: center">{{$saleCare->full_name}} - {{$saleCare->phone}}</h6>
    <div class="row" style="margin:0 20px;">
      
      <form method="POST" action="{{route('save-box-TN')}}" enctype="multipart/form-data"> 
          {{ csrf_field() }}
          <input type="hidden" name="id" value="{{$id}}">
          <input value="{{$saleId}}" class="hidden form-control" name="sale_id">
          <div class="mb-3">
              <textarea data-id="{{$saleCare->id}}" autofocus title="Không thêm hình => ko cần nhấn lưu" required name="note" class="form-control note" id="note_{{$saleCare->id}}" rows="10">{{$note}}</textarea>
          </div>
          <div class="mb-3">
            <div class="input-group hdtuto control-group lst increment" >
              <div class="list-input-hidden-upload">
                  <input type="file" name="filenames[]" id="file_upload" class="myfrm form-control hidden">
              </div>
              <div class="input-group-btn"> 
                  <button class="btn btn-success btn-add-image" type="button"><i class="fldemo glyphicon glyphicon-plus"></i>+Thêm hình</button>
                  <span class="err-upload" style="color: red;"></span>
                </div>
            </div>
            <div class="list-images">
              @if (isset($imgs) && !empty($imgs))
                @foreach (json_decode($imgs) as $key => $img)
                  <div class="box-image">
                      <input type="hidden" name="images_uploaded[]" value="{{ $img }}" id="img-{{ $key }}">
                      <img src="{{ asset('public/files/'.$img) }}" class="picture-box">
                      <div class="wrap-btn-delete"><span data-id="img-{{ $key }}" class="btn-delete-image">x</span></div>
                  </div>
                @endforeach
                <input type="hidden" name="images_uploaded_origin" value="{{ $imgs }}">
              @endif
            </div>
          </div>

          <div style="padding-bottom: 10px; height:100%;">
            {{-- <pre style="max-height: 30vh;"> --}}
              @if ($listHistory)
              @foreach ($listHistory as $his)
            <div >{{ date_format($his->created_at,"d/m")}} <span id="id_his_{{$his->id}}">{{$his->note }} </span></div>
              @endforeach
              @endif
            {{-- </pre> --}}
            {{$saleCare->TN_can}}
          </div>

          <div class="bottom-tab">
            <div class="text-right bottom-content">
              <div><button type="submit" id="submit" class="btn btn-primary">Lưu</button></div>
              <div><button type="button" class="back btn">Quay lại</button>
                <button type="button" class="refresh btn btn-info">Tải lại</button>
              </div>
            </div>
          </div>
          
      </form>
    <div id="loader-overlay">
      <div class="loader"></div>
    </div>
  </div>
    <script type="text/javascript" src="{{asset('public/js/dateRangePicker/daterangepicker.min.js')}}"></script>
    <script src="https://use.fontawesome.com/fe459689b4.js"></script>
    @include('includes.foot')

    <script>
      

    $(document).ready(function() {
      // $("#laravel-notify").slideDown('fast').delay(3000).hide(0);
      
      $('.refresh').click(function() {
        $('#loader-overlay').css('diplay', 'flex');
        location.reload(true)
      });
      $('.back').click(function() {
        $('#loader-overlay').css('diplay', 'flex');
            // Your code here!
            history.back()
      });

      $('#submit').on( "click", function() {
        $('#loader-overlay').css('display', 'flex');
      });

      $(".btn-add-image").click(function(){
        $('.err-upload').html('');
          $('#file_upload').trigger('click');
      });

      // $(".err-upload.txt").slideDown('fast').delay(3000).hide(0);
      
      $('.list-input-hidden-upload').on('change', '#file_upload', function(event){
          let today = new Date();
          let time = today.getTime();
          let image = event.target.files[0];
          let file_name = event.target.files[0].name;
          
          var extension = file_name.substring(
          file_name.lastIndexOf('.') + 1).toLowerCase();

          let allowed = ['jpg', 'png', 'jpeg', 'svg', 'gif'];
          console.log(extension)
          if (!allowed.includes(extension)) {
            
            $('.err-upload').html('Sai định dạng hình ảnh! Chỉ cho phép: jpg, png, jpeg, svg, gif');
            location.reload(true)
            return;
          }

          size = event.target.files[0].size;
          if(size > 1024 * 1024 * 2) {
            alert("Chỉ cho phép tải tệp tin nhỏ hơn 2MB");
            location.reload(true)
            return;
          }

          let box_image = $('<div class="box-image"></div>');
          box_image.append('<img src="' + URL.createObjectURL(image) + '" class="picture-box">');
          box_image.append('<div class="wrap-btn-delete"><span data-id='+time+' class="btn-delete-image">x</span></div>');
          $(".list-images").append(box_image);

          $(this).removeAttr('id');
          $(this).attr( 'id', time);
          let input_type_file = '<input type="file" name="filenames[]" id="file_upload" class="myfrm form-control hidden">';
          $('.list-input-hidden-upload').append(input_type_file);
          
      });

      $(".list-images").on('click', '.btn-delete-image', function(){
          let id = $(this).data('id');
          $('#'+id).remove();
          $(this).parents('.box-image').remove();
      });
    });

    $.fn.myFunc = function(id, type){
        if (type == 1) {
          $('#loader-overlay').css('diplay', 'flex');
        }
        
        // var id = $(this).data("id");
        var textArea = '#note_' + id;
        var textTN   = $(textArea).val();
        var _token   = $("input[name='_token']").val();
        
        var idHis = $('input[name="id"]').val()
        $('#id_his_' + idHis).html(textTN)

        $.ajax({
            url: "{{route('update-salecare-TNcan')}}",
            type: 'POST',
            data: {
                _token: _token,
                id,
                textTN
            },
            success: function(data) {
                if (type == 1) {
                    var tr = '.tr_' + id;
                    if (!data.error) {
                        $('#notify-modal').modal('show');
                        if ($('.modal-backdrop-notify').length === 0) {
                            $('.modal-backdrop').addClass('modal-backdrop-notify');
                        }

                        $(tr).addClass('success');
                        setTimeout(function() { 
                            $('#notify-modal').modal("hide");
                            $(tr).removeClass('success');
                        }, 2000);
                    } else {
                        alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                        $(tr).addClass('error');
                        setTimeout(function() { 
                            $(tr).removeClass('error');
                        }, 3000);
                    }
                    $('#loader-overlay').css('diplay', 'none');
                }
            }
        });
    }

    // $(".note").keyup(function(){
    //     var id = $(this).data("id");
    //     var type = 2
    //     $('.body').myFunc(id, type); 
    // });
    </script>
  </body>
</html>