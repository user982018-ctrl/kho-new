@extends('layouts.default')
@section('content')

<style>
  .form-group {
    margin-bottom: 15px;
  }
  .help-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
  }
  .file-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s;
  }
  .file-upload-area:hover {
    border-color: #007bff;
    background: #e7f3ff;
  }
  .file-upload-area.dragover {
    border-color: #007bff;
    background: #e7f3ff;
  }
  .file-name {
    margin-top: 10px;
    font-weight: bold;
    color: #28a745;
  }
  .file-info {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
  }
</style>

<div class="body flex-grow-1 px-3">
  <div class="container-lg">
    <div class="row">
      <div id="notifi-box" class="hidden alert alert-success print-error-msg">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
      </div>

      <div class="col-12">
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ session('error') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif

        <div class="card mb-4">
          <div class="card-header">
            <strong>Xuất Report GHN từ File Excel</strong>
          </div>
          <div class="card-body">
            <form id="reportGHNForm" method="POST" action="{{ route('report-ghn') }}" enctype="multipart/form-data">
              {{ csrf_field() }}
              
              <div class="row">
                <div class="col-md-12 form-group">
                  <label class="form-label" for="excel_file">
                    Chọn file Excel/CSV <span class="text-danger">*</span>
                  </label>
                  <div class="file-upload-area" id="fileUploadArea">
                    <i class="fa fa-cloud-upload" style="font-size: 48px; color: #6c757d; margin-bottom: 10px;"></i>
                    <p style="margin: 10px 0;">
                      <strong>Kéo thả file vào đây</strong> hoặc <strong>click để chọn file</strong>
                    </p>
                    <input 
                      required 
                      type="file" 
                      name="excel_file" 
                      id="excel_file" 
                      accept=".xlsx,.xls,.csv"
                      style="display: none;"
                    >
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('excel_file').click()">
                      <i class="fa fa-folder-open"></i> Chọn File
                    </button>
                    <div id="fileName" class="file-name" style="display: none;"></div>
                    <div id="fileInfo" class="file-info"></div>
                  </div>
                  <div class="help-text">
                    Hỗ trợ định dạng: .xlsx, .xls, .csv
                  </div>
                  <p class="error_msg text-danger" id="file_error"></p>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="form-label" for="file_prefix">Tên file tuỳ chỉnh</label>
                  <input 
                    class="form-control" 
                    name="file_prefix" 
                    id="file_prefix" 
                    type="text" 
                    placeholder="Ví dụ: GHN_thang_10"
                    value="{{ old('file_prefix') }}"
                  >
                  <div class="help-text">
                    Nếu nhập, tên file sẽ có dạng: [Tên này]_[timestamp].xlsx. Nếu để trống sẽ dùng tên mặc định.
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 form-group">
                  <button type="submit" id="submitBtn" class="btn btn-primary">
                    <i class="fa fa-download"></i> Xuất File Excel
                  </button>
                  <button type="button" id="resetBtn" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  var fileInput = $('#excel_file');
  var fileUploadArea = $('#fileUploadArea');
  var fileName = $('#fileName');
  var fileInfo = $('#fileInfo');
  var isProcessing = false;
  var submitBtn = $('#submitBtn');
  var submitBtnLabel = submitBtn.html();
  var hasValidFile = false;

  // Disable submit until file được chọn
  submitBtn.prop('disabled', true);

  // Click to select file - chỉ trigger khi click vào vùng upload, không phải button
  fileUploadArea.on('click', function(e) {
    // Ngăn event bubble và chỉ trigger khi click vào vùng upload
    if ($(e.target).is('button') || $(e.target).closest('button').length > 0) {
      return;
    }
    if ($(e.target).is('input[type="file"]')) {
      return;
    }
    e.stopPropagation();
    if (!isProcessing) {
      fileInput[0].click();
    }
  });

  // Ngăn event bubble từ file input
  fileInput.on('click', function(e) {
    e.stopPropagation();
  });

  // File selected
  fileInput.off('change').on('change', function(e) {
    if (isProcessing) return;
    
    var file = e.target.files[0];
    if (file) {
      isProcessing = true;
      var fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
      fileName.text(file.name).show();
      fileInfo.text('Kích thước: ' + fileSize + ' MB');
      $('#file_error').text('');
      hasValidFile = true;
      submitBtn.prop('disabled', false);
      isProcessing = false;
    } else {
      hasValidFile = false;
      submitBtn.prop('disabled', true);
    }
  });

  // Drag and drop
  fileUploadArea.off('dragover dragleave drop').on('dragover', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).addClass('dragover');
  });

  fileUploadArea.on('dragleave', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).removeClass('dragover');
  });

  fileUploadArea.on('drop', function(e) {
    e.preventDefault();
    e.stopPropagation();
    $(this).removeClass('dragover');
    
    var files = e.originalEvent.dataTransfer.files;
    if (files.length > 0 && !isProcessing) {
      isProcessing = true;
      fileInput[0].files = files;
      // Trigger change event manually
      fileInput.trigger('change');
      setTimeout(function() {
        isProcessing = false;
      }, 100);
    }
  });

  // Reset form
  $("#resetBtn").off('click').on('click', function(e) {
    e.preventDefault();
    $("#reportGHNForm")[0].reset();
    fileInput.val('');
    fileName.hide();
    fileInfo.text('');
    $('#file_error').text('');
    isProcessing = false;
    hasValidFile = false;
    submitBtn.prop('disabled', true);
  });

  // Validate và submit form
  $("#reportGHNForm").off('submit').on('submit', function(e) {
    var file = fileInput[0].files[0];
    
    if (!file) {
      e.preventDefault();
      $("#file_error").text('Vui lòng chọn file');
      submitBtn.prop('disabled', true);
      return false;
    }

    // Validate file type
    var allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                        'application/vnd.ms-excel', 
                        'text/csv',
                        'application/csv'];
    var fileType = file.type;
    
    // Check by extension if type is empty
    if (!fileType) {
      var ext = file.name.split('.').pop().toLowerCase();
      if (!['xlsx', 'xls', 'csv'].includes(ext)) {
        e.preventDefault();
        $("#file_error").text('Định dạng file không hợp lệ. Chỉ chấp nhận .xlsx, .xls, .csv');
        return false;
      }
    }

    // Show loading
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...');

    // Tự động refresh trang sau khi submit để form về trạng thái ban đầu
    // setTimeout(function() {
    //   submitBtn.prop('disabled', false).html(submitBtnLabel);
    //   window.location.reload();
    // }, 6000);
  });
});
</script>

@stop
