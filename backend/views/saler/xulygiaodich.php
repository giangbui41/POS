<?php
    include('backend/core/function.php');
?>

<link rel="stylesheet" href="frontend/css/transaction.css">

<section class="content-header">
    <h3>Xử lý giao dịch</h3>
</section>
<section class="content">
    <div class="container-fluid px-4">
        <div class="card-body">
            <div>
                <label>Tìm sản phẩm: </label>
                <input type="text" id="product_search" placeholder="Nhập tên hoặc mã vạch">
                <div id="suggestions"></div>
            </div>
            <div class="table-responsive mb-3">
                <table id="cart" class="table table-bordered table-striped">
                    <thead>
                        <tr><th>Tên SP</th><th>Ảnh SP</th><th>SL</th><th>Đơn giá</th><th>Tổng</th><th>Hành động</th></tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <p>Tổng cộng: <span id="total">0</span> đ</p>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="mb-0">Thông tin khách hàng</h4>
            </div>
            <div class="card-body">
                <div>
                    <label>SĐT khách hàng:</label>
                    <input type="text" id="phone">
                    <button onclick="checkCustomer()">Kiểm tra</button>
                    <div id="customer_info" style="display:none">
                        <p>Họ tên: <input id="name"></p>
                        <p>Địa chỉ: <input id="address"></p>
                    </div>
                </div>

                <div>
                    <p>Tiền khách đưa: <input id="cash"></p>
                    <p>Tiền trả lại: <span id="change">0</span> đ</p>
                    
                </div>
                <div>
                    <label>Chọn phương thức thanh toán</label>
                    <select id="payment_mode" class="form-select">
                        <option value="">--Chọn phương thức--</option>
                        <option value="Tiền mặt">Tiền mặt</option>
                        <option value="Chuyển khoản">Chuyển khoản</option>
                    </select>
                </div>
                <div>
                    <br/>
                    <button type="button"  class="btn btn-warning w-100 proceedToPlaceBtn">Tiến hành đặt hàng</button>

                </div>
            </div>
        </div>


    </div>
</section>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="frontend/css/admin.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let cart = [];

    // Vùng nhập sản phẩm, kích hoạt khi người dùng nhập gì đó vào thanh input
    document.getElementById('product_search').addEventListener('input', function() {
        let key = this.value.trim(); // Loại bỏ khoảng trắng đầu và cuối
        if (key.length < 2) {
            document.getElementById('suggestions').innerHTML = "";
            document.getElementById('suggestions').style.display = 'none';
            return;
        }

        fetch('saler/get_product?key=' + key)
            .then(res => res.json())
            .then(data => {
                let sug = document.getElementById('suggestions');
                sug.innerHTML = "";

                // Kiểm tra trường hợp barcode trùng khớp duy nhất
                const exactBarcodeMatch = data.filter(p => p.barcode === key);
                if (exactBarcodeMatch.length === 1) {
                    addToCart(exactBarcodeMatch[0]);
                    this.value = ""; // Xóa nội dung ô tìm kiếm
                    sug.style.display = 'none';
                    return; // Kết thúc hàm sau khi tự động thêm
                }

                // Xử lý trường hợp không trùng khớp duy nhất hoặc tìm kiếm theo tên
                if (data.length > 0) {
                    data.forEach(p => {
                        let div = document.createElement("div");
                        div.style.display = 'flex';
                        div.style.alignItems = 'center';

                        let image = document.createElement("img");
                        image.src = `${p.image}`;
                        image.style.width = '50px';
                        image.style.height = '50px';
                        image.style.marginRight = '10px';
                        div.appendChild(image);

                        let productInfo = document.createElement("span");
                        productInfo.textContent = `${p.name} (${p.barcode}) - ${Number(p.price).toLocaleString('vi-VN')} đ`;
                        div.appendChild(productInfo);

                        div.onclick = () => {
                            addToCart(p);
                            sug.style.display = 'none';
                            document.getElementById('product_search').value = '';
                        };

                        sug.appendChild(div);
                    });
                    sug.style.display = 'block';
                } else {
                    sug.style.display = 'none';
                }
            });
    });

    // Dùng khi người dùng nhấp vào div
    function addToCart(product) {
        let found = cart.find(p => p.id === product.id);
        if (found) found.qty++;
        else cart.push({...product, qty: 1, image: product.image}); // Thêm image vào cart

        renderCart();
    }

    // Cập nhật hiển thị của giỏ hàng
    function renderCart() {
        let tbody = document.querySelector('#cart tbody');
        tbody.innerHTML = "";
        let total = 0;
        cart.forEach((item, i) => {
            let row = `<tr>
                <td>
                    ${item.name}
                </td>
                <td><img style="width: 50px" src="${item.image}"></td>
                <td><input type='number' value='${item.qty}' onchange='updateQty(${i}, this.value)' /></td>
                <td>${Number(item.price).toLocaleString('vi-VN')}</td>
                <td>${Number(item.qty * item.price).toLocaleString('vi-VN')}</td>
                <td><button onclick='removeItem(${i})'>X</button></td>
            </tr>`;
            total += item.qty * item.price;
            tbody.innerHTML += row;
        });
        document.getElementById("total").textContent = Number(total).toLocaleString('vi-VN');
        updateChange();

        
    }

    // Dùng khi thay đổi số lượng sp trong giỏ
    function updateQty(index, val) {
        cart[index].qty = parseInt(val);
        renderCart();
    }

    // Dùng khi nhấn nút X để xóa
    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    // Tính toán và hiện tiền thối
    function updateChange() {
        let cash = parseInt(document.getElementById("cash").value || 0);
        let total = parseFloat(document.getElementById("total").textContent.replace(/\./g, '').replace(/,/g, ''));
        let change = cash - total;
        document.getElementById("change").textContent = Number(change).toLocaleString('vi-VN');
    }

    // Nhập số tiền người dùng đưa sẽ gọi hàm updateChange
    document.getElementById("cash").addEventListener("input", updateChange);

    // Tìm kiếm khách hàng trên số điện thoại đã nhập
    // onclick="checkCustomer()
    function checkCustomer() {
        let phone = document.getElementById("phone").value;
        const formData = new FormData();
        formData.append("phone", phone);

        fetch('saler/get_customer', { // Gọi đúng action trong controller
            method: 'POST',
            body: formData
        })
        .then(res => res.json()) // Phân tích response thành JSON
        .then(data => {
            document.getElementById("customer_info").style.display = "block";
            if (data) { // Kiểm tra nếu data không phải là null
                document.getElementById("name").value = data.name;
                document.getElementById("address").value = data.address;
                document.getElementById("phone").dataset.makh = data.makh; // Lưu MAKH vào thuộc tính data của input phone
            } else {
                document.getElementById("name").value = "";
                document.getElementById("address").value = "";
                document.getElementById("phone").dataset.makh = ""; // Xóa MAKH nếu không tìm thấy
            }
        });
    }



    // Nút tiến hàng đặt hàng
    $(document).on('click','.proceedToPlaceBtn',function(){

        var name = $('#name').val();
        var address = $('#address').val();
        var cphone = $('#phone').val();
        var payment_mode = $('#payment_mode').val();


        if(payment_mode == ''){
            Swal.fire(
                'Chọn phương thức thanh toán',
                'Vui lòng chọn phương thức thanh toán của khách hàng',
                'warning'
            );
            return false;
        }

        if(cphone == '' || !$.isNumeric(cphone)){
            Swal.fire(
                'Nhập số điện thoại',
                'Vui lòng nhập đúng số điện thoại khách hàng',
                'warning'
            );
            return false;
        }

        // Tạo một form để gửi dữ liệu bằng phương thức POST
        var form = $('<form action="index.php?url=saler/orders" method="post"></form>');

        // Thêm thông tin sản phẩm vào form
        $('<input>').attr({
            type: 'hidden',
            name: 'productItems',
            value: JSON.stringify(cart) // Chuyển đổi mảng cart thành chuỗi JSON
        }).appendTo(form);

        // Thêm thông tin khách hàng vào form
        $('<input>').attr({
            type: 'hidden',
            name: 'cphone',
            value: $('#phone').val()
        }).appendTo(form);

        $('<input>').attr({
            type: 'hidden',
            name: 'name',
            value: $('#name').val()
        }).appendTo(form);

        $('<input>').attr({
            type: 'hidden',
            name: 'address',
            value: $('#address').val()
        }).appendTo(form);

        // Thêm thông tin thanh toán vào form
        $('<input>').attr({
            type: 'hidden',
            name: 'payment_mode',
            value: $('#payment_mode').val()
        }).appendTo(form);

        // Thêm form vào body và submit
        $(document.body).append(form);
        form.submit();
        });
</script>