    <link href="{{ asset('public/css/pages/notify.css'); }}" rel="stylesheet">
@extends('layouts.default')
@section('content')
<style>
  body {
      font-family: Arial, sans-serif;
      background: #f9fafb;
      color: #333;
      margin: 0;
    }
    .container {
      max-width: 900px;
      margin: 20px auto;
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
     h2 {
      margin-top: 0;
      color: #2c3e50;
    }
    label {
      font-weight: bold;
      margin-bottom: 5px;
      display: block;
    }
     input, select {
      padding: 8px 10px;
      margin-top: 5px;
      margin-bottom: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
      width: 100%;
      box-sizing: border-box;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }
    th {
      background: #f1f5f9;
    }
    .btn {
      cursor: pointer;
      border: none;
      font-size: 14px;
    }
    .btn-primary {
      background: #3498db;
      color: white;
    }
    .btn-danger {
      background: #e74c3c;
      color: white;
    }
    .btn-success {
      background: #2ecc71;
      color: white;
      font-size: 16px;
      padding: 10px 20px;
    }
    .btn-sm {
      padding: 6px 12px;
      font-size: 13px;
    }
    .actions {
      text-align: right;
    }
    #laravel-notify .notify {
      z-index: 1030;
    }
</style>
<div class="container">
    <h2>Thêm Combo Sản Phẩm</h2>

    <!-- Form -->
    <form method="post" action="{{route('save-combo')}}">
        @csrf
      <label for="name">Tên combo</label>
      <input required type="text" id="name" name="name" placeholder="Nhập tên combo">

      <label for="price">Giá combo</label>
      <input required type="number" id="price" name="price" placeholder="Nhập giá combo">

      <h3>Sản phẩm trong combo</h3>
      <table id="comboItemsTable">
        <thead>
          <tr>
            <th>Sản phẩm</th>
            <th>Số lượng</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <!-- JS sẽ thêm row ở đây -->
        </tbody>
      </table>

      <button type="button" class="btn btn-primary btn-sm" onclick="addRow()">+ Thêm sản phẩm</button>

      <div class="actions">
        <button type="submit" class="btn btn-success">Lưu Combo</button>
      </div>
    </form>
  </div>

  <script>
    function addRow() {
        let productsJson = '<?php echo $products; ?>';
        let products = JSON.parse(productsJson);
        var options = ''
        products.forEach(product => {
            options += `<option value=" ${product.id}">${product.name}</option>`
        });
        console.log(options)
      let row = `
        <tr>
          <td>
            <select name="products[]">
              ${options}
            </select>
          </td>
          <td>
            <input type="number" name="quantities[]" value="1" min="1">
          </td>
          <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Xóa</button>
          </td>
        </tr>
      `;
      document.querySelector('#comboItemsTable tbody')
              .insertAdjacentHTML('beforeend', row);
    }
  </script>
  <script type="text/javascript" src="{{ asset('public/js/notify.js'); }}"></script>
@stop