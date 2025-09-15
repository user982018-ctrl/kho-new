<style>

    .card {
      background: #fff;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      width: 410px;
    }

    .card-tool input {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 15px;
      font-size: 16px;
    }
    .card-tool button {
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        margin-bottom: 15px;
        font-size: 16px;
        background: #3498db;
        color: white;
        border: none;
        cursor: pointer;
    }
    .card-tool button:hover {
      background: #2980b9;
    }
    .card-tool .result {
      padding: 12px;
      border-radius: 8px;
      margin-top: 10px;
      display: none;
    }
    .card-tool .success {
      background: #eafaf1;
      color: #27ae60;
      border: 1px solid #27ae60;
    }
    .card-tool .error {
      background: #fdecea;
      color: #e74c3c;
      border: 1px solid #e74c3c;
    }
    .container-lg {
        align-items: center;
        display: flex;
        justify-content: space-around;
    }
</style>
    <div id="loader-overlay">
        <div class="loader"></div>
    </div>
  <div class="card card-tool">
    <h2>Nạp dữ liệu từ pancake</h2>
    <p>*ở pancake đã tìm thấy hoặc lọc được sđt nhưng data chưa về.</p>
    <input type="text" id="phone" placeholder="Nhập số điện thoại...">
    <button onclick="checkPhone()">Gửi</button>
    <div id="result" class="result"></div>
  </div>

  <script>
    async function checkPhone() {
        $('#loader-overlay').css('display', 'flex');
        const phone = document.getElementById('phone').value.trim();
        const resultBox = document.getElementById('result');

        if (!phone || phone.length > 10) {
            resultBox.style.display = 'block';
            resultBox.className = 'result error';
            resultBox.innerText = 'Vui lòng nhập số điện thoại, 10 ký tự.';
            $('#loader-overlay').css('display', 'none');
            return;
        }

        try {
            // Gọi API (ví dụ API giả định)
            
            let url = "{{ url('api/nap-du-lieu-pc/') }}" + "/" + encodeURIComponent(phone);
            let res = await fetch(url);
            let data = await res.json();

            if (data.error) {
                resultBox.style.display = 'block';
                resultBox.className = 'result error';
                resultBox.innerText = data.text;
                $('#loader-overlay').css('display', 'none');
                return;
            }
            // Hiển thị kết quả
            resultBox.style.display = 'block';
            resultBox.className = 'result success';
            resultBox.innerText = `${data.text}`;
            document.getElementById('phone').value = '';
            $('#loader-overlay').css('display', 'none');
        } catch (error) {
            $('#loader-overlay').css('display', 'none');
            resultBox.style.display = 'block';
            resultBox.className = 'result error';
            resultBox.innerText = 'Có lỗi xảy ra khi gọi API!';
        }
    }
  </script>