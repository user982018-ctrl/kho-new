var baseLink = location.href.slice(0,location.href.lastIndexOf("/"));
$('.removeBtn').click(function (event) {
    if (confirm('Bạn muốn xóa data nàiiiy?')) {
        var id = $(this).data("id");
        // var baseLink = location.href.slice(0,location.href.lastIndexOf("/"));
        var link = baseLink + '/xoa-sale-care/' + id;
        var _token   = $("input[name='_token']").val();
        console.log(id)
        console.log(link)
        
        $('.body').css("opacity", '0.5');
        $('.loader').show();
        $.ajax({
            url: link,
            type: "POST",
            data: {
                id,
                _token: _token,
            },
            success: function (data) {
                $('.body').css("opacity", '1');
                
                if (!data.error) {
                    $('#notify-modal').modal('show');
                    if ($('.modal-backdrop-notify').length === 0) {
                        $('.modal-backdrop').addClass('modal-backdrop-notify');
                    }

                    $('#notify-modal .modal-title').html('Xoá data thành công!');

                    setTimeout(function() {
                        $('#notify-modal .modal-title').text('');
                        $('#notify-modal').modal("hide");
                    }, 2000);
                    
                    var tr = '.tr_' + id;
                    $(tr).delay(1000).hide(0);
                } else {
                    alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
                }

                $('.loader').hide();
            }
        });
    }
});

$('.update-assign-TN-sale').click(function(){
    $('.body').css("opacity", '0.5');
    $('.loader').show();
    var id = $(this).data("id");
    var textArea = "select[name='assignTNSale_" + id + "']";
    var assignSale  = $(textArea).val();
    var _token   = $("input[name='_token']").val();
    var link = baseLink + '/cap-nhat-assign-TNcan';
    $.ajax({
        url: link,
        type: 'POST',
        data: {
            _token: _token,
            id,
            assignSale
        },
        success: function(data) {
            $('.body').css("opacity", '1');
            var tr = '.tr_' + id;
            if (!data.error) {
                $('#notify-modal').modal('show');
                if ($('.modal-backdrop-notify').length === 0) {
                    $('.modal-backdrop').addClass('modal-backdrop-notify');
                }

                $('#notify-modal .modal-title').text('Cập nhật data thành công!');

                setTimeout(function() {
                    $('#notify-modal .modal-title').text('')
                    $('#notify-modal').modal("hide");
                }, 2000);
            } else {
                alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
            }
            $('.loader').hide();
        }
    });
});

$('.orderModal').on('click', function () {
    var idOrderNew = $(this).data('id_order_new');
    var TNSaleId = $(this).data('tnsale-id');
    console.log(TNSaleId);
    if (idOrderNew) {
        // var link = "{{URL::to('/update-order/')}}";
        var link = baseLink + '/update-order';
        $("#createOrder iframe").attr("src", link + '/' + idOrderNew);
    } else {
        var phone = $(this).data('phone');
        var name = $(this).data('name');
        var address = $(this).data('address');

        var param = 'saleCareId=' + TNSaleId + '&phone=' + phone + '&name=' + name + '&address=' + address ;
        var link = baseLink + '/them-don-hang/';
        $("#createOrder iframe").attr("src", link + '?' + param);

        //cập nhật TN Sale
        (function( $ ){
        $.fn.getIdOrderNew = function() {
            console.log('aaaa')
            setTimeout(function() {
                var _token  = $("input[name='_token']").val();
                var link = baseLink + '/id-order-new-check';
                $.ajax({
                    url: "link",
                    type: 'POST',
                    data: {
                        _token: _token,
                        TNSaleId,
                    },
                    success: function(data) {
                        if (data.id_order_new) {
                            if ($('.tr_' + TNSaleId + ' .id-order-new a').length == 0) {
                                var td = $('.tr_' + TNSaleId + ' .id-order-new');
                                td.wrapInner('<a href="' + data.link + '">' + data.id_order_new + '</a>');

                                var aCreate = $('.tr_' + TNSaleId + ' td div a.orderModal');
                                aCreate.data('id_order_new',  data.id_order_new);
                                aCreate.attr('title', 'Sửa đơn');
                            }
                        
                        } 
                    }
                });
       
            }, 3000);
        }; 
        })( jQuery );

        $('#createOrder').on('click', function () {
            $.fn.getIdOrderNew();
        });
       

        $('#close-main').on('click', function () {
            $.fn.getIdOrderNew();
        });
    }
});

$('.TNHistoryModal').on('click', function () {
    var saleId = $(this).data('tnsale_id');
    var link = baseLink + '/sale-hien-thi-TN-box';
    $("#TNHistory iframe").attr("src", link + '/' + saleId);
});

$('.result-TN').on('change', function() {
    var  id = $(this).data("id");
    var value = this.value;
    var _token   = $("input[name='_token']").val();
    var link = baseLink + '/cap-nhat-ket-qua-TN';
    $('.body').css("opacity", '0.5');
    $('.loader').show();
    $.ajax({
        url: link,
        type: 'POST',
        data: {
            _token: _token,
            id,
            value
        },
        success: function(data) {
            $('.body').css("opacity", '1');
            var tr = '.tr_' + id;
            if (!data.error) {
                var trId = 'tr.tr_' + id;
                console.log(data.nextTN);
                if (data.classHasTN) {
                    $(trId + ' .type-TN span.fb').removeClass('ttgh7');
                } else {
                    $(trId + ' .type-TN span.fb').addClass('ttgh7');
                }

                $(trId + ' .next-TN').text(data.nextTN);
                
                $('#notify-modal').modal('show');
                if ($('.modal-backdrop-notify').length === 0) {
                    $('.modal-backdrop').addClass('modal-backdrop-notify');  
                } 

                $('#notify-modal .modal-title').text('Cập nhật data thành công!');

                setTimeout(function() {
                    $('#notify-modal .modal-title').text('');
                    $('#notify-modal').modal("hide");
                }, 2000);
            } else {
                alert('Đã xảy ra lỗi trong quá trình cập nhật TN Sale!');
            }
            $('.loader').hide();
        }
    });
});

$('.TNModal').on('click', function () {
    var saleId = $(this).data('tnsale_id');
    var link = baseLink + '/sale-view-luu-TN-box';
    $("#TN iframe").attr("src", link + '/' + saleId);
});

$('.duplicate').on('click', function () {
    var phone = $(this).data('phone');
    var link = baseLink + '/danh-sach-so-trung';
    $("#listDuplicate iframe").attr("src", link + '/' + phone);
});