<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
       .filter {
  font-family: sans-serif;
  margin-bottom: 20px;
}
button {
  border: 1px solid #ccc;
  padding: 8px 15px;
  margin: 5px;
  cursor: pointer;
  border-radius: 6px;
  background: #f9f9f9;
  transition: 0.2s;
}
button:hover {
  background: #eee;
}
button.active {
  border-color: #333;
  background: #ddd;
  font-weight: bold;
}
#result {
  font-size: 18px;
  font-weight: bold;
  color: #444;
}

      font-weight: lighter;
    }
    a{
      color:white;
      font-size:15px;
      padding: 0 30px

    }
  }
}
#notFound {
  position: fixed;
  top:50%;
  left:50%;
  transform:translateY(-50%) translateX(-50%) scale(1.2);
  width:80%;
  height:auto;
  
}
    </style>
</head>
<body>
   <div class="filter">
  <h3>Chọn màu</h3>
  <div class="colors">
    <button class="color" data-color="red">🔴 Đỏ</button>
    <button class="color" data-color="blue">🔵 Xanh</button>
    <button class="color" data-color="green">🟢 Xanh lá</button>
  </div>

  <h3>Chọn size</h3>
  <div class="sizes">
    <button class="size" data-size="S">S</button>
    <button class="size" data-size="M">M</button>
    <button class="size" data-size="L">L</button>
  </div>
</div>

<hr>

<div id="result"></div>
<script>
const result = document.getElementById("result");

let selectedColor = null;
let selectedSize = null;

document.querySelectorAll(".color").forEach(btn => {
  btn.addEventListener("click", () => {
    // remove active from others
    document.querySelectorAll(".color").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    selectedColor = btn.dataset.color;
    updateResult();
  });
});

document.querySelectorAll(".size").forEach(btn => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".size").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    selectedSize = btn.dataset.size;
    updateResult();
  });
});

function updateResult() {
  if (selectedColor && selectedSize) {
    result.textContent = `Bạn đã chọn: ${selectedColor} - Size ${selectedSize}`;
  } else if (selectedColor) {
    result.textContent = `Bạn đã chọn: ${selectedColor}`;
  } else if (selectedSize) {
    result.textContent = `Bạn đã chọn: Size ${selectedSize}`;
  } else {
    result.textContent = "Vui lòng chọn biến thể";
  }
}
</script>

</body>
</html>